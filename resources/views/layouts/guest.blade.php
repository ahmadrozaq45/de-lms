<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LMS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="antialiased" style="margin:0; min-height:100vh; background:linear-gradient(135deg,#3b5bdb 0%,#4c6ef5 50%,#7950f2 100%); display:flex; align-items:center; justify-content:center; padding:20px;">

    <div style="background:white; border-radius:16px; padding:40px; width:100%; max-width:400px; box-shadow:0 25px 50px rgba(0,0,0,0.15);">
        <!-- Logo -->
        <div style="text-align:center; margin-bottom:28px;">
            <div style="width:60px; height:60px; background:linear-gradient(135deg,#3b5bdb,#4c6ef5); border-radius:14px; display:inline-flex; align-items:center; justify-content:center; color:white; font-size:16px; font-weight:700; margin-bottom:16px;">LMS</div>
            <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0 0 4px 0;">Learning Management System</h1>
            <p style="font-size:14px; color:#6b7280; margin:0;">Masuk ke akun Anda</p>
        </div>

        {{ $slot }}
    </div>

</body>
</html>
