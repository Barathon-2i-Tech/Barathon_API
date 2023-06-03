<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validation de votre établissement</title>
</head>
<body>
Cher(e) {{$user->first_name}} {{Str::upper($user->last_name)}},

<p>Nous sommes heureux de vous informer que votre établissement "{{ $establishment->trade_name }}" a été validé avec succès
et est désormais accessible à nos utilisateurs. Nous vous remercions pour votre engagement et votre collaboration.</p>

<p>Vous pouvez désormais créer des événements pour votre établissement et profiter pleinement de toutes les fonctionnalités
de notre application.</p>

<p>Nous vous rappelons que nous prenons très au sérieux la qualité et la pertinence des établissements référencés sur notre
plateforme. Nous surveillons en permanence les activités suspectes et les commentaires des utilisateurs, pour garantir
une expérience optimale à tous les utilisateurs.</p>

<p>Nous vous souhaitons une excellente expérience sur notre plateforme et sommes convaincus que votre établissement sera un
ajout précieux à notre communauté.</p>

Cordialement,<br/>
L'équipe de Barathon

</body>
</html>
