<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIR #{{ $case->case_id }} | {{ $station->name }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7f6; color: #2c3e50; }
        nav { background-color: #1a252f; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .nav-brand { color: #f1c40f; font-size: 20px; font-weight: bold; text-decoration: none; }
        nav a { color: white; text-decoration: none; margin-left: 18px; font-weight: bold; font-size: 14px; }
        .hero { background: linear-gradient(135deg, #1a252f, #2c3e50); color: white; padding: 56px 20px; }
        .wrap, .content { max-width: 960px; margin: 0 auto; padding: 0 20px; }
        h1 { margin: 0; font-size: 32px; }
        .muted { color: #bdc3c7; }
        .card { margin: -32px auto 40px; background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(26,37,47,.08); padding: 24px; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin: 20px 0; }
        .stat { border: 1px solid #edf1f4; background: #f8fafb; border-radius: 10px; padding: 14px; }
        .stat span { display: block; color: #7f8c8d; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
        .stat strong { display: block; margin-top: 6px; color: #1a252f; }
        .badge { display: inline-block; border-radius: 999px; background: #eef5fb; color: #2980b9; padding: 5px 9px; font-size: 12px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; color: #7f8c8d; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; border-bottom: 1px solid #edf1f4; padding: 12px 10px; }
        td { border-bottom: 1px solid #edf1f4; padding: 14px 10px; font-size: 14px; }
        .privacy { margin-top: 20px; padding: 14px; border-radius: 10px; background: #fff8e1; color: #806000; }
        .prompt { margin-top: 24px; background: #1a252f; color: white; border-radius: 12px; padding: 24px; display: flex; justify-content: space-between; gap: 20px; align-items: center; }
        .prompt p { margin: 6px 0 0; color: #bdc3c7; }
        .prompt-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .prompt-actions a { border-radius: 8px; padding: 11px 16px; text-decoration: none; font-weight: bold; white-space: nowrap; }
        .btn-login { color: #1a252f; background: #f1c40f; }
        .btn-register { color: white; background: #3498db; }
        .btn-citizen { color: #1a252f; background: #f1c40f; }
        @media (max-width: 700px) { nav, .prompt { align-items: flex-start; flex-direction: column; gap: 12px; } .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">BD Police HQ Portal</a>
        <div>
            <a href="{{ route('stations.show', $station) }}">Back to Station</a>
            <a href="{{ route('stations.index') }}">Browse Stations</a>
        </div>
    </nav>

    <header class="hero">
        <div class="wrap">
            <h1>{{ $case->case_title }}</h1>
            <p class="muted">FIR #{{ $case->case_id }} &bull; {{ $station->name }}</p>
        </div>
    </header>

    <main class="content">
        @guest
        <section class="prompt">
            <div>
                <strong>Submit a complaint?</strong>
                <p>Register or log in to submit and track a complaint securely.</p>
            </div>
            <div class="prompt-actions">
                <a class="btn-login" href="/login">Login</a>
                <a class="btn-register" href="/register">Register</a>
            </div>
        </section>
        @else
        <section class="card">
            <h2>Public Case Detail</h2>
            <div class="grid">
                <div class="stat"><span>Status</span><strong><span class="badge">{{ $case->status }}</span></strong></div>
                <div class="stat"><span>Date Filed</span><strong>{{ date('d M Y', strtotime($case->date_filed)) }}</strong></div>
                <div class="stat"><span>Investigating Officer</span><strong>{{ $case->officer?->name ?? 'Not assigned' }}</strong></div>
                <div class="stat"><span>Evidence Count</span><strong>{{ $case->evidence_count }}</strong></div>
                <div class="stat"><span>Linked People</span><strong>{{ $case->criminals_count }}</strong></div>
                <div class="stat"><span>Station</span><strong>{{ $station->name }}</strong></div>
            </div>

            <h3>Linked Criminal Registry Entries</h3>
            @if($case->criminals->count())
                <table>
                    <thead><tr><th>Name</th><th>Alias</th><th>Involvement</th><th>Wanted Status</th></tr></thead>
                    <tbody>
                        @foreach($case->criminals as $criminal)
                            <tr>
                                <td>{{ $criminal->name }}</td>
                                <td>{{ $criminal->alias ?: 'N/A' }}</td>
                                <td>{{ $criminal->pivot->involvement_type }}</td>
                                <td>{{ $criminal->wanted_status ? 'Wanted' : 'Not wanted' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No linked criminal registry entries are public for this case.</p>
            @endif

            <div class="privacy">Personal identifiers such as NID number, date of birth, complaint details, and evidence descriptions are hidden from public view.</div>
        </section>
        @endguest
    </main>
</body>
</html>
