<?php
require_once ('AccountStorage.php');
require_once ('Account.php');
require_once ('AuthentificationManager.php');
class AccountStorageStub implements AccountStorage{

    private $tableauCompte;
    private $authManager;

    public function __construct(){
        $this->tableauCompte = array(1 => new Account("vanier","vanier",'$2y$10$GKeIBRG94GC6QpyxMKikeeGa/rNoepk2bzWAyM9knOmbvNWO36.tO',"admin"),
            2 => new Account("lecarpentier","lecarpentier",'$2y$10$GKeIBRG94GC6QpyxMKikeeGa/rNoepk2bzWAyM9knOmbvNWO36.tO',"admin"));
        $this->authManager = new AuthentificationManager($this->tableauCompte);
    }

    public function checkAuth($login, $psw){
        return $this->authManager->connectUser($login,$psw);
    }

    public function disconnect(){
        $this->authManager->disconnectUser();
    }
}