<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome Pro</title>
</head>
<body>
    Bonjour {{Str::upper($user->last_name)}} {{$user->first_name}},
    <br>
    <br>
    Nous sommes heureux de vous avoir parmi nos propriétaires d'établissements.
    <br>
    Sur Barathon vous pourrez y enregistrer vos établissements pour y créer des événements qui seront visibles auprès de nos utilisateurs les "Barathoniens" !
    <br>
    Vous êtes actuellement en attente de validation par notre équipe pour vérifier si vos informations sont bonnes, nous vous préviendrons lors de l'activation de votre compte.
    <br>
    Merci de votre compréhension,
    <br>
    <br>
    <b>L'équipe de Barathon</b>
</body>
</html>