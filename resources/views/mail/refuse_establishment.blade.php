<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Refus de votre établissement</title>
</head>
<body>
Cher(e) {{Str::upper($user->last_name)}} {{$user->first_name}},

<p>Nous vous informons que nous avons rencontré un problème lors de la vérification de votre établissement
"{{ $establishment->trade_name }}", qui empêche l'activation de celui-ci. Nous vous prions de nous excuser pour la gêne
occasionnée.</p>

<p>Nous vous invitons à vous connecter à notre plateforme pour vérifier les informations relatives à votre établissement.
Veuillez vérifier que toutes les informations que vous avez fournies sont correctes et complètes. Si vous avez besoin
d'aide ou si vous avez des questions, n'hésitez pas à contacter notre équipe de support.</p>

<p>Nous souhaitons rappeler que nous prenons très au sérieux la qualité des établissements que nous référençons, afin de
garantir une expérience optimale à nos utilisateurs. Nous travaillons en permanence à améliorer nos processus de
vérification pour nous assurer que les établissements référencés répondent à nos critères de qualité.</p>

<p>Nous espérons que vous pourrez résoudre rapidement les problèmes que nous avons identifiés, afin que nous puissions
valider votre établissement et le rendre accessible à nos utilisateurs.</p>

Cordialement,</br>
L'équipe de Barathon

</body>
</html>
