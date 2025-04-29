<?php
function afficherMenu($pageActive = '') {
    echo '<nav class="menu">
        <ul>
            <li><a href="document.php" class="'.($pageActive == 'document' ? 'active' : '').'">Gestion des documents</a></li>
            <li><a href="mot_liaison.php" class="'.($pageActive == 'mot_liaison' ? 'active' : '').'">Gestion des mots de liaison</a></li>
            <li><a href="recherche.php" class="'.($pageActive == 'recherche' ? 'active' : '').'">Recherche</a></li>
        </ul>
    </nav>';
}