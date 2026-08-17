<!doctype html>
<html lang="rw">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kwegereza — Imiyoborere</title>

    {{--
        Carried over from the React app's old standalone index.html —
        that file (and its <link> tags) is gone now that this is served
        from Laravel's own Blade view instead; without this here, the
        Fraunces/Inter fonts the whole design depends on would silently
        fall back to default sans-serif with no error anywhere to
        explain why the "Kwegereza" wordmark and headings look wrong.
    --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite('resources/js/admin/main.jsx')
  </head>
  <body>
    <div id="root"></div>
  </body>
</html>
