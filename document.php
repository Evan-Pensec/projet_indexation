<?php
require_once 'menu.php';
require_once 'Document.class.php';

$message = '';
$document = new Document();
$mode = 'create';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if (isset($_POST['titre']) && isset($_POST['texte'])) {
            $titre = $_POST['titre'];
            $texte = $_POST['texte'];
            $dateCreation = date('Y-m-d');
            
            $document = new Document($titre, $texte, $dateCreation);
            
            if ($_POST['action'] === 'create') {
                if ($document->create()) {
                    $message = "Document ajouté avec succès !";
                    $document = new Document();
                } else {
                    $message = "Erreur lors de l'ajout du document.";
                }
            } elseif ($_POST['action'] === 'update' && isset($_POST['numDoc'])) {
                $document->setNumDoc($_POST['numDoc']);
                if ($document->update()) {
                    $message = "Document mis à jour avec succès !";
                    $mode = 'create';
                    $document = new Document();
                } else {
                    $message = "Erreur lors de la mise à jour du document.";
                }
            }
        }
    } elseif (isset($_POST['delete']) && isset($_POST['numDoc'])) {
        $docToDelete = Document::getById($_POST['numDoc']);
        if ($docToDelete && $docToDelete->delete()) {
            $message = "Document supprimé avec succès !";
        } else {
            $message = "Erreur lors de la suppression du document.";
        }
    } elseif (isset($_POST['edit']) && isset($_POST['numDoc'])) {
        $document = Document::getById($_POST['numDoc']);
        if ($document) {
            $mode = 'update';
        } else {
            $message = "Document non trouvé.";
        }
    }
}

$documents = Document::getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Documents</title>
    <link rel="stylesheet" href="minh_styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestion des Documents</h1>
        <?php afficherMenu('document'); ?>
        
        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="content">
            <h2><?php echo ($mode === 'create') ? 'Ajouter un nouveau document' : 'Modifier le document'; ?></h2>
            
            <form method="post" action="document.php" class="form">
                <input type="hidden" name="action" value="<?php echo $mode; ?>">
                <?php if ($mode === 'update') : ?>
                    <input type="hidden" name="numDoc" value="<?php echo $document->getNumDoc(); ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="titre">Titre :</label>
                    <input type="text" id="titre" name="titre" value="<?php echo $document->getTitre(); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="texte">Texte :</label>
                    <textarea id="texte" name="texte" rows="10" required><?php echo $document->getTexte(); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <?php echo ($mode === 'create') ? 'Ajouter' : 'Mettre à jour'; ?>
                    </button>
                    <?php if ($mode === 'update') : ?>
                        <a href="document.php" class="btn">Annuler</a>
                    <?php endif; ?>
                </div>
            </form>
            
            <h2>Liste des documents</h2>
            
            <?php if (empty($documents)) : ?>
                <p>Aucun document disponible</p>
            <?php else : ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documents as $doc) : ?>
                            <tr>
                                <td><?php echo $doc->getNumDoc(); ?></td>
                                <td><?php echo $doc->getTitre(); ?></td>
                                <td><?php echo $doc->getDateCreation(); ?></td>
                                <td>
                                    <form method="post" action="document.php" style="display:inline;">
                                        <input type="hidden" name="numDoc" value="<?php echo $doc->getNumDoc(); ?>">
                                        <button type="submit" name="edit" class="btn btn-small">Modifier</button>
                                        <button type="submit" name="delete" class="btn btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>