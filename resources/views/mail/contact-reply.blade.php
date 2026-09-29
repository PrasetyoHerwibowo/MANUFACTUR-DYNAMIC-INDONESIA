<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $reply->subject }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f5f5f4; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f5f5f4; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                       style="max-width:600px; background-color:#ffffff; border-radius:14px; overflow:hidden; border:1px solid #e7e5e4;">

                    <tr>
                        <td style="background-color:#b91c1c; padding:20px 28px;">
                            <p style="margin:0; color:#ffffff; font-size:16px; font-weight:700; line-height:1.4;">
                                {{ $companyName }}
                            </p>
                            @if ($companyTagline)
                                <p style="margin:4px 0 0 0; color:#fecaca; font-size:12px; line-height:1.4;">
                                    {{ $companyTagline }}
                                </p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px;">
                            <div style="color:#44403c; font-size:14px; line-height:1.75; white-space:pre-wrap;">{{ $body }}</div>

                            <p style="margin:24px 0 0 0; padding-top:16px; border-top:1px solid #e7e5e4; color:#a8a29e; font-size:11px; line-height:1.6;">
                                Email ini dikirim otomatis oleh {{ $companyName }}.
                                Mohon tidak membalas langsung dari email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
