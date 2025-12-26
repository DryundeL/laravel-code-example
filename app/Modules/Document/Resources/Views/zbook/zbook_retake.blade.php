<table class="bordered">
    <tr>
        <td colspan="5" style="width:50%">
            <div style="text-align:right"><u><b class="pen">{{$reportCardData['main']['student']}}</b></u></div>
            <div class="note" style="text-align:right">(Фамилия И.О. студента (курсанта))</div>
        </td>
    </tr>
    <tr>
        <td colspan="5" style="width:50%;text-align: center">
            <b>Перезачеты (экзамены)</b>
        </td>
    </tr>
    <tr>
        <td style="padding:0;text-align: center">
            №<br />п/п
        </td>
        <td style="padding:0;text-align: center">
            Наименование дисциплины<br />(модуля), раздела
        </td>
        <td style="padding:0;text-align: center">
            Общее<br />кол-во<br />час./з. ед.
        </td>
        <td style="padding:0;text-align: center">
            Оценка
        </td>
        <td style="padding:0;text-align: center">
            Протокол, дата
        </td>
    </tr>
    @if(isset($retake['left']) && is_array($retake['left']))
        @foreach($retake['left'] as $item)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td>{{ $item['discipline'] ?? '' }}</td>
                <td style="text-align: center;">{{ $item['zet'] ?? '' }}</td>
                <td style="text-align: center">{{ $item['grade'] ?? '' }}</td>
                <td>{{ $item['protocol'] ?? '' }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endif
    <tr>
        <td colspan="5" style="width:50%;text-align: center">
            <b>Перезачеты (зачеты)</b>
        </td>
    </tr>
    @if(isset($retake['right']) && is_array($retake['right']))
        @foreach($retake['right'] as $item)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td>{{ $item['discipline'] ?? '' }}</td>
                <td style="text-align: center;">{{ $item['zet'] ?? '' }}</td>
                <td style="text-align: center">{{ $item['grade'] ?? '' }}</td>
                <td>{{ $item['protocol'] ?? '' }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endif
    <tr>
        <td colspan="6" style="width: 50%; padding: 5px;text-align: right">
            Руководитель структурного подразделения <br> <u><b class="pen">{{$reportCardData['main']['departmentHead']}}</b></u>
            <span class="note">(подпись)</span> <u><b class="pen">{{$reportCardData['main']['departmentHead'] ? '*ЭП*' : ''}}</b></u>
        </td>
    </tr>
</table>


