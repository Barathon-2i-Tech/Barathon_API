<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nouvelle demande d'ajout de catégorie</title>
</head>
<body>
<h2>Nouvelle demande d'ajout de catégorie</h2>
<p>Bonjour,</p>
<p>L'utilisateur {{$user->first_name}} {{Str::upper($user->last_name)}} souhaiterai créer une nouvelle categorie.</p>

<p>Voici les détails de la catégorie :</p>
<ul>
    <li>Nom de la catégorie : {{$categoryName}}</li>
    <li>Affectation de la catégorie : {{$categoryVisibility}}</li>
</ul>
<p>Cordialement</p>
</body>
</html>
