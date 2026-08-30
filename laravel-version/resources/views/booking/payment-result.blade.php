@php
    $isPaid = $paid ?? false;
    $isCancel = $cancel ?? false;
    $service = $booking && $booking->service ? $booking->service : null;
    $serviceName = $service ? ($service->name_fr ?? $service->name ?? 'Programme') : ($booking->session_label ?? 'Programme');
    $amount = $payment ? $payment->amount : ($booking ? $booking->total_amount : null);
    $currency = strtoupper(($payment ? $payment->currency : null) ?? ($booking->currency ?? 'cad'));
    $icon = $isCancel ? '&#10005;' : ($isPaid || !$booking ? '' : '&#10004;');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isPaid ? 'Réservation confirmée' : ($isCancel ? 'Paiement annulé' : 'Statut de la réservation') }} — Cultulangues</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #F4F6FA; color: #2C3E50; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
        .card { background: #FFFFFF; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 8px 30px rgba(0,0,0,0.06); overflow: hidden; }
        .topbar { background: #FA4E30; color: #fff; padding: 20px 28px; font-size: 20px; font-weight: 800; }
        .body { padding: 30px 28px; }
        .icon { width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; font-size: 28px; font-weight: 800; }
        .icon.ok { background: #E8F7EF; color: #1C7C46; }
        .icon.err { background: #FDECEC; color: #C0392B; }
        .icon.info { background: #F3E8FF; color: #6C2BD9; }
        h1 { font-size: 20px; margin: 0 0 8px; text-align: center; }
        p.lead { color: #7F8C8D; font-size: 14px; line-height: 1.6; text-align: center; margin: 0 0 22px; }
        .summary { background: #FAFAFA; border: 1px solid #E8ECF1; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px; }
        .row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
        .row .k { color: #7F8C8D; }
        .row .v { font-weight: 700; text-align: right; }
        .row.total .v { color: #1C7C46; font-weight: 800; }
        .actions { display: flex; flex-direction: column; gap: 10px; }
        .btn { display: block; width: 100%; text-align: center; padding: 13px 18px; border-radius: 10px; font-size: 14px; font-weight: 700; text-decoration: none; border: none; cursor: pointer; }
        .btn-primary { background: #FA4E30; color: #FFF; }
        .btn-primary:hover { background: #E04320; }
        .btn-ghost { background: #FFF; color: #2C3E50; border: 1px solid #E8ECF1; }
        .btn-ghost:hover { background: #F4F6FA; }
        .note { margin-top: 16px; font-size: 12px; color: #BDC3C7; text-align: center; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <div class="topbar">Cultulangues</div>
        <div class="body">
            @if($isCancel)
                <div class="icon err">&#10005;</div>
                <h1>Paiement annulé</h1>
                <p class="lead">Votre paiement a été annulé. Aucun montant n'a été débité.</p>
            @elseif($isPaid)
                <div class="icon ok">&#10003;</div>
                <h1>Réservation confirmée !</h1>
                <p class="lead">Merci ! Votre paiement a bien été reçu et votre réservation est confirmée.</p>
            @elseif($booking)
                <div class="icon ok">&#10003;</div>
                <h1>En attente de paiement</h1>
                <p class="lead">{{ $message ?? 'Votre paiement est en cours de vérification. Il sera confirmé automatiquement.' }}</p>
            @else
                <div class="icon info">&#63;</div>
                <h1>Session introuvable</h1>
                <p class="lead">{{ $message ?? 'Cette page n\'est pas valide ou la session est déjà consommée.' }}</p>
            @endif

            @if($booking)
                <div class="summary">
                    <div class="row"><span class="k">Programme</span><span class="v">{{ $serviceName }}</span></div>
                    @if($booking->session_label)
                        <div class="row"><span class="k">Session</span><span class="v">{{ $booking->session_label }}</span></div>
                    @endif
                    @if($booking->session_date)
                        <div class="row"><span class="k">Date</span><span class="v">{{ \Carbon\Carbon::parse($booking->session_date)->translatedFormat('d F Y') }}</span></div>
                    @endif
                    <div class="row"><span class="k">Référence</span><span class="v">{{ $booking->booking_ref }}</span></div>
                    @if($amount)
                        <div class="row total"><span class="k">Montant</span><span class="v">{{ number_format((float)$amount, 2, ',', ' ') }} {{ $currency }}</span></div>
                    @endif
                </div>
            @endif

            <div class="actions">
                @if($isPaid)
                    <a class="btn btn-primary" href="{{ route('student.programs') }}">Accéder à mes programmes</a>
                @elseif($isCancel || ($booking && !$isPaid))
                    <a class="btn btn-primary" href="{{ url('/booking?course=' . ($booking->service->slug ?? '')) }}">Réessayer le paiement</a>
                @else
                    <a class="btn btn-ghost" href="{{ route('home') }}">Retour à l'accueil</a>
                @endif
            </div>

            @if($isPaid)
                <p class="note">Un courriel de confirmation vous a été envoyé. Nos équipes vous contacteront sous 24 h pour planifier votre test oral.</p>
            @endif
        </div>
    </div>
</body>
</html>
