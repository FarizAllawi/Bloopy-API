@component('mail::message')
Hi {{$user->user_name}}
<br>
Welcome to Bloopy {{ $platform }}, this is your verification code. 
<br>
<center>
    <strong><span>{{$code[0]}} {{$code[1]}} {{$code[2]}} {{$code[3]}}</span></strong>
</center>
<br>
If you don't registering in Bloopy {{ $platform }}, please ignore this email. 
And if you do, then please input this code into Bloopy {{ $platform }} {{$app}}.
<br><br>
Thanks,<br>
Bloopy {{ $platform }}
@endcomponent
