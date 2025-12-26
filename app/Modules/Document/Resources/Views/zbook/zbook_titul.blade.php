<div class="title page">ЗАЧЁТНАЯ КНИЖКА</div>
<table class="page" style="width: 100%;margin-top: 10px">
    <tr>
        <td style="width: 35%; vertical-align: top">
            <p style="height:100px;margin-left: 15px;margin-bottom: 0"><img src="{{$imgUrl . 'zbook/logo.mip.png'}}" width="200" alt="" /></p>
            <table>
                <tr>
                    <td style="vertical-align: top">
                        <div style="position: relative;">
                                <div style="margin-left: 5px">
                                    @if(empty($studentPhoto))
                                        <img width="100" src="{{$imgUrl . 'zbook/no_phont.jpg'}}" alt="" />
                                    @else
                                        <img width="100" style="border: 1px solid #ccc;" src="data:image/png;base64, {{ $studentPhoto }}" alt="" />
                                    @endif
                                </div>
                                <img style="transform: translate(-60px, 120px) rotate(-45deg);width: 100px;position: absolute;display: block;right: -100px;top:-50px;" src="{{$imgUrl . 'zbook/tpl.png'}}" alt="" />
                                <span style="margin-left:80px">М.П.</span>
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                            <div style="width:170px;border:1px dotted #777;float:right;border-radius:7px;padding:5px 5px">
                                <div style="width:25px;height:25px;float:left;">
                                    <img src="{{$imgUrl . 'zbook/logo.mip_sign.png'}}" width="25" alt="" />
                                </div>
                                <div style="font-size:8px;font-weight:bold;margin-left: 30px;line-height: 1em;">
                                    ДОКУМЕНТ ПОДПИСАН<br/>ЭЛЕКТРОННОЙ ПОДПИСЬЮ
                                </div>
                                <div style="width:100%;font-size:9px;margin-top: 5px;line-height: 1em">
                                    <b>Сертификат</b>: {{ substr(md5($userId), 0, 25) }}</b><br/>
                                    <b>Владелец</b>: НОЧУ ВО «Московский институт психоанализа»</b>
                                </div>
                            </div>
                    </td>
                </tr>
            </table>

            <div style="text-align: right;margin-top: 30px">
                <span >Подпись студента (курсанта) <u><b class="pen">*ЭП*</b></u></span>
                <br/><br/>
                <u><b class="pen">{{ date('d.m.Y', strtotime($reportCardData['main']['reportСardIssueDate'])) }}</b></u> года
				<br />
				<div class="note">(дата выдачи зачетной книжки)</div>
			</div>

        </td>
        <td style="padding-left: 20px;padding-right: 5px">
            <center>
                <u><b class="pen">ООО «Современное образование 3»</b></u>
                <br/>
                <div class="note">(учредитель)</div>

                <u><b class="pen">Негосударственное образовательное частное учреждение высшего образования «Московский институт психоанализа»</b></u>
                <br/>

                <div class="note">(полное наименование организации, осуществляющей образовательную деятельность)</div>
                <br/>

                <b>ЗАЧЕТНАЯ КНИЖКА №</b> <u><b class="pen">{{ $reportCardData['main']['reportСardNumber'] }}</b></u>
                <br/><br/>

                <u><b class="pen">{{ $reportCardData['main']['student'] }}</b></u>
                <br/>

                <div class="note">(фамилия, имя, отчество (последнее - при наличии) студента (курсанта))</div>
            </center>
            Код, направление подготовки (специальность) <u><b class="pen">{{$reportCardData['main']['specialization']}}</b></u>
            <br/>

            Структурное подразделение (факультет) <u><b class="pen">{{$reportCardData['main']['department']}}</b></u>
            <br/>

            Зачислен приказом <u><b class="pen">{{$reportCardData['main']['orderEnrollmentNumber']}}</b></u>
            <br/><br/>

            Руководитель организации, осуществляющей образовательную деятельность, или иное уполномоченное им должностное лицо
            <br/>

            <div style="text-align: right">
				<u><b class="pen">*ЭП*</b></u> М.П.
				<u><b class="pen">Л.И. Сурат</b></u>
                <div class="note" style="text-align: right">(подпись, фамилия, имя, отчество (последнее - при наличии))</div>
                <img style="transform: translate(-80px, -40px);position: absolute; right: -30px;margin-top:-30px" src="{{$imgUrl . 'zbook/tpl.png'}}" alt="" width="100"  />
			</div>


            <br />Руководитель структурного подразделения
            <br/>
            <div style="text-align: right">
                <u><b class="pen">{{ $reportCardData['main']['departmentHead'] ? '*ЭП*' : '' }}</b></u>
				<u><b class="pen">{{$reportCardData['main']['departmentHead']}}</b></u>
			</div>
            <div class="note" style="text-align: right">(подпись, фамилия, имя, отчество (последнее - при наличии))</div>

        </td>
    </tr>
</table>
