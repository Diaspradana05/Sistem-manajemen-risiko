@component('mail::message')
# Halo {{ $data['nama'] }}

Ini adalah email percobaan dari Laravel.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
