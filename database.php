<?php
$connexion = new PDO("mysql:host=localhost", "root", "");
$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $connexion->exec("CREATE DATABASE IF NOT EXISTS exo_minh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de données créée avec succès<br>";
    
    $connexion->exec("USE exo_minh");
    
    $connexion->exec("CREATE TABLE IF NOT EXISTS Documents (
        numDoc INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        texte TEXT NOT NULL,
        dateCreation DATE NOT NULL
    )");
    echo "Table Documents créée avec succès<br>";
    
    $connexion->exec("CREATE TABLE IF NOT EXISTS Mot_liaison (
        numMotLiaison INT AUTO_INCREMENT PRIMARY KEY,
        mot_exclu VARCHAR(50) NOT NULL
    )");
    echo "Table Mot_liaison créée avec succès<br>";
    
    $connexion->exec("CREATE TABLE IF NOT EXISTS Indexation (
        numIndex INT AUTO_INCREMENT PRIMARY KEY,
        mot_cle VARCHAR(100) NOT NULL,
        numDoc INT NOT NULL,
        nombre_Mot INT NOT NULL,
        FOREIGN KEY (numDoc) REFERENCES Documents(numDoc) ON DELETE CASCADE,
        INDEX (mot_cle)
    )");
    
    $connexion->exec("INSERT INTO Mot_liaison (mot_exclu) VALUES 
        ('le'), ('la'), ('les'), ('un'), ('une'), ('des'), ('du'), ('au'), ('aux'),
        ('et'), ('ou'), ('à'), ('de'), ('dans'), ('par'), ('pour'), ('en'), ('sur'),
        ('avec'), ('sans'), ('ce'), ('cette'), ('ces'), ('mon'), ('ma'), ('mes'),
        ('ton'), ('ta'), ('tes'), ('son'), ('sa'), ('ses'), ('notre'), ('nos'),
        ('votre'), ('vos'), ('leur'), ('leurs')");
    
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}