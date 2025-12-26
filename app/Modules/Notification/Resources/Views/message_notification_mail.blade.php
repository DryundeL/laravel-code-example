@extends('layouts.emails.base_notification')

@section('content')
    <tr>
        <td style="background-color: #ffffff; margin: 0; border-radius: 12px; text-align: left;padding: 24px;">
            <table align="left" style="width: 100%;" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td style="text-align: left;">
                        <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;color: #212529; font-weight: 400; font-size: 16px; margin: 0;">
                            У вас новое личное сообщение:
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:24px;">&#160;</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                            <tr>
                                @if($data['from']['photoUrl'] != null)
                                    <td style="width: 46px; vertical-align: middle;">
                                        <img src="{{ $data['from']['photoUrl'] }}"
                                             alt="Аватар"
                                             width="46"
                                             height="46"
                                             style="display: block; border-radius: 50%; border: none;">
                                    </td>
                                @else
                                    <td style="width: 46px; vertical-align: middle;">
                                        <img src="{{ config('inStudy.yandex_s3_url') . 'icons/avatar.png' }}"
                                             alt="Аватар"
                                             width="46"
                                             height="46"
                                             style="display: block; border-radius: 50%; border: none;">
                                    </td>
                                @endif
                                <td style="width: 12px;"></td>
                                <td style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;vertical-align: middle; font-family: Arial, sans-serif; font-size: 16px; line-height: 1.4;">
                                    @if(isset($data['from']['lastName']) && isset($data['from']['firstName']))
                                        {{ trim(($data['from']['lastName'] ?? '') . ' ' . ($data['from']['firstName'] ?? '') . ' ' . ($data['from']['middleName'] ?? '')) }}
                                    @else
                                        {{ $data['from']['fullName'] ?? 'Пользователь' }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:16px;">&#160;</td>
                </tr>
                <tr>
                    <td style="background-color: #F5F5F5; margin: 0; border-radius: 8px; text-align: left;padding: 12px;">
                        <table align="left" style="width: 100%;" cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr>
                                <td>
                                    @if(isset($data['link']))
                                        <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;color:#CF2042; text-decoration: underline; font-weight: 400;font-size: 16px; margin: 0;">
                                            {{ $data['text'] }}
                                        </p>
                                    @else
                                        <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;color: #212529; font-weight: 400; font-size: 16px; margin: 0;">
                                            {{ Str::limit($data['text'], 150) }}
                                        </p>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="line-height:4px;">&#160;</td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;color: #00000073; font-weight: 400; font-size: 14px; margin: 0;">
                                        {{ now()->translatedFormat('d M \в H:i') }}
                                    </p>
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
                            <tr>
                                <td style="background-color: #CF2042; text-align: center; padding:7px 13px;border-radius: 4px;">
                                    @php
                                        $fromExternalId = $data['fromExternalId'] ?? $data['from']['externalId'] ?? $data['from_external_id'] ?? null;
                                        $chatUrl = $fromExternalId
                                            ? config('inStudy.landing_url') . '/chats/' . $fromExternalId
                                            : config('inStudy.landing_url') . '/chats';
                                    @endphp
                                    <a href="{{ $chatUrl }}"
                                       target="_blank" rel="noopener noreferrer"
                                       style="color: #FFFFFF; text-decoration: none; font-family: Arial, sans-serif; font-size: 16px; font-weight: 400;">
                                        Ответить на сообщение
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
@endsection
