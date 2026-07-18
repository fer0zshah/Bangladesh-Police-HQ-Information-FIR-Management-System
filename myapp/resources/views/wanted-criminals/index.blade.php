<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wanted Criminals | Bangladesh Police HQ</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f7f6;color:#253746;font-family:Arial,sans-serif}
        .hero{background:#2d4358;padding:64px 20px;color:#fff}.wrap{width:min(1180px,calc(100% - 40px));margin:auto}
        .hero h1{margin:0;font-size:38px}.hero p{max-width:700px;margin:14px 0 0;color:#c7d0d8;line-height:1.6}
        main{padding:34px 0 60px}.search{display:grid;grid-template-columns:1fr auto auto;gap:12px;border:1px solid #e1e8ed;border-radius:12px;background:#fff;padding:16px;box-shadow:0 6px 18px rgba(31,48,64,.07)}
        input{min-width:0;border:1px solid #cfd9e0;border-radius:8px;padding:13px 15px;font-size:15px}button,.reset{border-radius:8px;padding:13px 20px;font-weight:700;text-decoration:none}
        button{border:1px solid #2d6f9f;background:#2d6f9f;color:#fff;cursor:pointer}.reset{border:1px solid #d7e0e6;color:#526879}
        .notice{margin:18px 0 0;color:#667985;font-size:13px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-top:26px}
        .card{position:relative;overflow:hidden;min-height:235px;border:1px solid #e0e7ec;border-radius:13px;background:#fff;padding:24px;box-shadow:0 5px 16px rgba(31,48,64,.07)}
        .card:after{content:"";position:absolute;right:0;top:0;width:88px;height:88px;border-radius:0 0 0 88px;background:#fff1f2}
        .status{position:relative;z-index:1;color:#be123c;font-size:11px;font-weight:800;letter-spacing:.15em}.card h2{position:relative;z-index:1;margin:18px 0 4px;font-size:21px}.alias{color:#70818d}
        .meta{display:flex;gap:22px;margin-top:28px;border-top:1px solid #edf1f4;padding-top:16px;color:#70818d;font-size:12px}.meta strong{display:block;margin-top:4px;color:#253746;font-size:15px}
        .detail{display:inline-block;margin-top:22px;color:#1674a8;font-weight:700;text-decoration:none}.pagination{margin-top:28px}
        @media(max-width:900px){.grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){.search{grid-template-columns:1fr}.grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <x-citizen-portal-nav />
    <header class="hero"><div class="wrap"><h1>Wanted Criminals</h1><p>Public safety registry of people currently marked as wanted. Personal identity numbers and FIR information are not displayed.</p></div></header>
    <main class="wrap">
        <form class="search" method="GET" action="{{route('wanted-criminals.index')}}">
            <input name="search" value="{{request('search')}}" placeholder="Search by name or known alias">
            <button>Search Registry</button>
            <a class="reset" href="{{route('wanted-criminals.index')}}">Reset</a>
        </form>
        @guest<p class="notice">You can browse this list. Log in to open a wanted-person profile.</p>@endguest
        <section class="grid">
            @forelse($criminals as $criminal)
                <article class="card">
                    <span class="status">WANTED</span>
                    <h2>{{$criminal->name}}</h2>
                    <p class="alias">{{$criminal->alias ?: 'No known alias'}}</p>
                    <div class="meta"><div>Registry ID<strong>#{{$criminal->criminal_id}}</strong></div><div>Status<strong>Active</strong></div></div>
                    <a class="detail" href="{{route('wanted-criminals.show',$criminal->criminal_id)}}">View profile →</a>
                </article>
            @empty
                <p>No wanted records match your search.</p>
            @endforelse
        </section>
        <div class="pagination">{{$criminals->links()}}</div>
    </main>
</body>
</html>
