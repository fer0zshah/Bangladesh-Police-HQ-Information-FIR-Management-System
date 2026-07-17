<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bangladesh Police HQ - Citizen Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; color: #333; }
        
        /* Public Navigation Bar */
        nav { background-color: #1a252f; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .nav-brand { color: #f1c40f; font-size: 20px; font-weight: bold; text-decoration: none; }
        .nav-links a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; font-size: 14px; }
        .nav-links a:hover { color: #3498db; }
        .btn-auth { background-color: #3498db; padding: 8px 15px; border-radius: 4px; color: white !important; }
        .btn-auth:hover { background-color: #2980b9; }

        /* Hero Section */
        .hero { background-color: #2c3e50; color: white; text-align: center; padding: 80px 20px; }
        .hero h1 { margin: 0; font-size: 36px; }
        .hero p { font-size: 18px; margin-top: 15px; color: #bdc3c7; }
        
        /* Content Container */
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; text-align: center; }
        
        /* Quick Action Cards */
        .card-grid { display: flex; justify-content: space-between; gap: 20px; margin-top: 40px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); width: 30%; }
        .card h3 { color: #2980b9; margin-top: 0; }
        .card a { display: inline-block; margin-top: 15px; text-decoration: none; background: #2c3e50; color: white; padding: 10px 15px; border-radius: 4px; }
        .card a:hover { background: #1a252f; }
    </style>
</head>
<body>

    <nav>
        <a href="/" class="nav-brand">BD Police HQ Portal</a>
        <div class="nav-links">
            <a href="/">Home</a> 
            <a href="/stations">Stations</a>
            <a href="#">About Us</a>
            <a href="#">Contact</a>
            
            @guest
                <a href="/login" class="btn-auth">Login</a>
                <a href="/register" class="btn-auth">Register</a>
            @endguest

            @auth
                @if(auth()->user()->role === 'super_admin')
                    <a href="/admin/dashboard" class="btn-auth">Go to HQ Dashboard →</a>
                @elseif(auth()->user()->role === 'station_oc')
                    <a href="/oc/dashboard" class="btn-auth">Go to OC Dashboard →</a>
                @else
                    <a href="/citizen/my-complaints" class="btn-auth">My Complaints →</a>
                @endif
            @endauth
        </div>
    </nav>

    <header class="hero">
        <h1>Welcome to the Central Police HQ System</h1>
        <p>A transparent, secure, and public-facing portal for citizens and law enforcement.</p>
    </header>

    <div class="container">
        <h2>How can we help you today?</h2>
        
        <div class="card-grid">
            <div class="card">
                <h3>View Public Cases</h3>
                <p>Browse general information regarding active FIRs and closed cases across the country.</p>
                <a href="/cases">View Cases</a>
            </div>

            <div class="card">
                <h3>Find a Station</h3>
                <p>Locate the nearest police station in your district, including contact details and OC information.</p>
                <a href="{{ route('stations.index') }}">Find Stations</a>
            </div>

            <div class="card">
                <h3>File a Complaint</h3>
                <p>Registered citizens can securely submit preliminary complaints directly to their local station.</p>
                <a href="{{ route('citizen.complaints.create') }}">Submit Complaint</a>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>System Database Overview</h2>
        <div class="card-grid">
            <div class="card">
                <h3>Police Officers</h3>
                <p>{{ \App\Models\Officer::count() }}</p>
                <a href="/officers">View List</a>
            </div>
            
            <div class="card">
                <h3>Criminals (W)</h3>
                <p>{{ \App\Models\Criminal::where('wanted_status', 1)->count() }}</p>
                <a href="/criminals">View List</a>
            </div>

            <div class="card">
                <h3>Total Cases</h3>
                <p>{{ \App\Models\CaseFir::count() }}</p>
                <a href="/cases">View List</a>
            </div>
        </div>
    </div>

</body>
</html>
