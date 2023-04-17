<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nouvelle demande d'ajout de catégorie</title>
</head>
<body>
<h2>Nouvelle demande d'ajout de catégorie</h2>
<p>Bonjour,</p>
<p>L'utilisateur {{Str::upper($user->last_name)}} {{$user->first_name}} souhaiterai créer une nouvelle categorie.</p>

<p>Voici les détails de la catégorie :</p>
<ul>
    <li>Nom de la catégorie : {{$categoryName}}</li>
    <li>Affectation de la catégorie : {{$categoryVisibility}}</li>
</ul>
<p>Cordialement</p>
</body>
</html>
