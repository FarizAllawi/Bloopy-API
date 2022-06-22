@component('mail::message')
Hi {{$recipant['name']}}
<br>
Do you want to join my business, the {{ $business->business_name }}
@component('mail::button', ['url' => config('app.url').'/business/invitation?business='.$business->id.'&recipant='.$recipant['email'].'&status='.$status.'&token='.$token])
Join Here
@endcomponent
if you don't want to join this business ignore this email
<br><br>
Thanks,<br>
{{$sender}} 
@endcomponent

