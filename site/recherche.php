<?php
require_once 'menu.php';
require_once '../class/Document.class.php';

$resultats = [];
$motsRecherche = '';
$documentDetails = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['recherche'])) {
        $motsRecherche = $_POST['recherche'];
        if (!empty($motsRecherche)) {
            $resultats = Document::rechercher($motsRecherche);
            if (empty($resultats)) {
            }
        }
    } elseif (isset($_POST['voir_document']) && isset($_POST['numDoc'])) {
        $documentDetails = Document::getById($_POST['numDoc']);
        $motsRecherche = isset($_POST['mots_recherche']) ? $_POST['mots_recherche'] : '';
        if (!empty($motsRecherche)) {
            $resultats = Document::rechercher($motsRecherche);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Documents</title>
    <link rel="stylesheet" href="../styles/minh_styles.css">
</head>
<body>
    <div class="container">
        <h1>Recherche de Documents</h1>
        <?php afficherMenu('recherche'); ?>
        
        
        
        <div class="content">
            <h2>Rechercher des documents par mots-clés</h2>
            
            <form method="post" action="recherche.php" class="form">
                <div class="form-group">
                    <label for="recherche">Mots-clés :</label>
                    <input type="text" id="recherche" name="recherche" value="<?php echo htmlspecialchars($motsRecherche); ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </form>
            
            <?php if (!empty($resultats)) : ?>
                <h2>Résultats de la recherche</h2>
                
                <div class="resultats">
                    <?php foreach ($resultats as $resultat) : ?>
                        <?php 
                            $doc = Document::getById($resultat['numDoc']);
                            $resume = Document::mettreEnEvidenceMotsCles($doc->getTexte(), $motsRecherche);
                        ?>
                        <div class="resultat-item">
                            <h3><?php echo $doc->getTitre(); ?> (ID: <?php echo $doc->getNumDoc(); ?>)</h3>
                            <div class="resume">
                                <?php echo $resume; ?>
                            </div>
                            <form method="post" action="recherche.php">
                                <input type="hidden" name="numDoc" value="<?php echo $doc->getNumDoc(); ?>">
                                <input type="hidden" name="mots_recherche" value="<?php echo htmlspecialchars($motsRecherche); ?>">
                                <button type="submit" name="voir_document" class="btn btn-small">Voir le document complet</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($documentDetails) : ?>
                <div class="document-details">
                    <h2>Document complet : <?php echo $documentDetails->getTitre(); ?></h2>
                    <p><strong>Date de création :</strong> <?php echo $documentDetails->getDateCreation(); ?></p>
                    <div class="document-texte">
                        <?php echo nl2br(htmlspecialchars($documentDetails->getTexte())); ?>
                    </div>
                    <a href="recherche.php" class="btn">Retour aux résultats</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>