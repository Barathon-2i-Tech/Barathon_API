<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validation de votre événement</title>
</head>
<body>

Bonjour {{Str::upper($user->last_name)}} {{$user->first_name}},
<br>
<br>
Nous sommes heureux de vous annoncer que votre événement "{{$event->event_name}}" est validé.
<br>
Votre événement est donc en ligne est visible par tous les barathoniens !</b>
<br>
Bon séjour parmi nous !
<br>
<br>
<b>L'équipe de Barathon</b>

</body>
</html>
