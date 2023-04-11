<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Change password</title>
</head>
<body>
    
    Bonjour {{Str::upper($user->last_name)}} {{$user->first_name}},
    <br>
    <br>
    Nous avons changer votre mot de passe !
    <br>
    voici le nouveau : <b>{{$password}}</b>
    <br>
    Vous pouvez changer votre mot de passe sur l'application.
    <br>
    <br>
    <b>L'équipe de Barathon</b>

</body>
</html>