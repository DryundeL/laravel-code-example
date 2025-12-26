<?php

namespace App\Modules\Document\Traits;

use App\Modules\Document\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

trait ProcessDocument
{
    private array $statusTypes = [
        'success' => ['Выполнено', 'Подписано', 'Оплачено', 'Согласовано', 'Подписано вузом'],
        'default' => ['Подано', 'На рассмотрении', 'Заявка подана', 'Повторное согласование'],
        'warning' => ['Требует подписи', 'Ожидает оплаты', 'Требует согласования', 'Отправлено на подпись', 'Формирование заявления'],
        'error' => ['Отказано в подписи', 'Отклонено', 'Заявка аннулирована']
    ];

    private function processStatus(string $statusName): array
    {
        $statusType = 'default';

        foreach ($this->statusTypes as $type => $statusesList) {
            if (in_array($statusName, $statusesList, true)) {
                $statusType = $type;
                break;
            }
        }

        return [
            'name' => $statusName,
            'type' => $statusType,
        ];
    }

    private function formatDate(string $datetime, string $format = 'd.m.Y H:i:s'): string
    {
        return Carbon::parse($datetime)->format($format);
    }

    public function processRequest(array &$request): array
    {
        if (isset($request['type'])) {
            $documentType = Document::where('type', $request['type'])->first();
            if ($documentType) {
                $request['title'] = $documentType->name;
            }
        }

        $statusName = $request['statusName'] ?? 'Неизвестный статус';
        $request['status'] = $this->processStatus($statusName);

        if (isset($request['datetime'])) {
            $request['date'] = $this->formatDate($request['datetime']);
        }

        if (isset($request['formData'])) {
            if ($request['formData'] === '') {
                $request['formData'] = null;
            } else {
                $data = json_decode($request['formData'], true);

                $dateFields = ['dateDismiss', 'datefrom', 'dateto'];
                foreach ($dateFields as $field) {
                    if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                        $data[$field] = $this->formatDate($data[$field], 'd.m.Y');
                    }
                }

                $request['formData'] = $data;
            }
        }

        $request['has_reject'] = ($request['type'] ?? null) === 'iup' && $statusName === 'Отправлено на подпись';

        if (isset($request['fileEp'])) {
            $request['file'] = $request['fileEp'];
        }

        unset(
            $request['type'],
            $request['statusName'],
            $request['datetime'],
            $request['fileEp'],
        );

