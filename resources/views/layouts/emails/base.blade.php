<!DOCTYPE HTML PUBLIC «-//W3C//DTD HTML 4.0 Transitional//EN»>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body style="font-size:0;margin: 0;padding: 0;background-color:#F8F9FA;" align="center">
<table align="center" style="width: 100%; max-width: 552px; ">
    <tbody>
    <tr>
        <td style="line-height:24px;">&#160;</td>
    </tr>
    <tr>
        <td>
            <table align="center" style="width: 100%;" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="line-height:24px;">&#160;</td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <img src="{{ config('inStudy.yandex_s3_url') . 'icons/instudy_logo.png' }}"
                             alt="InStudy 2.0"
                             style="width: 132px; height: 22px;">
                    </td>
                </tr>
                <tr>
                    <td style="line-height:32px;">&#160;</td>
                </tr>
                @yield('content')
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

