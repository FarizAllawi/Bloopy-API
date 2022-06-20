@component('mail::message')
Hi {{$user->user_name}}
<br>
Welcome to Bloopy, your account has been created successfully.
@component('mail::button', ['url' => config('app.frontend_url').'/auth/login'])
Login Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