        return $request;
    }

    public function processDocuments(array &$documents): array
    {
        array_walk($documents, function (&$item) {
            if (isset($item['date'])) {
                $item['date'] = $this->formatDate($item['date']);
            }

            $statusName = $item['status'] ?? '';
            $item['status'] = $statusName !== ''
                ? $this->processStatus($statusName)
                : $this->processStatus('Неизвестный статус');
        });

        return $documents;
    }

    public function processDocument(array &$document): array
    {
        if (isset($document['date'])) {
            $document['date'] = $this->formatDate($document['date']);
        }

        $statusName = $document['status'] ?? '';
        $document['status'] = $statusName !== ''
            ? $this->processStatus($statusName)
            : $this->processStatus('Неизвестный статус');

       return $document;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function processSignNew(array $response): array
    {
        Cache::set('profile_' . Auth::user()->id . '_contacts', $response['contacts']['contactInfo']);
        return [
            'document' => $response['file']['data'],
        ];
    }

    public function processDiscount(array $response): array
    {
        $profile = Auth::user();
        Cache::remember('discount_' . $profile->external_id, now()->addHour(), function () use ($response) {
            return $response;
        });

        return [
            'availableSemesters' => $response['semesters']
        ];
    }

    public function processSignDocument(array $response): array
    {
        if (isset($response['data'])) {
            $response['document'] = $response['data'];
        } else if (isset($response['binaryData'])) {
            $response['document'] = $response['binaryData'];
        } else {
            return $this->getErrorMessage('document', 'Файл документа не найден');
        }

        return $response;
    }

    public function getZbookRetake(mixed $retake): array
    {
        if (!is_array($retake)) {
            return [];
        }
        $items = [];
        $ctype = ['Зачет', 'Экзамен'];
        /* Установка формы контроля */
        for ($i = 0; $i < count($retake); $i++) {
            $retake[$i]['controlType'] = $ctype[mt_rand(0, 1)];
        }
        /* Установка формы контроля */
        for ($i = 0; $i < count($retake); $i++) {
            $item = $retake[$i];
            $side = trim($item['controlType']) == 'Экзамен' ? 'left' : 'right';
            $discipline = preg_replace('/\,\s\d+$/', '', $item['subject']);
            $zet = $item['hoursTotal'] . '/' . $item['zetTotal'];
            $protocol = '№' . $item['protocolNumber'] . ' от ' . date('d.m.Y', strtotime($item['protocolDate']));

            if (is_numeric($item['mark'])) {
                $item['mark'] = $item['controlType'] == 'Зачет'
                    ? $this->point2GradeZ($item['mark'])
                    : $this->point2Grade($item['mark']);
            }

            $items[$side][] = [
                'discipline' => $discipline,
                'zet' => $zet,
                'grade' => $item['mark'],
                'protocol' => $protocol
            ];
        }

        return $items;
    }

    public function getZbookCertification($c): array
    {
        $shortName = function (?string $name): string {
            if (!$name)
                return '';
            $name = explode(' ', $name);
            $name = $name[0] . ' ' . mb_substr($name[1], 0, 1, 'UTF-8') . '.' . mb_substr($name[2], 0, 1, 'UTF-8') . '.';
            $name = trim(str_replace('.', '', $name)) ? $name : '';
            return $name;
        };

        $items = [];
        for ($i = 0; $i < count($c); $i++) {
            for ($x = 0; $x < count($c[$i]['data']); $x++) {
                $item = $c[$i]['data'][$x];
                $period = $item['initialAcademicYear'] . '/' . $item['finalAcademicYear'];
                $side = trim($item['controlType']) == 'Экзамен' ? 'left' : 'right';
                $discipline = preg_replace('/\,\s\d+$/', '', $item['subject']);
                $zet = $item['hoursTotal'] . '/' . $item['zetTotal'];
                $date = date('d.m.Y', strtotime($item['examDate']));
                $teacher = $shortName($item['gEK'][0]['teacher'] ?? '');

                $items[$item['semester']][$side][] = [
                    'discipline' => $discipline,
                    'zet' => $zet,
                    'grade' => $item['fivePointMark'],
                    'teacher' => $teacher,
                    'date' => $date
                ];

                $items[$item['semester']]['period'] = $period;
            }
        }

        return $items;
    }

    private function point2GradeZ(int $point): string
    {
        $point = $point > 10 ? 10 : $point;

        $dict = [
            10 => 'Зачтено',
            9 => 'Зачтено',
            8 => 'Зачтено',
            7 => 'Зачтено',
            6 => 'Зачтено',
            5 => 'Зачтено',
            4 => 'Зачтено',
            3 => 'Зачтено',
            2 => 'Не зачтено',
            1 => 'Не зачтено',
            0 => 'Не явился'
        ];

        return $dict[$point];
    }

    private function point2Grade(int $point): string
    {
        $point = $point > 10 ? 10 : $point;

        $dict = [
            10 => 'Отлично',
            9 => 'Отлично',
            8 => 'Хорошо',
            7 => 'Хорошо',
            6 => 'Хорошо',
            5 => 'Удовлетворительно',
            4 => 'Удовлетворительно',
            3 => 'Удовлетворительно',
            2 => 'Неудовлетворительно',
            1 => 'Неудовлетворительно',
            0 => 'Не явился'
        ];

        return $dict[$point];
    }
}
