<?php
require_once 'Database.class.php';

class Document {
    private $numDoc;
    private $titre;
    private $texte;
    private $dateCreation;
    
    public function __construct($titre = "", $texte = "", $dateCreation = "") {
        $this->titre = $titre;
        $this->texte = $texte;
        $this->dateCreation = $dateCreation;
    }
    
    public function getNumDoc() { return $this->numDoc; }
    public function getTitre() { return $this->titre; }
    public function getTexte() { return $this->texte; }
    public function getDateCreation() { return $this->dateCreation; }
    
    public function setNumDoc($numDoc) { $this->numDoc = $numDoc; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setTexte($texte) { $this->texte = $texte; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; }
    
    public function create() {
        $db = Database::getInstance()->getConnection();
        
        $query = "INSERT INTO Documents (titre, texte, dateCreation) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$this->titre, $this->texte, $this->dateCreation]);
        
        if ($result) {
            $this->numDoc = $db->lastInsertId();
            $this->indexerDocument();
            return true;
        }
        return false;
    }
    
    public function update() {
        $db = Database::getInstance()->getConnection();
        
        $query = "UPDATE Documents SET titre = ?, texte = ?, dateCreation = ? WHERE numDoc = ?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$this->titre, $this->texte, $this->dateCreation, $this->numDoc]);
        
        if ($result) {
            $this->supprimerIndexation();
            $this->indexerDocument();
            return true;
        }
        return false;
    }
    
    public function delete() {
        $db = Database::getInstance()->getConnection();
        
        $this->supprimerIndexation();
        
        $query = "DELETE FROM Documents WHERE numDoc = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$this->numDoc]);
    }
    
    public static function getById($numDoc) {
        $db = Database::getInstance()->getConnection();
        
        $query = "SELECT * FROM Documents WHERE numDoc = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$numDoc]);
        
        if ($row = $stmt->fetch()) {
            $document = new Document();
            $document->setNumDoc($row['numDoc']);
            $document->setTitre($row['titre']);
            $document->setTexte($row['texte']);
            $document->setDateCreation($row['dateCreation']);
            return $document;
        }
        return null;
    }
    
    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        
        $query = "SELECT * FROM Documents ORDER BY dateCreation DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $documents = [];
        while ($row = $stmt->fetch()) {
            $document = new Document();
            $document->setNumDoc($row['numDoc']);
            $document->setTitre($row['titre']);
            $document->setTexte($row['texte']);
            $document->setDateCreation($row['dateCreation']);
            $documents[] = $document;
        }
        return $documents;
    }
    
    private function indexerDocument() {
        $db = Database::getInstance()->getConnection();
        
        $query = "SELECT mot_exclu FROM Mot_liaison";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $motsLiaison = [];
        while ($row = $stmt->fetch()) {
            $motsLiaison[] = strtolower($row['mot_exclu']);
        }
        
        // pour découper le texte en mots
        $texte = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $this->texte);
        $mots = preg_split('/\s+/', strtolower($texte));
        
        $compteurMots = [];
        foreach ($mots as $mot) {
            $mot = trim($mot);
            if (!empty($mot) && !in_array($mot, $motsLiaison) && strlen($mot) > 1) {
                if (isset($compteurMots[$mot])) {
                    $compteurMots[$mot]++;
                } else {
                    $compteurMots[$mot] = 1;
                }
            }
        }
        
        foreach ($compteurMots as $mot => $nombre) {
            $query = "INSERT INTO Indexation (mot_cle, numDoc, nombre_Mot) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$mot, $this->numDoc, $nombre]);
        }
    }
    
    private function supprimerIndexation() {
        $db = Database::getInstance()->getConnection();
        
        $query = "DELETE FROM Indexation WHERE numDoc = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$this->numDoc]);
    }
    
    public static function rechercher($motsRecherche) {
        $db = Database::getInstance()->getConnection();
        
        $mots = preg_split('/\s+/', strtolower($motsRecherche));
        
        $mots = array_filter($mots, function($mot) {
            return !empty(trim($mot));
        });
        
        if (empty($mots)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($mots), '?'));
        $query = "SELECT DISTINCT d.numDoc, d.titre 
                FROM Documents d 
                JOIN Indexation i ON d.numDoc = i.numDoc 
                WHERE i.mot_cle IN ($placeholders) 
                ORDER BY d.numDoc";
        
        $stmt = $db->prepare($query);
        $stmt->execute($mots);
        
        $resultats = [];
        while ($row = $stmt->fetch()) {
            $resultats[] = $row;
        }
        
        return $resultats;
    }
    
    public static function mettreEnEvidenceMotsCles($texte, $motsRecherche) {
        $mots = preg_split('/\s+/', strtolower($motsRecherche));
        $mots = array_filter($mots, function($mot) {
            return !empty(trim($mot));
        });
        
        if (empty($mots)) {
            return $texte;
        }
        
        $resume = "";
        $texteOriginal = $texte;
        $texteMinuscule = strtolower($texte);
        
        foreach ($mots as $mot) {
            $pos = 0;
            while (($pos = strpos($texteMinuscule, $mot, $pos)) !== false) {
                $debut = max(0, $pos - 30);
                $fin = min(strlen($texteOriginal), $pos + strlen($mot) + 30);
                
                while ($debut > 0 && $texteOriginal[$debut] != ' ') {
                    $debut--;
                }
                while ($fin < strlen($texteOriginal) && $texteOriginal[$fin] != ' ') {
                    $fin++;
                }
                
                $segment = substr($texteOriginal, $debut, $fin - $debut);
                
                $motOriginal = substr($texteOriginal, $pos, strlen($mot));
                $segmentMisEnEvidence = str_replace($motOriginal, "<strong>$motOriginal</strong>", $segment);
                
                $resume .= "..." . $segmentMisEnEvidence . "... <br>";
                
                $pos += strlen($mot);
            }
        }
        
        return $resume;
    }
}