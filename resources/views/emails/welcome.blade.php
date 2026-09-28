@component('mail::message')
# Selamat Datang, {{ $user->name }}!

Terima kasih sudah mendaftar di **{{ config('app.name') }}**. Akun kamu dengan email `{{ $user->email }}` sudah berhasil dibuat.

@component('mail::button', ['url' => config('app.url')])
Mulai Sekarang
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
