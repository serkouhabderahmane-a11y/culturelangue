@php
    $service = $booking->service;
    $serviceName = $service ? ($service->name_fr ?? $service->name ?? 'Programme') : (string) ($booking->session_label ?? 'Programme');
    $amount = $payment ? $payment->amount : $booking->total_amount;
    $currency = strtoupper($payment->currency ?? $booking->currency ?? 'CAD') . ' ';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Confirmation de réservation — Cultulangues</title>
</head>
<body style="margin:0;padding:0;background:#F4F6FA;font-family:Arial,Helvetica,sans-serif;color:#2C3E50;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F6FA;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#FFFFFF;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 32px;background:#FA4E30;color:#FFFFFF;font-size:22px;font-weight:800;">
                            Cultulangues
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <div style="width:56px;height:56px;border-radius:50%;background:#E8F7EF;color:#1C7C46;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;margin-bottom:16px;">✓</div>
                            <h1 style="margin:0 0 8px;font-size:20px;color:#2C3E50;">Réservation confirmée</h1>
                            <p style="margin:0 0 20px;color:#7F8C8D;font-size:14px;line-height:1.6;">
                                Bonjour <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong>,<br>
                                Merci ! Votre réservation et votre paiement ont bien été enregistrés.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FAFAFA;border:1px solid #E8ECF1;border-radius:10px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">
                                            @if($serviceName)
                                            <tr>
                                                <td style="padding:4px 0;color:#7F8C8D;">Programme</td>
                                                <td style="padding:4px 0;text-align:right;font-weight:700;">{{ $serviceName }}</td>
                                            </tr>
                                            @endif
                                            @if($booking->session_label)
                                            <tr>
                                                <td style="padding:4px 0;color:#7F8C8D;">Session</td>
                                                <td style="padding:4px 0;text-align:right;font-weight:700;">{{ $booking->session_label }}</td>
                                            </tr>
                                            @endif
                                            @if($booking->session_date)
                                            <tr>
                                                <td style="padding:4px 0;color:#7F8C8D;">Date</td>
                                                <td style="padding:4px 0;text-align:right;font-weight:700;">{{ \Carbon\Carbon::parse($booking->session_date)->translatedFormat('d F Y') }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding:4px 0;color:#7F8C8D;">Référence</td>
                                                <td style="padding:4px 0;text-align:right;font-weight:700;">{{ $booking->booking_ref }}</td>
                                            </tr>
                                            @if($amount)
                                            <tr>
                                                <td style="padding:4px 0;color:#7F8C8D;">Montant payé</td>
                                                <td style="padding:4px 0;text-align:right;font-weight:800;color:#1C7C46;">{{ number_format((float)$amount, 2, ',', ' ') }} {{ $currency }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px 0 0;font-size:13px;color:#7F8C8D;line-height:1.6;">
                                Nos équipes vous contacteront sous 24 h pour planifier votre test oral et votre démarrage.
                                Retrouvez vos détails dans votre espace étudiant.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;background:#F4F6FA;font-size:12px;color:#BDC3C7;text-align:center;border-top:1px solid #E8ECF1;">
                            © {{ date('Y') }} Cultulangues — Tous droits réservés
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
