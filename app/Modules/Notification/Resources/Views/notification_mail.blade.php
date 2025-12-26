@extends('layouts.emails.base_notification')

@section('content')
    <tr>
        <td style="background-color: #ffffff; margin: 0; border-radius: 12px; text-align: left;padding: 24px;">
            <table align="left" style="width: 100%;" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="text-align: left;">
                        <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;color: #212529; font-weight: 400; font-size: 16px; margin: 0;line-height: 1.5;">
                            У вас новое уведомление:
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:24px;">&#160;</td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tbody>
                            <tr>
                                <td style="width: 46px; vertical-align: top;">
                                    <img src="{{ $data['photoUrl'] }}"
                                         alt="Аватар"
                                         width="46"
                                         height="46"
                                         style="display: block; border-radius: 50%; border: none;">
                                </td>
                                <td style="width: 12px;">&#160;</td>
                                <td>
                                    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                        <tbody>
                                        <tr>
                                            <td style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;vertical-align: middle; font-family: Arial, sans-serif; font-size: 16px;color:#212529;line-height: 1.5;">
                                                {{ $data['title'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;vertical-align: middle; font-family: Arial, sans-serif; font-size: 16px;color:#6C757D;line-height: 1.5;">
                                                {{ $data['text'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;vertical-align: middle; font-family: Arial, sans-serif; font-size: 14px;color:#00000073;line-height: 1.5;">
                                                {{ now()->translatedFormat('d M \в H:i') }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:24px;">&#160;</td>
                </tr>
                <tr>
                    <td style="text-align: left;">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tbody>
                            {{--                                                <tr>--}}
                            {{--                                                    <td style="background-color: #CF2042; text-align: center; padding:7px 13px;border-radius: 4px;">--}}
                            {{--                                                        <a href="https://example.com" target="_blank" rel="noopener noreferrer"--}}
                            {{--                                                           style="color: #FFFFFF; text-decoration: none; font-family: Arial, sans-serif; font-size: 16px; font-weight: 400;line-height: 1.5;">--}}
                            {{--                                                            Ответить на сообщение--}}
                            {{--                                                        </a>--}}
                            {{--                                                    </td>--}}
                            {{--                                                    <td style="width: 8px;">&#160;</td>--}}
                            {{--                                                    <td style="background-color: #F8F9FA; text-align: center; padding:7px 13px;border-radius: 4px;">--}}
                            {{--                                                        <a href="https://example.com" target="_blank" rel="noopener noreferrer"--}}
                            {{--                                                           style="color: #000000; text-decoration: none; font-family: Arial, sans-serif; font-size: 16px; font-weight: 400;line-height: 1.5;">--}}
                            {{--                                                            Перейти к обучению--}}
                            {{--                                                        </a>--}}
                            {{--                                                    </td>--}}
                            {{--                                                </tr>--}}
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
@endsection
