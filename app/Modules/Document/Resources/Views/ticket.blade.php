<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Ошибка системы</title>
    <style>
        @charset "UTF-8";
        * {
            font-family: 'DejaVu Serif', serif;
        }
        td {
            font-size: 11px;
            font-family: 'DejaVu Serif', serif;
        }
        img {
            max-width: 200px;
            max-height: 200px;
            width: auto;
            height: auto;
            margin: 0 10px;
        }
    </style>
</head>
<body>
<table style="width: 100%;border: 1px solid;border-collapse: collapse;">
    <tr>
        <td style="width: 200px; text-align: center;vertical-align: top;border: 1px solid;">
            <b>Извещение</b><br><br><br><br><br><br><br><br><br><br><br><br><b>Кассир</b></td>
        <td style="border-top: 1px solid;border-right: 1px solid;border-bottom: 1px solid;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;text-align: left;"><b>СБЕРБАНК РОССИИ</b><br><span
                            style="font-size: 8px"></span></td>
                    <td style="font-size: 10px;text-align: right;width: 50%;"><i>Форма № ПД-4</i></td>
                </tr>
                <tr>
                    <td style="font-size: 8px;padding-top: -5px;">Основан в 1841 году</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;border-bottom: 1px solid;">
                        <b>НОЧУ ВО «Московский институт психоанализа»</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 8px;text-align: center;padding-top: -2px;">(наименование
                        получателя платежа)</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;border-bottom: 1px solid;"><b>7713131464</b></td>
                    <td style="width: 20%;"></td>
                    <td style="text-align: center;border-bottom: 1px solid;">
                        <b>40703810238100100649</b></td>
                </tr>
                <tr>
                    <td style="font-size: 8px;text-align: center;padding-top: -2px;">(ИНН получателя платежа)</td>
                    <td style="width: 20%;"></td>
                    <td style="font-size: 8px;text-align: center;padding-top: -2px;">(номер счета получателя платежа)
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td><b><u>в Сбербанке России ПАО г. Москва</u></b></td>
                    <td></td>
                    <td><b>БИК 044525225</b></td>
                </tr>
                <tr>
                    <td>Номер кор./сч. получателя платежа</td>
                    <td></td>
                    <td><b>30101810400000000225</b></td>
                </tr>
            </table>
            <br>
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: left;border-bottom: 1px solid;width: 100%;line-height: 10px;">
                        {{ $purpose }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-size: 8px;text-align: center;padding-top: -2px;width: 100%;">(наименование платежа)
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <br>
            <table style="width: 100%;">
                <tr>
                    <td>ФИО плательщика</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 75%;">{{ $fio }}</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>Адрес плательщика</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 72%;">
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 16%;">Сумма платежа</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 8%;"></td>
                    <td style="width: 35%;">руб 00 коп. Сумма оплаты за услуги</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 6%;"></td>
                    <td style="width: 4%;">руб.</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 4%;"></td>
                    <td style="width: 3%;">коп.</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>Итого</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 25%;"></td>
                    <td>руб.</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 5%;"></td>
                    <td>коп.</td>
                    <td style="width: 10%;"></td>
                    <td>«</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 5%;"></td>
                    <td>»</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 20%;"></td>
                    <td>20</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 5%;"></td>
                    <td>г.</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="font-size: 10px;">С условиями приема указанной в платежном документе суммы, в т.ч. с
                        суммой взимаемой платы за услуги банка, ознакомлен и согласен.</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="font-size: 10px;text-align: center;"><b>Подпись плательщика</b></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td
            style="width: 200px; text-align: center;vertical-align: top;padding-top: 30px;border: 1px solid;border-top: unset;">
            <br><br><? if (!empty($qr)): ?>
            <img src="{{ $qr }}" alt="">
            <? endif ?><br><br><br><br><br><br><b>Квитанция<br>Кассир</b>
        </td>
        <td style="border-top: 1px solid;border-right: 1px solid;border-bottom: 1px solid;">
            <table style="width: 100%;">
                <tr>
                    <td colspan="2" style="text-align: center;border-bottom: 1px solid;padding-top: 15px;">
                        <b>НОЧУ ВО «Московский институт психоанализа»</b></td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 8px;text-align: center;padding-top: -2px;">(наименование
                        получателя платежа)</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: center;border-bottom: 1px solid;"><b>7713131464</b></td>
                    <td style="width: 20%;"></td>
                    <td style="text-align: center;border-bottom: 1px solid;">
                        <b>40703810238100100649</b></td>
                </tr>
                <tr>
                    <td style="font-size: 8px;text-align: center;padding-top: -2px;">(ИНН получателя платежа)</td>
                    <td style="width: 20%;"></td>
                    <td style="font-size: 8px;text-align: center;padding-top: -2px;">(номер счета получателя платежа)
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td><b><u>в Сбербанке России ПАО г. Москва</u></b></td>
                    <td></td>
                    <td><b>БИК 044525225</b></td>
                </tr>
                <tr>
                    <td>Номер кор./сч. получателя платежа</td>
                    <td></td>
                    <td><b>30101810400000000225</b></td>
                </tr>
            </table>
            <br>
            <table style="width: 100%;">
                <tr>
                    <td style="text-align: left;border-bottom: 1px solid;width: 100%;line-height: 10px;"> {{ $purpose }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-size: 8px;text-align: center;padding-top: -2px;width: 100%;">(наименование платежа)
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <br>
            <table style="width: 100%;">
                <tr>
                    <td>ФИО плательщика</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 77%;">{{ $fio }}</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>Адрес плательщика</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 76%;">
                    </td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 15%;">Сумма платежа</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 8%;"></td>
                    <td style="width: 25%;">руб 00 коп. Сумма оплаты за услуги</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 6%;"></td>
                    <td style="width: 5%;">руб.</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 4%;"></td>
                    <td style="width: 5%;">коп.</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td>Итого</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 25%;"></td>
                    <td>руб.</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 5%;"></td>
                    <td>коп.</td>
                    <td style="width: 10%;"></td>
                    <td>«</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 5%;"></td>
                    <td>»</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 20%;"></td>
                    <td>20</td>
                    <td style="text-align: center;border-bottom: 1px solid;width: 5%;"></td>
                    <td>г.</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="font-size: 10px;">С условиями приема указанной в платежном документе суммы, в т.ч. с
                        суммой взимаемой платы за услуги банка, ознакомлен и согласен.</td>
                </tr>
            </table>
            <table style="width: 100%;">
                <tr>
                    <td style="font-size: 10px;text-align: center;"><b>Подпись плательщика</b></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
