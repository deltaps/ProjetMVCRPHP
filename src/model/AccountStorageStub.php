<?php
require_once ('AccountStorage.php');
require_once ('Account.php');
require_once ('AuthentificationManager.php');
class AccountStorageStub implements AccountStorage{

    private $tableauCompte;
    private $authManager;

    public function __construct(){
        $mdpVanier = password_hash("toto", PASSWORD_BCRYPT);
        $this->tableauCompte = array(1 => new Account("vanier","vanier",$mdpVanier,"admin"));
        $this->authManager = new AuthentificationManager($this->tableauCompte);
    }

    public function checkAuth($login, $psw){
        return $this->authManager->connectUser($login,$psw);
    }

    public function disconnect(){
        $this->authManager->disconnectUser();
    }
}