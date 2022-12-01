<h1>{{__('languages.forgot_password_email')}}</h1>
   
{{__('languages.you_can_reset_password_from')}}:
<a href="{{ route('reset.password.get', $token) }}">{{__('languages.reset_password')}}</a>