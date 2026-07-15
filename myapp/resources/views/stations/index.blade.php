<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Police Jurisdictions | Bangladesh Police HQ</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7f6; color: #2c3e50; }
        nav { background-color: #1a252f; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; gap: 20px; }
        .nav-brand { color: #f1c40f; font-size: 20px; font-weight: bold; text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .nav-links a { color: white; text-decoration: none; font-weight: bold; font-size: 14px; }
        .nav-links a:hover { color: #3498db; }
        .btn-auth { background-color: #3498db; padding: 8px 15px; border-radius: 4px; color: white !important; }
        .hero { background: linear-gradient(135deg, #1a252f, #2c3e50); color: white; padding: 56px 20px; }
        .hero-inner, .container { max-width: 1120px; margin: 0 auto; }
        .hero h1 { margin: 0; font-size: 34px; }
        .hero p { margin: 12px 0 0; color: #bdc3c7; font-size: 16px; max-width: 760px; }
        .container { padding: 32px 20px 56px; }
        .toolbar { background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(26,37,47,.08); padding: 18px; margin-top: -54px; position: relative; }
        .toolbar form { display: grid; grid-template-columns: 1fr auto; gap: 12px; }
        input { width: 100%; border: 1px solid #d8e0e6; border-radius: 8px; padding: 12px 14px; font-size: 14px; }
        button { border: 0; border-radius: 8px; padding: 0 22px; font-weight: bold; background: #2c3e50; color: white; cursor: pointer; }
        button:hover { background: #1a252f; }
        .split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px; margin-top: 28px; }
        .browser { background: white; border-radius: 14px; box-shadow: 0 8px 24px rgba(26,37,47,.08); overflow: hidden; }
        .browser-head { padding: 20px 22px; border-bottom: 1px solid #edf1f4; display: flex; justify-content: space-between; gap: 14px; align-items: center; }
        .browser-head h2 { margin: 0; color: #1a252f; font-size: 20px; }
        .browser-head p { margin: 6px 0 0; color: #7f8c8d; font-size: 13px; }
        .count { background: #eef5fb; color: #2980b9; border-radius: 999px; padding: 7px 11px; font-size: 12px; font-weight: bold; white-space: nowrap; }
        .viewport { position: relative; min-height: 365px; padding: 22px; }
        .hq-card { display: none; min-height: 300px; border: 1px solid #edf1f4; border-radius: 14px; background: linear-gradient(145deg, #ffffff, #f8fafb); padding: 24px; flex-direction: column; }
        .hq-card.is-active { display: flex; }
        .eyebrow { color: #3498db; font-size: 11px; letter-spacing: .16em; text-transform: uppercase; font-weight: bold; margin: 0 0 10px; }
        .hq-card h3 { margin: 0; font-size: 24px; color: #1a252f; line-height: 1.2; }
        .meta { margin: 14px 0 0; color: #5d6d7e; line-height: 1.5; font-size: 14px; }
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 20px 0; }
        .stat { border: 1px solid #edf1f4; border-radius: 10px; padding: 12px; background: white; }
        .stat strong { display: block; color: #1a252f; font-size: 22px; }
        .stat span { color: #7f8c8d; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
        .card-footer { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .profile-link { color: white; background: #3498db; border-radius: 8px; padding: 11px 14px; text-decoration: none; font-weight: bold; font-size: 13px; }
        .slide-controls { display: flex; align-items: center; gap: 8px; }
        .slide-controls button { height: 38px; width: 38px; padding: 0; border-radius: 999px; background: #eef2f5; color: #1a252f; }
        .slide-controls button:hover { background: #dce6ed; }
        .position { color: #7f8c8d; font-size: 12px; font-weight: bold; min-width: 46px; text-align: center; }
        .empty { background: #f8fafb; border: 1px dashed #ccd6dd; border-radius: 12px; padding: 42px 22px; text-align: center; color: #7f8c8d; }
        @media (max-width: 900px) { .split { grid-template-columns: 1fr; } .toolbar form { grid-template-columns: 1fr; } button { padding: 12px 18px; } }
        @media (max-width: 640px) { nav { align-items: flex-start; flex-direction: column; } .browser-head, .card-footer { align-items: flex-start; flex-direction: column; } }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">BD Police HQ Portal</a>
        <div class="nav-links">
            <a href="/">Home</a>
            <a href="/stations">Stations</a>
            @guest
                <a href="/login" class="btn-auth">Login</a>
                <a href="/register" class="btn-auth">Register</a>
            @endguest
            @auth
                @if(auth()->user()->role === 'super_admin')
                    <a href="/admin/dashboard" class="btn-auth">HQ Dashboard</a>
                @elseif(auth()->user()->role === 'station_oc')
                    <a href="/oc/dashboard" class="btn-auth">OC Dashboard</a>
                @elseif(in_array(auth()->user()->role, ['metro_head', 'district_head'], true))
                    <a href="/stations" class="btn-auth">Jurisdiction</a>
                @else
                    <a href="/citizen/my-complaints" class="btn-auth">My Complaints</a>
                @endif
            @endauth
        </div>
    </nav>

    <header class="hero">
        <div class="hero-inner">
            <h1>Browse Police Jurisdictions</h1>
            <p>Start from a Metropolitan Police HQ or District Police HQ, then open its thana list. This keeps public station browsing aligned with the Bangladesh Police hierarchy.</p>
        </div>
    </header>

    <main class="container">
        <section class="toolbar">
            <form method="GET" action="/stations">
                <input name="search" value="{{ request('search') }}" placeholder="Search by HQ, district, division, or address">
                <button type="submit">Find HQ</button>
            </form>
        </section>

        <section class="split">
            <x-station-hq-browser title="Metropolitan Police" subtitle="Commissioner-led metropolitan units" type="metro" :stations="$metroHqs" />
            <x-station-hq-browser title="District Police" subtitle="SP-led district headquarters" type="district" :stations="$districtHqs" />
        </section>
    </main>

    <script>
        document.querySelectorAll('[data-browser]').forEach((browser) => {
            const cards = [...browser.querySelectorAll('[data-card]')];
            const current = browser.querySelector('[data-current]');
            let index = 0;

            const render = () => {
                cards.forEach((card, cardIndex) => card.classList.toggle('is-active', cardIndex === index));
                if (current) current.textContent = cards.length ? `${index + 1}/${cards.length}` : '0/0';
            };

            browser.querySelectorAll('[data-prev]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!cards.length) return;
                    index = (index - 1 + cards.length) % cards.length;
                    render();
                });
            });

            browser.querySelectorAll('[data-next]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (!cards.length) return;
                    index = (index + 1) % cards.length;
                    render();
                });
            });

            render();
        });
    </script>
</body>
</html>
