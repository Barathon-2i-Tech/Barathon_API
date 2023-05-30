<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Validation de votre événement</title>
</head>
<body>
Cher(e) {{$user->first_name}} {{Str::upper($user->last_name)}},

<p>Nous sommes heureux de vous informer que votre événement "{{ $event->event_name }}" a été validé avec succès et est
désormais accessible à tous les utilisateurs de notre plateforme. Nous vous remercions pour votre confiance et votre
engagement.</p>

<p>Nous sommes convaincus que votre événement apportera une réelle valeur ajoutée à notre communauté et nous sommes
impatients de voir les utilisateurs profiter de votre événement.</p>

<p>Nous tenons à vous rappeler que nous prenons très au sérieux la qualité et la pertinence des événements référencés sur
notre plateforme. Nous surveillons en permanence les activités suspectes et les commentaires des utilisateurs, pour
garantir une expérience optimale à tous les utilisateurs.</p>

<p>Nous vous souhaitons une excellente expérience sur notre plateforme et sommes convaincus que votre événement sera un
véritable succès.</p>

Cordialement,<br/>
L'équipe de Barathon

</body>
</html>
