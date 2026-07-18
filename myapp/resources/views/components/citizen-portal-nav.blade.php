<style>
    .citizen-portal-nav{min-height:78px;background:#172430;border-bottom:1px solid #304252;padding:0 42px;display:flex;align-items:center;justify-content:space-between;gap:28px;font-family:Arial,sans-serif}
    .citizen-portal-brand{color:#ffd400;font-size:22px;font-weight:800;text-decoration:none;white-space:nowrap}
    .citizen-portal-links{display:flex;align-items:center;gap:34px}
    .citizen-portal-links>a{margin:0;color:#fff;font-size:14px;font-weight:700;text-decoration:none;white-space:nowrap}
    .citizen-portal-links>a:hover{color:#54b7ef}
    .citizen-portal-auth{min-width:94px;border:1px solid #3498db;border-radius:8px;background:#3498db;padding:11px 17px;color:#fff!important;text-align:center}
    .citizen-user-menu{position:relative}
    .citizen-user-trigger{min-width:206px;border:1px solid rgba(84,183,239,.55);border-radius:9px;background:rgba(52,152,219,.13);padding:12px 17px;color:#fff;font:700 14px Arial,sans-serif;text-align:left;text-transform:uppercase;cursor:pointer}
    .citizen-user-trigger::after{content:"▾";float:right;margin-left:14px}
    .citizen-user-trigger:hover,.citizen-user-trigger:focus{border-color:#54b7ef;background:rgba(52,152,219,.24);outline:none}
    .citizen-user-dropdown{position:absolute;z-index:100;top:100%;right:0;width:260px;display:none;overflow:hidden;border:1px solid #dce5eb;border-radius:10px;background:#fff;box-shadow:0 16px 36px rgba(23,36,48,.2)}
    .citizen-user-menu:hover .citizen-user-dropdown,.citizen-user-menu:focus-within .citizen-user-dropdown,.citizen-user-menu.is-open .citizen-user-dropdown{display:block}
    .citizen-user-dropdown a,.citizen-user-dropdown button{display:block;width:100%;margin:0;border:0;border-bottom:1px solid #edf1f4;background:#fff;padding:12px 15px;color:#34495e;font:700 13px Arial,sans-serif;text-align:left;text-decoration:none;cursor:pointer}
    .citizen-user-dropdown a:hover,.citizen-user-dropdown button:hover{background:#f3f7fa;color:#2980b9}
    .citizen-nav-toggle{display:none;border:1px solid #425463;border-radius:7px;background:transparent;padding:8px 11px;color:#fff;font-size:20px;cursor:pointer}
    @media(max-width:900px){.citizen-portal-nav{padding:15px 22px;flex-wrap:wrap}.citizen-nav-toggle{display:block}.citizen-portal-links{width:100%;display:none;align-items:stretch;gap:10px;flex-direction:column}.citizen-portal-nav.is-menu-open .citizen-portal-links{display:flex}.citizen-portal-links>a{padding:8px 0}.citizen-user-trigger,.citizen-user-menu,.citizen-user-dropdown{width:100%}.citizen-user-trigger{min-width:0}.citizen-user-dropdown{position:static;margin-top:8px}}
</style>

<nav class="citizen-portal-nav" aria-label="Citizen portal navigation" data-citizen-portal-nav>
    <a href="{{ url('/') }}" class="citizen-portal-brand">BD Police HQ Portal</a>
    <button type="button" class="citizen-nav-toggle" aria-label="Open navigation" aria-expanded="false" data-portal-menu-toggle>☰</button>
    <div class="citizen-portal-links">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('stations.index') }}">Browse Stations</a>
        <a href="{{ route('wanted-criminals.index') }}">Wanted List</a>
        <a href="{{ url('/#about') }}">About Us</a>
        <a href="{{ url('/#contact') }}">Contact</a>
        @guest
            <a href="{{ route('login') }}" class="citizen-portal-auth">Login</a>
            <a href="{{ route('register') }}" class="citizen-portal-auth">Register</a>
        @endguest
        @auth
            @if(auth()->user()->role === 'citizen')
                <div class="citizen-user-menu" data-citizen-user-menu>
                    <button type="button" class="citizen-user-trigger" aria-expanded="false" data-account-menu-toggle>{{ auth()->user()->name }}</button>
                    <div class="citizen-user-dropdown">
                        <a href="{{ route('profile.edit') }}">My profile & complaints</a>
                        <a href="{{ route('citizen.complaints.create') }}">Submit complaint</a>
                        <a href="{{ route('wanted-criminals.index') }}">Wanted criminals</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">Log out</button></form>
                    </div>
                </div>
            @elseif(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.dashboard') }}" class="citizen-portal-auth">IGP Dashboard</a>
            @elseif(in_array(auth()->user()->role, ['metro_head','district_head'], true) && Route::has('command.dashboard'))
                <a href="{{ route('command.dashboard') }}" class="citizen-portal-auth">Command</a>
            @elseif(auth()->user()->role === 'station_oc')
                <a href="{{ route('oc.dashboard') }}" class="citizen-portal-auth">OC Dashboard</a>
            @endif
        @endauth
    </div>
</nav>
<script>
(()=>{const nav=document.querySelector('[data-citizen-portal-nav]');if(!nav||nav.dataset.navigationReady==='true')return;nav.dataset.navigationReady='true';const portal=nav.querySelector('[data-portal-menu-toggle]');const menu=nav.querySelector('[data-citizen-user-menu]');const trigger=menu?.querySelector('[data-account-menu-toggle]');const setOpen=open=>{if(!menu)return;menu.classList.toggle('is-open',open);trigger?.setAttribute('aria-expanded',String(open))};portal?.addEventListener('click',e=>{e.stopPropagation();const open=nav.classList.toggle('is-menu-open');portal.setAttribute('aria-expanded',String(open))});trigger?.addEventListener('click',e=>{e.stopPropagation();setOpen(!menu.classList.contains('is-open'))});menu?.querySelector('.citizen-user-dropdown')?.addEventListener('click',e=>e.stopPropagation());document.addEventListener('click',()=>{setOpen(false);nav.classList.remove('is-menu-open');portal?.setAttribute('aria-expanded','false')});document.addEventListener('keydown',e=>{if(e.key==='Escape'){setOpen(false);nav.classList.remove('is-menu-open')}})})();
</script>
