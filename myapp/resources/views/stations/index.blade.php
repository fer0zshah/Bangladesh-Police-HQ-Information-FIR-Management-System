<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Police Stations | Bangladesh Police HQ</title>
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
        .hero p { margin: 12px 0 0; color: #bdc3c7; font-size: 16px; max-width: 680px; }
        .container { padding: 32px 20px 56px; }
        .toolbar { background: white; border-radius: 12px; box-shadow: 0 8px 24px rgba(26,37,47,.08); padding: 18px; margin-top: -54px; position: relative; }
        .toolbar form { display: grid; grid-template-columns: 1fr 220px auto; gap: 12px; }
        input, select { width: 100%; border: 1px solid #d8e0e6; border-radius: 8px; padding: 12px 14px; font-size: 14px; }
        button { border: 0; border-radius: 8px; padding: 0 22px; font-weight: bold; background: #2c3e50; color: white; cursor: pointer; }
        button:hover { background: #1a252f; }
        .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 28px; }
        .station-card { background: white; border-radius: 12px; box-shadow: 0 4px 14px rgba(26,37,47,.08); padding: 22px; display: flex; flex-direction: column; min-height: 250px; }
        .eyebrow { color: #3498db; font-size: 11px; letter-spacing: .16em; text-transform: uppercase; font-weight: bold; margin: 0 0 8px; }
        .station-card h2 { margin: 0; font-size: 20px; color: #1a252f; }
        .meta { margin: 12px 0 0; color: #5d6d7e; line-height: 1.5; font-size: 14px; }
        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 18px 0; }
        .stat { border: 1px solid #edf1f4; border-radius: 10px; padding: 10px; background: #f8fafb; }
        .stat strong { display: block; color: #1a252f; font-size: 18px; }
        .stat span { color: #7f8c8d; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
        .card-footer { margin-top: auto; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .status { color: #198754; background: #e8f6ef; border-radius: 999px; padding: 6px 10px; font-size: 12px; font-weight: bold; }
        .profile-link { color: white; background: #3498db; border-radius: 8px; padding: 10px 12px; text-decoration: none; font-weight: bold; font-size: 13px; }
        .empty { background: white; border-radius: 12px; padding: 42px; text-align: center; color: #7f8c8d; margin-top: 28px; }
        .pagination { margin-top: 24px; }
        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } .toolbar form { grid-template-columns: 1fr; } button { padding: 12px 18px; } }
        @media (max-width: 640px) { nav { align-items: flex-start; flex-direction: column; } .grid { grid-template-columns: 1fr; } }
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
                @elseif(auth()->user()->role === 'officer')
                    <a href="/oc/dashboard" class="btn-auth">OC Dashboard</a>
                @else
                    <a href="/citizen/my-complaints" class="btn-auth">My Complaints</a>
                @endif
            @endauth
        </div>
    </nav>

    <header class="hero">
        <div class="hero-inner">
            <h1>Browse Police Stations</h1>
            <p>Find public station information, district coverage, contact number, and high-level public activity counts.</p>
        </div>
    </header>

    <main class="container">
        <section class="toolbar">
            <form method="GET" action="/stations">
                <input name="search" value="{{ request('search') }}" placeholder="Search by station, district, or address">
                <select name="district">
                    <option value="">All districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district }}" @selected(request('district') === $district)>{{ $district }}</option>
                    @endforeach
                </select>
                <button type="submit">Find Station</button>
            </form>
        </section>

        @if($stations->count())
            <section class="grid">
                @foreach($stations as $station)
                    <article class="station-card">
                        <p class="eyebrow">{{ $station->district }}</p>
                        <h2>{{ $station->name }}</h2>
                        <p class="meta">{{ $station->address ?: 'Address will be updated soon.' }}</p>
                        <p class="meta"><strong>Contact:</strong> {{ $station->contact_number ?: 'Not available' }}</p>

                        <div class="stats">
                            <div class="stat">
                                <strong>{{ $station->cases_count }}</strong>
                                <span>Public Cases</span>
                            </div>
                            <div class="stat">
                                <strong>{{ $station->officers_count }}</strong>
                                <span>Officers</span>
                            </div>
                        </div>

                        <div class="card-footer">
                            <span class="status">{{ $station->status }}</span>
                            <a class="profile-link" href="{{ route('stations.show', $station) }}">View Profile</a>
                        </div>
                    </article>
                @endforeach
            </section>

            <div class="pagination">{{ $stations->links() }}</div>
        @else
            <section class="empty">
                <h2>No stations found</h2>
                <p>Try another district, station name, or address keyword.</p>
            </section>
        @endif

    </main>
</body>
</html>
