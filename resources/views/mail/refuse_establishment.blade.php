<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Refus de votre établissement</title>
</head>
<body>

Bonjour {{Str::upper($user->last_name)}} {{$user->first_name}},
<br>
<br>
Malheureusement, nous avons repéré un problème lié à votre établissement "{{$establishment->trade_name}}" qui empêche l'activation de celui-ci.
<br>
Veuillez vous connecter pour voir vérifier vos informations.
<br>
<br>
<b>L'équipe de Barathon</b>

</body>
</html>
