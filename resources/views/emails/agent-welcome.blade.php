<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>エージェント登録のお知らせ</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4ed; font-family: 'Helvetica Neue', Arial, 'Hiragino Kaku Gothic ProN', 'Hiragino Sans', Meiryo, sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4ed; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    {{-- ヘッダー --}}
                    <tr>
                        <td style="background-color: #332c54; padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 22px; font-weight: bold;">
                                Opinio ATS
                            </h1>
                            <p style="margin: 8px 0 0; color: #65b891; font-size: 14px;">
                                採用管理システム
                            </p>
                        </td>
                    </tr>

                    {{-- 本文 --}}
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 24px; font-size: 15px; color: #333333; line-height: 1.8;">
                                {{ $agent->organization }}<br>
                                {{ $agent->name }} 様
                            </p>

                            <p style="margin: 0 0 24px; font-size: 15px; color: #333333; line-height: 1.8;">
                                いつもお世話になっております。<br>
                                {{ $company->name }}の採用管理システム「Opinio ATS」にエージェントとしてご登録いただきましたことをお知らせいたします。
                            </p>

                            {{-- 登録情報ボックス --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f8f5; border-radius: 8px; margin: 0 0 32px; border-left: 4px solid #4e878c;">
                                <tr>
                                    <td style="padding: 20px 24px;">
                                        <p style="margin: 0 0 4px; font-size: 12px; color: #4e878c; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                                            ご登録情報
                                        </p>
                                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 12px;">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #666666; width: 100px;">会社名</td>
                                                <td style="padding: 6px 0; font-size: 13px; color: #333333; font-weight: bold;">{{ $agent->organization }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #666666;">担当者名</td>
                                                <td style="padding: 6px 0; font-size: 13px; color: #333333; font-weight: bold;">{{ $agent->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; color: #666666;">メール</td>
                                                <td style="padding: 6px 0; font-size: 13px; color: #333333;">{{ $agent->email }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{-- 推薦フォームCTA --}}
                            @if($recommendationUrl)
                            <h2 style="margin: 0 0 16px; font-size: 16px; color: #332c54; border-bottom: 2px solid #65b891; padding-bottom: 8px;">
                                候補者のご推薦について
                            </h2>

                            <p style="margin: 0 0 20px; font-size: 14px; color: #333333; line-height: 1.8;">
                                以下のボタンから、専用の推薦フォームをご利用いただけます。<br>
                                候補者情報と履歴書を送信いただくだけで、推薦が完了します。
                            </p>

                            {{-- CTAボタン --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $recommendationUrl }}" target="_blank" style="display: inline-block; padding: 16px 40px; background-color: #4e878c; color: #ffffff; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; letter-spacing: 0.5px;">
                                            候補者を推薦する
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 8px; font-size: 12px; color: #999999; text-align: center; line-height: 1.6;">
                                ※ このリンクは {{ $agent->organization }} 様専用です。第三者への共有はお控えください。
                            </p>

                            {{-- 推薦に必要な情報 --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 16px 0 24px;">
                                <tr>
                                    <td style="padding: 10px 16px; background-color: #f0f7f4; border-radius: 6px;">
                                        <p style="margin: 0 0 4px; font-size: 12px; color: #4e878c; font-weight: bold;">推薦時にご入力いただく情報：</p>
                                        <p style="margin: 0; font-size: 13px; color: #333333; line-height: 1.8;">
                                            候補者名、連絡先、推薦理由・強み、履歴書（任意）
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @else
                            <h2 style="margin: 0 0 16px; font-size: 16px; color: #332c54; border-bottom: 2px solid #65b891; padding-bottom: 8px;">
                                候補者のご推薦について
                            </h2>

                            <p style="margin: 0 0 16px; font-size: 14px; color: #333333; line-height: 1.8;">
                                今後、候補者をご推薦いただく際は、以下の情報をお送りください。
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 24px;">
                                <tr>
                                    <td style="padding: 10px 16px; background-color: #f0f7f4; border-radius: 6px;">
                                        <p style="margin: 0 0 8px; font-size: 14px; color: #333333; line-height: 1.8;">
                                            <strong style="color: #4e878c;">1.</strong> 候補者のお名前（フルネーム）
                                        </p>
                                        <p style="margin: 0 0 8px; font-size: 14px; color: #333333; line-height: 1.8;">
                                            <strong style="color: #4e878c;">2.</strong> 連絡先（メールアドレス・電話番号）
                                        </p>
                                        <p style="margin: 0 0 8px; font-size: 14px; color: #333333; line-height: 1.8;">
                                            <strong style="color: #4e878c;">3.</strong> 推薦理由・候補者の強み
                                        </p>
                                        <p style="margin: 0; font-size: 14px; color: #333333; line-height: 1.8;">
                                            <strong style="color: #4e878c;">4.</strong> 履歴書・職務経歴書（可能であれば）
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <p style="margin: 0 0 32px; font-size: 14px; color: #333333; line-height: 1.8;">
                                推薦方法やご不明な点がございましたら、お気軽にご連絡ください。<br>
                                今後ともどうぞよろしくお願いいたします。
                            </p>

                            {{-- 署名 --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-top: 1px solid #e5e5e5; padding-top: 24px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 4px; font-size: 14px; color: #332c54; font-weight: bold;">
                                            {{ $company->name }}
                                        </p>
                                        <p style="margin: 0; font-size: 13px; color: #666666; line-height: 1.6;">
                                            採用チーム
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- フッター --}}
                    <tr>
                        <td style="background-color: #f8f8f5; padding: 20px 40px; text-align: center; border-top: 1px solid #e5e5e5;">
                            <p style="margin: 0; font-size: 11px; color: #999999;">
                                このメールは Opinio ATS から自動送信されています。<br>
                                心当たりのない場合は、本メールを破棄してください。
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
