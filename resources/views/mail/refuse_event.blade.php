<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Refus de votre événement</title>
</head>
<body>
Cher(e) {{Str::upper($user->last_name)}} {{$user->first_name}},

Nous sommes au regret de vous informer que nous avons identifié un problème lié à votre événement
"{{ $event->event_name }}", qui empêche sa validation et son activation sur notre plateforme. Nous vous prions de nous
excuser pour la gêne occasionnée.

Nous vous invitons à vous connecter à notre plateforme pour vérifier les informations relatives à votre événement. Nous
vous suggérons de vérifier que toutes les informations que vous avez fournies sont correctes et complètes. Si vous avez
besoin d'aide ou si vous avez des questions, n'hésitez pas à contacter notre équipe de support.

Nous tenons à vous rappeler que nous attachons une grande importance à la qualité et à la pertinence des événements
référencés sur notre plateforme. Nous sommes convaincus que vous partagez cet engagement et que vous ferez tout votre
possible pour résoudre les problèmes que nous avons identifiés.

Nous espérons que vous pourrez rapidement corriger les problèmes liés à votre événement, afin que nous puissions le
valider et le rendre accessible à nos utilisateurs.

Cordialement,
L'équipe de Barathon

</body>
</html>
