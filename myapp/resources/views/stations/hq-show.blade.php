<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $station->name }} Thanas | Bangladesh Police HQ</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f7f6; color: #2c3e50; }
        nav { background-color: #1a252f; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; gap: 18px; }
        .nav-brand { color: #f1c40f; font-size: 20px; font-weight: bold; text-decoration: none; }
        nav a { color: white; text-decoration: none; margin-left: 18px; font-weight: bold; font-size: 14px; }
        .hero { background: linear-gradient(135deg, #1a252f, #2c3e50); color: white; padding: 56px 20px; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
        h1 { margin: 0; font-size: 34px; }
        .muted { color: #bdc3c7; line-height: 1.6; }
        .panel { max-width: 1120px; margin: -32px auto 40px; padding: 0 20px; }
        .card { background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(26,37,47,.08); padding: 24px; }
        .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 18px; }
        .stat { background: #f8fafb; border: 1px solid #edf1f4; border-radius: 10px; padding: 16px; }
        .stat strong { display: block; font-size: 26px; color: #1a252f; }
        .stat span { color: #7f8c8d; text-transform: uppercase; letter-spacing: .08em; font-size: 11px; }
        .section-title { margin: 28px 0 14px; color: #1a252f; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(26,37,47,.08); }
        th { text-align: left; color: #7f8c8d; font-size: 11px; letter-spacing: .08em; text-transform: uppercase; border-bottom: 1px solid #edf1f4; padding: 14px 16px; background: #fbfcfd; }
        td { border-bottom: 1px solid #edf1f4; padding: 16px; font-size: 14px; vertical-align: top; }
        tr:last-child td { border-bottom: 0; }
        .station-name { color: #1a252f; font-weight: bold; }
        .badge { display: inline-block; border-radius: 999px; background: #eef5fb; color: #2980b9; padding: 5px 9px; font-size: 12px; font-weight: bold; }
        .profile-link { color: white; background: #3498db; border-radius: 8px; padding: 10px 12px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block; }
        .empty { background: white; border-radius: 12px; padding: 42px; text-align: center; color: #7f8c8d; box-shadow: 0 8px 24px rgba(26,37,47,.08); }
        @media (max-width: 760px) { nav { align-items: flex-start; flex-direction: column; } .summary { grid-template-columns: 1fr; } table, thead, tbody, th, td, tr { display: block; } thead { display: none; } tr { border-bottom: 1px solid #edf1f4; } td { border-bottom: 0; padding: 10px 16px; } td::before { content: attr(data-label); display: block; color: #7f8c8d; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; font-weight: bold; } }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="nav-brand">BD Police HQ Portal</a>
        <div>
            <a href="{{ route('stations.index') }}">All HQs</a>
            <a href="/login">Login</a>
        </div>
    </nav>

    <header class="hero">
        <div class="wrap">
            <h1>{{ $station->name }}</h1>
            <p class="muted">
                {{ $station->head_rank ?? 'Police command' }}
                @if($station->division)
                    &bull; {{ $station->division }}
                @endif
                @if($station->district)
                    &bull; {{ $station->district }}
                @endif
            </p>
        </div>
    </header>

    <main class="panel">
        <section class="card">
            <h2>Thana Stations Under This HQ</h2>
            <p class="muted" style="color:#5d6d7e">{{ $station->address ?: 'HQ address will be updated soon.' }}</p>
            <div class="summary">
                <div class="stat"><strong>{{ $thanas->count() }}</strong><span>Total Thanas</span></div>
                <div class="stat"><strong>{{ $thanas->sum('cases_count') }}</strong><span>Public Cases</span></div>
                <div class="stat"><strong>{{ $thanas->sum('officers_count') }}</strong><span>Active Officers</span></div>
            </div>
        </section>

        <h2 class="section-title">Stations / Thanas</h2>
        @if($thanas->count())
            <table>
                <thead>
                    <tr>
                        <th>Station</th>
                        <th>District</th>
                        <th>Contact</th>
                        <th>Cases</th>
                        <th>Officers</th>
                        <th>Profile</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($thanas as $thana)
                        <tr>
                            <td data-label="Station">
                                <span class="station-name">{{ $thana->name }}</span>
                                <div class="muted" style="color:#7f8c8d">{{ $thana->address ?: 'Address will be updated soon.' }}</div>
                            </td>
                            <td data-label="District">{{ $thana->district ?: 'N/A' }}</td>
                            <td data-label="Contact">{{ $thana->contact_number ?: 'Not available' }}</td>
                            <td data-label="Cases"><span class="badge">{{ $thana->cases_count }}</span></td>
                            <td data-label="Officers"><span class="badge">{{ $thana->officers_count }}</span></td>
                            <td data-label="Profile"><a class="profile-link" href="{{ route('stations.show', $thana) }}">View Profile</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <section class="empty">
                <h2>No thana stations found</h2>
                <p>This HQ does not have active child thanas attached yet.</p>
            </section>
        @endif
    </main>
</body>
</html>
