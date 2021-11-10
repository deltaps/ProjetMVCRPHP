<?php
include_once 'model/Car.php';
include_once 'model/CarStorage.php';
include_once 'model/CarBuilder.php';
include_once 'model/AccountStorageStub.php';
include_once 'model/AccountStorage.php';
class Controller{
    protected $view;
    protected $carStorage;
    protected $accountStorage;

    public function __construct($view,$carStorage,$accountStorage){
        $this->view = $view;
        $this->carStorage = $carStorage;
        $this->accountStorage = $accountStorage;
    }

    public function showInformation($id){
        $car = $this->carStorage->read($id);
        if($car != null){
            $this->view->makeCarPage($car,$id);
        }
        else{
            $this->view->makeUnknownCarPage();
        }
    }
    public function showWelcomPage(){
        $this->view->makeWelcomPage();
    }
    public function showList($page){
        $allCar = $this->carStorage->readAll();
        $carList = array();
        $compteur = 0;
        foreach ($allCar as $key => $car){
            if($compteur > $page*5+5-1){// A changer pour changer la pagination
                break;
            }
            if($compteur >= $page*5){// A changer pour changer la pagination
                $carList[$key] = $car;
            }
            $compteur++;
        }
        $nbPage = sizeof($allCar) / 5;
        //var_dump($nbPage);
        $this->view->makeListPage($carList,$nbPage);
    }
    public function showDebugPage(){
      $test = "medor";
      $this->view->makeDebugPage($test);
    }
    public function saveNewCar($data){
      if($data != null){
          $carBuilder = new CarBuilder($data);
          if($carBuilder->isValid()){
              unset($_SESSION['currentNewCar']);
              $car = $carBuilder->createCar();
              $this->carStorage->create($car);
              $compt = 0;
              $myKey = 0;
              foreach ($this->carStorage->readAll() as $key => $value) {
                  if($compt == count($this->carStorage->readAll())-1){
                      $myKey = $key;
                  }
                  $compt++;
              }
              $this->view->makeCarImageAdd($myKey);
          }
          else{
              $_SESSION['currentNewCar'] = $carBuilder;
              $this->view->makeCarCreationPage($carBuilder);
          }
      }
      else{
          $this->view->makeWelcomPage();
      }
    }
    public function isOwner($id){
        if($_SESSION['user']->getStatus() === "admin"){
            return true;
        }
        return $this->carStorage->isOwner($id) == $_SESSION['user']->getNom();
    }

    public function askCarDeletion($id){
        if($this->carStorage->read($id) != null){
            if($this->isOwner($id)){
                $this->view->makeAskSupressionPage($id);
            }
            else{
                $this->view->makeUnauthorizedPage();
            }
        }
        else{
            $this->view->makeErrorPage("La voiture n'existe pas");
        }
    }
    public function deleteCar($id){
        if($this->isOwner($id)){
            $this->carStorage->delete($id);
            if(file_exists("./img/" . $id . "/")){
                $compt = 0;
                while(true){
                    if(file_exists("./img/" . $id . "/" . $compt . ".png")){
                        unlink("./img/" . $id . "/" . $compt . ".png");
                        $compt++;
                    }
                    else{
                        break;
                    }
                }
                rmdir("./img/".$id);
            }
            /*
            if(file_exists("./img/" . $id . ".png")){
                unlink("./img/" . $id . ".png");
            }
            */
            $this->view->displayCarSupressionSuccess();
        }
        else{
            $this->view->makeUnauthorizedPage();
        }
    }
    public function optionModification($id){
        if($this->carStorage->read($id) != null){
            if($this->isOwner($id)){
                $liste = array("name" => $this->carStorage->read($id)->getName() ,
                    "brand" => $this->carStorage->read($id)->getBrand() ,
                    "horsePower" => $this->carStorage->read($id)->getHorsePower() ,
                    "torque" => $this->carStorage->read($id)->getTorque() ,
                    "year" => $this->carStorage->read($id)->getYear());
                $carBuilder = new CarBuilder($liste);
                $this->view->makeCarModificationPage($id,$carBuilder,false);
            }
            else{
                $this->view->makeUnauthorizedPage();
            }
        }
        else{
            $this->view->makeErrorPage("La voiture n'existe pas");
        }
    }
    public function modification($id,$data){
        if($this->isOwner($id)){
            $carBuilder = new CarBuilder($data);
            if($carBuilder->isValid()){
                $car = $carBuilder->createCar();
                $this->carStorage->modify($id,$car);
                $this->view->makeCarPage($car,$id);
            }
            else{
                $this->view->makeCarModificationPage($id,$carBuilder,true);
            }
        }
        else{
            $this->view->makeUnauthorizedPage();
        }
    }
    public function optionImageModification($id){
        if($this->carStorage->read($id) != null){
            if($this->isOwner($id)){
                $this->view->makeCarImageModification($id);
            }
            else{
                $this->view->makeUnauthorizedPage();
            }
        }
        else{
            $this->view->makeErrorPage("La voiture n'existe pas");
        }
    }
    public function optionImageSupression($id){
        if($this->carStorage->read($id) != null){
            if($this->isOwner($id)){
                $this->view->makeCarImageSupression($id);
            }
            else{
                $this->view->makeUnauthorizedPage();
            }
        }
        else{
            $this->view->makeErrorPage("La voiture n'existe pas");
        }
    }
    public function imageSupression($id,$image){
        if($this->carStorage->read($id) != null){
            if($this->isOwner($id)){
                if(file_exists("./img/" . $id . "/" . $image . ".png")){
                    unlink("./img/" . $id . "/" . $image . ".png");
                }
                $this->view->makeCarPage($this->carStorage->read($id),$id);
            }
            else{
                $this->view->makeUnauthorizedPage();
            }
        }
        else{
            $this->view->makeErrorPage("La voiture n'existe pas");
        }
    }

