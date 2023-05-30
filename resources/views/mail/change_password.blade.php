<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notification de changement de mot de passe</title>
</head>
<body>
Cher(e) {{$user->first_name}} {{Str::upper($user->last_name)}},

<p>Nous souhaitons vous informer que nous avons récemment procédé à un changement de mot de passe pour votre compte. Par
mesure de sécurité, nous avons généré un nouveau mot de passe pour votre compte que vous trouverez ci-dessous :</p>

<p>Nouveau mot de passe : <b>{{$password}}</b></p>

<p>Nous vous conseillons vivement de changer ce mot de passe dès que possible, en utilisant la fonctionnalité de
modification de mot de passe disponible sur notre application.</p>

<p>Si vous avez des questions ou des préoccupations concernant cette notification, n'hésitez pas à nous contacter. Nous
sommes disponibles pour vous aider à tout moment.</p>

Cordialement,</br>
L'équipe de Barathon

</body>
</html>
