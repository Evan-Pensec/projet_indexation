<?php
require_once 'menu.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système d'Indexation de Documents</title>
    <link rel="stylesheet" href="minh_styles.css">
</head>
<body>
    <div class="container">
        <h1>Système d'Indexation de Documents</h1>
        <?php afficherMenu(); ?>
        
        <div class="content">
            <h2>Bienvenue dans le système d'indexation de documents</h2>
            <p>Cette application permet de :</p>
            <ul>
                <li>Gérer une base de documents textuels</li>
                <li>Indexer automatiquement les mots-clés</li>
                <li>Exclure certains mots de l'indexation</li>
                <li>Rechercher des documents par mots-clés</li>
            </ul>
            <p>Utilisez le menu ci-dessus pour naviguer entre les différentes fonctionnalités.</p>
        </div>
    </div>
</body>
</html>