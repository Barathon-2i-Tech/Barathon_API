<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validation de votre établissement</title>
</head>
<body>

Bonjour {{Str::upper($user->last_name)}} {{$user->first_name}},
<br>
<br>
Nous sommes heureux de vous annoncer que votre établissement "{{$establishment->trade_name}}" est validé.
<br>
Vous pouvez donc y créer vos événements est profiter pleinement de l'application !</b>
<br>
Bon séjour parmi nous !
<br>
<br>
<b>L'équipe de Barathon</b>

</body>
</html>
