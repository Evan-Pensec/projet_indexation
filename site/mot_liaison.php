<?php
require_once 'menu.php';
require_once '../class/MotLiaison.class.php';

$message = '';
$motLiaison = new MotLiaison();
$mode = 'create';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if (isset($_POST['mot_exclu'])) {
            $mot_exclu = $_POST['mot_exclu'];
            
            $motLiaison = new MotLiaison($mot_exclu);
            
            if ($_POST['action'] === 'create') {
                if ($motLiaison->create()) {
                    $motLiaison = new MotLiaison();
                }
            } elseif ($_POST['action'] === 'update' && isset($_POST['numMotLiaison'])) {
                $motLiaison->setNumMotLiaison($_POST['numMotLiaison']);
                if ($motLiaison->update()) {
                    $mode = 'create';
                    $motLiaison = new MotLiaison();
                }
            }
        }
    } elseif (isset($_POST['delete']) && isset($_POST['numMotLiaison'])) {
        $motToDelete = MotLiaison::getById($_POST['numMotLiaison']);
        
    } elseif (isset($_POST['edit']) && isset($_POST['numMotLiaison'])) {
        $motLiaison = MotLiaison::getById($_POST['numMotLiaison']);
        if ($motLiaison) {
            $mode = 'update';
        }
    }
}

$motsLiaison = MotLiaison::getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Mots-clés</title>
    <link rel="stylesheet" href="../styles/minh_styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestion des Mots-clés</h1>
        <?php afficherMenu('mot_liaison'); ?>
        
        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="content">
            <h2><?php echo ($mode === 'create') ? 'Exclure un mot de liaison' : 'Modifier le mot de liaison'; ?></h2>
            
            <form method="post" action="mot_liaison.php" class="form">
                <input type="hidden" name="action" value="<?php echo $mode; ?>">
                <?php if ($mode === 'update') : ?>
                    <input type="hidden" name="numMotLiaison" value="<?php echo $motLiaison->getNumMotLiaison(); ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="mot_exclu">Mot à exclure :</label>
                    <input type="text" id="mot_exclu" name="mot_exclu" value="<?php echo $motLiaison->getMotExclu(); ?>" required>
                </div>
                
                <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <?php echo ($mode === 'create') ? 'Exclure' : 'Mettre à jour'; ?>
                </button>
                <?php if ($mode === 'update') : ?>
                    <a href="mot_liaison.php" class="btn">Annuler</a>
                <?php endif; ?>
                </div>
            </form>
            
            <h2>Liste des mots de liaison</h2>
            
            <?php if (empty($motsLiaison)) : ?>
                <p>Aucun mot de liaison disponible</p>
            <?php else : ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Mots exclus</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($motsLiaison as $mot) : ?>
                            <tr>
                                <td><?php echo $mot->getNumMotLiaison(); ?></td>
                                <td><?php echo $mot->getMotExclu(); ?></td>
                                <td>
                                    <form method="post" action="mot_liaison.php" style="display:inline;">
                                        <input type="hidden" name="numMotLiaison" value="<?php echo $mot->getNumMotLiaison(); ?>">
                                        <button type="submit" name="edit" class="btn btn-small">Modifier</button>
                                        <button type="submit" name="delete" class="btn btn-small btn-danger">Supprimer</button>
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