<html>
<head>
	<title>How to send mail using queue in Laravel 7/8 ?</title>
</head>
<body>  
<strong><p>Hi, {{$userdata['name']}}</p></strong>
<p>Welcome to our Adaptive learning portal.</p>
<strong><p>Your credentials is below here :</p></strong>
<p><strong>Name </strong>: {{$userdata['name']}}</p>
<p><strong>Email </strong>: {{$userdata['email']}}</p>
<p><strong>Password </strong>: {{$userdata['DecryptPassword']}}</p>
<p><strong>Mobile Number </strong>: {{$userdata['mobile_no']}}</p>
<p><strong>DateOfBirth </strong>: {{$userdata['dob']}}</p>
<p><strong>City </strong>: {{$userdata['city']}}</p>
<p><strong>Click here to login </strong>: <a href="{{$login_url}}">Click Here</a></p> 
<strong>Thanks & Regards.</strong>
</body>
</html>