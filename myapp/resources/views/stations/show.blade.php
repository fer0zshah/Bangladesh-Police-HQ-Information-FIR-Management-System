<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $station->name }} | Bangladesh Police HQ</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7f6; color: #2c3e50; }
        nav { background-color: #1a252f; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; gap: 18px; }
        .nav-brand { color: #f1c40f; font-size: 20px; font-weight: bold; text-decoration: none; }
        nav a { color: white; text-decoration: none; margin-left: 18px; font-weight: bold; font-size: 14px; }
        .hero { background: linear-gradient(135deg, #1a252f, #2c3e50); color: white; padding: 56px 20px; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
        h1 { margin: 0; font-size: 34px; }
        .muted { color: #bdc3c7; }
        .panel { max-width: 1120px; margin: -32px auto 40px; padding: 0 20px; }
        .card { background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(26,37,47,.08); padding: 24px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-top: 22px; }
        .stat { background: #f8fafb; border: 1px solid #edf1f4; border-radius: 10px; padding: 16px; }
        .stat strong { display: block; font-size: 26px; color: #1a252f; }
        .stat span { color: #7f8c8d; text-transform: uppercase; letter-spacing: .08em; font-size: 11px; }
        .section-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-top: 22px; }
        .section-title { margin: 0 0 14px; color: #1a252f; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #7f8c8d; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; border-bottom: 1px solid #edf1f4; padding: 12px 10px; }
        td { border-bottom: 1px solid #edf1f4; padding: 14px 10px; font-size: 14px; vertical-align: top; }
        .badge { display: inline-block; border-radius: 999px; background: #eef5fb; color: #2980b9; padding: 5px 9px; font-size: 12px; font-weight: bold; }
        .case-link { color: #2980b9; font-weight: bold; text-decoration: none; }
        button.case-link { border: 0; background: transparent; cursor: pointer; font: inherit; padding: 0; }
        .officer-list { display: grid; gap: 10px; }
        .officer { border: 1px solid #edf1f4; border-radius: 10px; padding: 12px; background: #f8fafb; }
        .officer strong { display: block; color: #1a252f; }
        .officer span { color: #7f8c8d; font-size: 13px; }
        .empty { color: #7f8c8d; padding: 18px; background: #f8fafb; border-radius: 10px; }
        .prompt { margin-top: 24px; background: #1a252f; color: white; border-radius: 12px; padding: 24px; display: flex; justify-content: space-between; gap: 20px; align-items: center; }
        .prompt p { margin: 6px 0 0; color: #bdc3c7; }
        .prompt-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .prompt-actions a { border-radius: 8px; padding: 11px 16px; text-decoration: none; font-weight: bold; white-space: nowrap; }
        .btn-login { color: #1a252f; background: #f1c40f; }
        .btn-register { color: white; background: #3498db; }
        .btn-citizen { color: #1a252f; background: #f1c40f; }
        .is-hidden { display: none; }
        @media (max-width: 860px) { .stats, .section-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { nav, .prompt { align-items: flex-start; flex-direction: column; } .stats, .section-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">BD Police HQ Portal</a>
        <div>
            <a href="{{ route('stations.index') }}">Stations</a>
            <a href="/login">Login</a>
        </div>
    </nav>

    <header class="hero">
        <div class="wrap">
            <h1>{{ $station->name }}</h1>
            <p class="muted">{{ $station->district }} &bull; {{ $station->contact_number ?: 'Contact not available' }}</p>
        </div>
    </header>

    <main class="panel">
        <section class="card">
            <h2>Public Station Profile</h2>
            <p>{{ $station->address ?: 'Address will be updated soon.' }}</p>

            <div class="stats">
                <div class="stat"><strong>{{ $stats['total_cases'] }}</strong><span>Total Cases</span></div>
                <div class="stat"><strong>{{ $stats['active_cases'] }}</strong><span>Active Cases</span></div>
                <div class="stat"><strong>{{ $stats['closed_cases'] }}</strong><span>Closed Cases</span></div>
                <div class="stat"><strong>{{ $stats['evidence_items'] }}</strong><span>Evidence Items</span></div>
            </div>
        </section>

        <div class="section-grid">
            <section class="card">
                <h2 class="section-title">Active Public Cases</h2>
                @if($cases->count())
                    <table>
                        <thead><tr><th>Type / Title</th><th>Status</th><th>Date</th><th>Public Detail</th></tr></thead>
                        <tbody>
                            @foreach($cases as $case)
                                <tr>
                                    <td>{{ $case->case_title }}</td>
                                    <td><span class="badge">{{ $case->status }}</span></td>
                                    <td>{{ date('d M Y', strtotime($case->date_filed)) }}</td>
                                    <td>
                                        @guest
                                            <button type="button" class="case-link js-detail-auth">View Detail</button>
                                        @else
                                            <a class="case-link" href="{{ route('stations.cases.show', [$station, $case]) }}">View Detail</a>
                                        @endguest
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No active public cases listed for this station.</div>
                @endif
            </section>

            <section class="card">
                <h2 class="section-title">Officers at this Station</h2>
                <div class="officer-list">
                    @forelse($officers as $officer)
                        <div class="officer">
                            <strong>{{ $officer->name }}</strong>
                            <span>{{ $officer->rank }}</span>
                        </div>
                    @empty
                        <div class="empty">No active officers listed publicly.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <section id="detail-auth-prompt" class="prompt is-hidden">
            <div>
                <strong>Submit a complaint?</strong>
                <p>Register or log in to submit and track a complaint securely.</p>
            </div>
            <div class="prompt-actions">
                <a class="btn-login" href="/login">Login</a>
                <a class="btn-register" href="/register">Register</a>
            </div>
        </section>
    </main>
    <script>
        document.querySelectorAll('.js-detail-auth').forEach(button => {
            button.addEventListener('click', () => {
                const prompt = document.getElementById('detail-auth-prompt');
                prompt.classList.remove('is-hidden');
                prompt.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    </script>
</body>
</html>
