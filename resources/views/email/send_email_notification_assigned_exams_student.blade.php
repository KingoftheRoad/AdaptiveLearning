<html>
    <head></head>
    <body>
        <p>Hi {{ucfirst($userdata->name) ?? ''}}</p>
        <br>
        <p>ALP school has assiged to you new exams can you please check and attempt this exams</p>
        <br><br>
        <p>Click Here : <a href="{{$login_url}}" target="_blank">{{$login_url}}</a></p>
    </body>
</html>