    public function uploadPage($id){
        if(!file_exists("./img/" . $id . "/")){
            mkdir("img/" . $id . "/", 0777,true);
            $compt = 0;
        }
        else{
            $fi = new FilesystemIterator("./img/" . $id . "/", FilesystemIterator::SKIP_DOTS); // C'est deux ligne de code on été trouvé sur internet, elle permettent de compter le nombre d'image que possède le dossier.
            $compt = iterator_count($fi);
        }
        foreach($_FILES['pj']['tmp_name'] as $name){
            move_uploaded_file($name, "./img/". $id . "/" . $compt . ".png");
            $compt++;
        }
        $this->view->displayCarCreationSuccess($id);
        /*
      if (move_uploaded_file($_FILES['pj']['tmp_name'], "./img/". $id .".png")){
          $this->view->displayCarCreationSuccess($id);
      }
      else {
          if(file_exists("./img/" . $id . ".png")){
              unlink("./img/" . $id . ".png");
          }
          $this->view->displayCarCreationSuccess($id);
      }
        */
    }

    public function newCar(){
      if(key_exists('currentNewCar',$_SESSION)){
        $this->view->makeCarCreationPage($_SESSION['currentNewCar']);
      }
      else{
        $carBuilder = new CarBuilder(null);
        $this->view->makeCarCreationPage($carBuilder);
      }
    }

    public function login($data){
        if($data != null){
            if($this->accountStorage->checkAuth($data['login'],$data['password'])){
                $this->view->makeWelcomPage();
            }
            else{
                $this->view->makeLoginErrorPage("Erreur, votre login ou passsword est incorect");
            }
        }
        else{
            $this->view->makeWelcomPage();
        }
    }

    public function disconnection(){
        $this->accountStorage->disconnect();
        $this->view->makeWelcomPage();
    }

    public function creationAccount($data){
        if($data != null){
            $loginAlreadyTaken = false;
            foreach ($this->accountStorage->getTableauCompte() as $compte) {
                if($compte->getLogin() === $data['login']){
                    $this->view->makeCreationAccountPage("le login appartient déjà a quelqu'un");
                    $loginAlreadyTaken = true;
                }
            }
            if(!$loginAlreadyTaken){
                if($data['login'] != "" && $data['password'] != ""){
                    $this->accountStorage->creationAccount($data);
                    $this->view->makeLoginErrorPage("Votre compte a été crée avec succées, veuillé vous connecter");
                }
                else{
                    $this->view->makeCreationAccountPage("il faut que les deux champs sois remplie");
                }
            }
        }
        else{
            $this->view->makeWelcomPage();
        }
    }
    public function modificationAccount($login,$data){
        $allAccount = $this->accountStorage->getTableauCompte();
        foreach ($allAccount as $compte) {
            if($compte->getLogin() === $login){
                $vraieCompte = $compte;
            }
        }
        $newAccount = new Account($vraieCompte->getLogin(),$vraieCompte->getLogin(),$vraieCompte->getMDP(),$data['status']);
        $this->accountStorage->modifyAccount($newAccount);
        $this->view->makeModificationAccountPage($login,$newAccount);
    }
    public function supressionAccount($login){
        $this->accountStorage->deleteAccount($login);
        $this->view->displayAccountSupressionSuccess();
        //$this->view->makeListModificationAccountPage($this->accountStorage);
    }
}
