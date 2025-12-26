<table class="bordered">
    <tr>
        <td valign="top" colspan="7" style="width:50%">
            <u><b class="pen">{{ $index }}</b></u>-й семестр <u><b class="pen">{{ $item['period'] ?? '' }}</b></u> учебного года

            <span style="margin-left:28%"><u><b class="pen">{{ round($index / 2) }}</b></u> <b> КУРС</b></span>
            <div style="text-align: right;margin-left:10px"><u><b class="pen">{{ $reportCardData['main']['student'] ?? '' }}</b></u></div>
            <div class="note" style="text-align: right">(Фамилия И.О. студента (курсанта))</div>
        </td>
    </tr>
    <tr>
        <td valign="top" colspan="7" style="width:50%" align="center">
            <b>Результаты промежуточной аттестации (экзамены)</b>
        </td>
    </tr>
    <tr>
        <td align="center" style="">
            №<br />п/п
        </td>
        <td align="center" style="">
            Наименование дисциплины<br />(модуля), раздела
        </td>
        <td align="center" style="">
            Общее<br />кол-во<br />час./з. ед.
        </td>
        <td align="center" style="">
            Оценка
        </td>
        <td align="center" style="">
            Дата<br />сдачи<br />экзамена
        </td>
        <td align="center" style="">
            Подпись<br />преподава-<br />теля
        </td>
        <td align="center" style="">
            Фамилия<br />преподавателя
        </td>
    </tr>

    @if(isset($item['left']) && count($item['left']) > 0)
        @foreach($item['left'] as $i => $tmp)
            <tr>
                <td align="center">{{ $i + 1 }}</td>
                <td>{{ $tmp['discipline'] ?? '' }}</td>
                <td align="center">{{ $tmp['zet'] ?? '' }}</td>
                <td align="center">{{ $tmp['grade'] ?? '' }}</td>
                <td align="center">{{ $tmp['date'] ?? '' }}</td>
                <td align="center">
                    <b class="pen">{{ isset($tmp['teacher']) && $tmp['teacher'] ? '*ЭП*' : '' }}</b>
                </td>
                <td>{{ $tmp['teacher'] ?? '' }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    @endif

    <tr>
        <td valign="top" colspan="7" style="width:50%" align="center">
            <b>Результаты промежуточной аттестации (зачеты)</b>
        </td>
    </tr>

    @if(isset($item['right']) && count($item['right']) > 0)
        @foreach($item['right'] as $i => $tmp)
            <tr>
                <td align="center">{{ $i + 1 }}</td>
                <td>{{ $tmp['discipline'] ?? '' }}</td>
                <td align="center">{{ $tmp['zet'] ?? '' }}</td>
                <td align="center">{{ $tmp['grade'] ?? '' }}</td>
                <td align="center">{{ $tmp['date'] ?? '' }}</td>
                <td align="center">
                    <b class="pen">{{ isset($tmp['teacher']) && $tmp['teacher'] ? '*ЭП*' : '' }}</b>
                </td>
                <td>{{ $tmp['teacher'] ?? '' }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    @endif

    @if($index % 2 == 0 && $semester > $index)
        <tr>
            <td colspan="7" style="width: 50%; padding: 5px;border-bottom:none;" valign="top" align="left">
                Студент (курсант) <u><b class="pen">{{ $reportCardData['main']['student'] ?? '' }}</b></u> переведен на <u><b class="pen">{{ round($index / 2) + 1 }}</b></u> курс
            </td>
        </tr>
    @endif

    <tr>
        <td colspan="7" style="width: 50%; padding: 5px;border-top:none;" valign="top" align="right">
            Руководитель структурного подразделения <br>
            <u><b class="pen">{{ $reportCardData['main']['departmentHead'] ?? '' }}</b></u>
            <span class="note">(подпись)</span>
            <u><b class="pen">{{ isset($reportCardData['main']['departmentHead']) && $reportCardData['main']['departmentHead'] ? '*ЭП*' : 'XXX' }}</b></u>
        </td>
    </tr>
</table>
