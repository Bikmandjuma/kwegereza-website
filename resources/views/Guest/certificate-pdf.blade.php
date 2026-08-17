<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>{{ $certificate->certificate_number }}</title>
<style>
    @page { margin: 0; }
    body{
        margin:0; padding:0; font-family: 'DejaVu Sans', Arial, sans-serif;
        background:#f5ebe2;
    }
    .cert-wrap{
        width: 1000px; height: 700px; margin: 20px auto; background:#fff;
        border: 10px solid #094939; position: relative; padding: 50px 70px;
        box-sizing: border-box; text-align: center;
    }
    .cert-inner-border{
        position:absolute; inset: 16px; border: 2px solid #e2b45f;
    }
    .cert-logo{ font-size: 13px; letter-spacing: 3px; color:#058e48; font-weight:700; margin-bottom: 4px; }
    .cert-arabic{ font-size: 14px; color:#c8a36c; margin-bottom: 20px; }
    .cert-title{ font-size: 34px; font-weight: 800; color:#094939; margin-bottom: 6px; letter-spacing: 1px; }
    .cert-sub{ font-size: 14px; color:#777; margin-bottom: 30px; }
    .cert-name{ font-size: 30px; font-weight: 800; color:#058e48; margin: 10px 0; border-bottom: 2px solid #e2b45f; display:inline-block; padding-bottom:6px; }
    .cert-program{ font-size: 18px; color:#333; margin: 16px 0 30px; }
    .cert-meta{ display:flex; justify-content: space-between; margin-top: 50px; font-size: 12px; color:#555; }
    .cert-meta div{ text-align:center; width: 220px; }
    .cert-meta strong{ display:block; font-size: 13px; color:#094939; margin-bottom:4px; }
    .cert-number{ position:absolute; bottom: 24px; right: 40px; font-size: 10px; color:#999; }
</style>
</head>
<body>

<div class="cert-wrap">
    <div class="cert-inner-border"></div>

    <p class="cert-logo">KWEGEREZA ISLAM UMURYANGO</p>
    <p class="cert-arabic">تقريب السنة بين يدي الأمة</p>

    <p class="cert-title">Certificate of Completion</p>
    <p class="cert-sub">This certifies that</p>

    <p class="cert-name">{{ $certificate->user->firstname }} {{ $certificate->user->lastname }}</p>

    <p class="cert-program">
        has successfully completed
        <strong>{{ $certificate->course->title ?? $certificate->title }}</strong>
    </p>

    <div class="cert-meta">
        <div>
            <strong>{{ $certificate->issued_at->format('F j, Y') }}</strong>
            Date Issued
        </div>
        <div>
            <strong>{{ $certificate->certificate_number }}</strong>
            Certificate ID
        </div>
        <div>
            <strong>Kwegereza Islam Umuryango</strong>
            Issuing Organization
        </div>
    </div>

    <p class="cert-number">Verify at: {{ $certificate->verifyUrl() }}</p>
</div>

</body>
</html>
