<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangladesh Police HQ - Citizen Portal</title>
    <style>
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:#f4f7f6;color:#243444;font-family:Arial,sans-serif}
        .hero{min-height:318px;background:#2d4358;padding:96px 20px 70px;color:#fff;text-align:center}.hero h1{margin:0;font-size:clamp(34px,4vw,48px)}.hero p{margin:18px auto 0;max-width:760px;color:#c7d0d8;font-size:18px;line-height:1.6}
        .container{width:min(1240px,calc(100% - 40px));margin:0 auto}.help{padding:50px 0;text-align:center}.help h2{margin:0;color:#1f3040;font-size:30px}
        .card-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px;margin-top:44px}.card{min-height:282px;border:1px solid #e5ebef;border-radius:11px;background:#fff;padding:36px 30px;box-shadow:0 4px 12px rgba(31,48,64,.09);display:flex;flex-direction:column;align-items:center}.card h3{margin:0;color:#2980b9;font-size:24px}.card p{max-width:290px;margin:24px auto 0;color:#273746;font-size:17px;line-height:1.35}.card-link{margin-top:auto;border:1px solid #bae6fd;border-radius:7px;background:#f0f9ff;padding:13px 22px;color:#0369a1;font-size:16px;font-weight:700;text-decoration:none}
        .info-section{background:#fff;border-top:1px solid #e1e8ed;padding:48px 0}.info-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:40px}.info-grid h3{color:#1f3040}.info-grid p{color:#657581;line-height:1.7}
        @media(max-width:900px){.card-grid{grid-template-columns:1fr}}@media(max-width:680px){.info-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <x-citizen-portal-nav />
    <header class="hero"><h1>Welcome to the Central Police HQ System</h1><p>A transparent, secure, and public-facing portal for citizens and law enforcement.</p></header>
    <main>
        <section class="help container">
            <h2>How can we help you today?</h2>
            <div class="card-grid">
                <article class="card"><h3>Wanted Criminals</h3><p>Browse the public wanted-person registry by name or known alias.</p><a href="{{route('wanted-criminals.index')}}" class="card-link">View Wanted List</a></article>
                <article class="card"><h3>Find a Station</h3><p>Navigate the police station directory and locate the thana responsible for your area.</p><a href="{{route('stations.index')}}" class="card-link">Find Stations</a></article>
                <article class="card"><h3>File a Complaint</h3><p>Registered citizens can submit a preliminary complaint directly to an active police thana.</p><a href="{{route('citizen.complaints.create')}}" class="card-link">Submit Complaint</a></article>
            </div>
        </section>
        <section class="info-section"><div class="container info-grid"><article id="about"><h3>About the portal</h3><p>The portal connects public station information, citizen complaint submission and role-based police operations through one central database.</p></article><article id="contact"><h3>Need assistance?</h3><p>Use the station directory to locate the responsible thana and view its public contact information.</p></article></div></section>
    </main>
</body>
</html>
