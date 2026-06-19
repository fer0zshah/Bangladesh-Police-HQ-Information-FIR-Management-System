<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police HQ System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; }
        nav { background-color: #2c3e50; padding: 15px; text-align: center; }
        nav a { color: white; text-decoration: none; margin: 0 15px; font-weight: bold; }
        nav a:hover { color: #3498db; }
        .container { padding: 40px; max-width: 1000px; margin: auto; }
        
        /* Table Styles (moved here so all pages can use them!) */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2980b9; color: white; }
        tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>

    <nav>
        <a href="/stations">Stations</a>
        <a href="/officers">Officers</a>
        <a href="/criminals">Criminals</a>
        <a href="/cases">Cases (FIR)</a>
    </nav>

    <div class="container">
        @yield('content')
    </div>

</body>
</html>