<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$criminal->name}} | Wanted Registry</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f4f7f6;color:#253746;font-family:Arial,sans-serif}.wrap{width:min(920px,calc(100% - 40px));margin:auto}
        main{padding:54px 0}.back{color:#1674a8;font-weight:700;text-decoration:none}.profile{position:relative;overflow:hidden;margin-top:20px;border:1px solid #e0e7ec;border-radius:16px;background:#fff;padding:36px;box-shadow:0 10px 28px rgba(31,48,64,.09)}
        .profile:after{content:"";position:absolute;right:0;top:0;width:160px;height:160px;border-radius:0 0 0 160px;background:#fff1f2}.status{position:relative;z-index:1;color:#be123c;font-size:12px;font-weight:800;letter-spacing:.18em}
        h1{position:relative;z-index:1;margin:18px 0 5px;font-size:36px}.alias{color:#70818d;font-size:17px}.facts{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:34px;border-top:1px solid #edf1f4;padding-top:26px}
        .fact{border:1px solid #edf1f4;border-radius:10px;background:#f8fafb;padding:17px}.fact span{color:#778895;font-size:11px;text-transform:uppercase;letter-spacing:.1em}.fact strong{display:block;margin-top:8px}
        .privacy{margin-top:24px;border-left:4px solid #2d6f9f;background:#eef7fc;padding:16px;color:#506675;line-height:1.6}
        @media(max-width:650px){.facts{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <x-citizen-portal-nav />
    <main class="wrap">
        <a class="back" href="{{route('wanted-criminals.index')}}">← Back to wanted list</a>
        <section class="profile">
            <span class="status">ACTIVE WANTED RECORD</span>
            <h1>{{$criminal->name}}</h1>
            <p class="alias">{{$criminal->alias ?: 'No known alias recorded'}}</p>
            <div class="facts">
                <div class="fact"><span>Registry ID</span><strong>#{{$criminal->criminal_id}}</strong></div>
                <div class="fact"><span>Date of birth</span><strong>{{$criminal->date_of_birth ? date('d M Y',strtotime($criminal->date_of_birth)) : 'Not available'}}</strong></div>
            </div>
            <div class="privacy">For privacy and investigation safety, identity numbers and operational investigation records are not available in the citizen portal.</div>
        </section>
    </main>
</body>
</html>
