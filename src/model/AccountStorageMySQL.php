<?php

class AccountStorageMySQL implements AccountStorage{

    private $authManager;
    private $tableauCompte;
    private $bd;

    public function __construct($bd){
        $this->bd = $bd;
        $requete = "SELECT Name,Login,Password,Status FROM comptes";
        $response = $this->bd->query($requete);
        $this->tableauCompte = array();
        foreach ($response->fetchALL() as $listeCompte) {
            $compte = new Account($listeCompte["Name"],$listeCompte["Login"],$listeCompte["Password"],$listeCompte["Status"]);
            array_push($this->tableauCompte,$compte);
        }
        $this->authManager = new AuthentificationManager($this->tableauCompte);
    }


    public function checkAuth($login, $psw){
        return $this->authManager->connectUser($login,$psw);
    }

    public function disconnect(){
        $this->authManager->disconnectUser();
    }

    public function creationAccount($data){
        $hash = password_hash($data["password"], PASSWORD_BCRYPT);
        $requete = "INSERT INTO comptes VALUES (:name,:login,:password,:status)";
        $stmt = $this->bd->prepare($requete);
        $data = array(':name' => $data["login"], ':login' => $data["login"], ':password' => $hash, ':status' => "none");
        $stmt->execute($data);
    }

    public function getTableauCompte(){
        return $this->tableauCompte;
    }
}