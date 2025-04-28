<?php
require_once 'Database.class.php';

class MotLiaison {
    private $numMotLiaison;
    private $mot_exclu;
    
    public function __construct($mot_exclu = "") {
        $this->mot_exclu = $mot_exclu;
    }
    
    public function getNumMotLiaison() { return $this->numMotLiaison; }
    public function getMotExclu() { return $this->mot_exclu; }
    
    public function setNumMotLiaison($numMotLiaison) { $this->numMotLiaison = $numMotLiaison; }
    public function setMotExclu($mot_exclu) { $this->mot_exclu = $mot_exclu; }
    
    public function create() {
        $db = Database::getInstance()->getConnection();
        
        $query = "INSERT INTO Mot_liaison (mot_exclu) VALUES (?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([strtolower($this->mot_exclu)]);
        
        if ($result) {
            $this->numMotLiaison = $db->lastInsertId();
            return true;
        }
        return false;
    }
    
    public function update() {
        $db = Database::getInstance()->getConnection();
        
        $query = "UPDATE Mot_liaison SET mot_exclu = ? WHERE numMotLiaison = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([strtolower($this->mot_exclu), $this->numMotLiaison]);
    }
    
    public function delete() {
        $db = Database::getInstance()->getConnection();
        
        $query = "DELETE FROM Mot_liaison WHERE numMotLiaison = ?";
        $stmt = $db->prepare($query);
        return $stmt->execute([$this->numMotLiaison]);
    }
    
    public static function getById($numMotLiaison) {
        $db = Database::getInstance()->getConnection();
        
        $query = "SELECT * FROM Mot_liaison WHERE numMotLiaison = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$numMotLiaison]);
        
        if ($row = $stmt->fetch()) {
            $motLiaison = new MotLiaison();
            $motLiaison->setNumMotLiaison($row['numMotLiaison']);
            $motLiaison->setMotExclu($row['mot_exclu']);
            return $motLiaison;
        }
        return null;
    }
    
    public static function getAll() {
        $db = Database::getInstance()->getConnection();
        
        $query = "SELECT * FROM Mot_liaison ORDER BY mot_exclu";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $motsLiaison = [];
        while ($row = $stmt->fetch()) {
            $motLiaison = new MotLiaison();
            $motLiaison->setNumMotLiaison($row['numMotLiaison']);
            $motLiaison->setMotExclu($row['mot_exclu']);
            $motsLiaison[] = $motLiaison;
        }
        return $motsLiaison;
    }
}