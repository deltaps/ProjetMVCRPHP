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

    public function __construct($view,$carStorage){
        $this->view = $view;
        $this->carStorage = $carStorage;
        $this->accountStorage = new AccountStorageStub();
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
    public function showList(){
        $this->view->makeListPage($this->carStorage->readAll());
    }
    public function showDebugPage(){
      $test = "medor";
      $this->view->makeDebugPage($test);
    }
    public function saveNewCar($data){
      $carBuilder = new CarBuilder($data);
      if($carBuilder->isValid()){
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
        $this->view->displayCarCreationSuccess($myKey);
      }
      else{
        $_SESSION['currentNewCar'] = $carBuilder;
        $this->view->makeCarCreationPage($carBuilder);
      }
    }
    public function isOwner($id){
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
            $this->showList();
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

    public function uploadPage($data){
      if (move_uploaded_file($_FILES['pj']['tmp_name'], "./img/uploadedFile.png")){
        $this->view->makeWelcomPage();
      }
      else {
        $this->view->makeErrorPage("L'upload n'a pas fonctionnée");
      }
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
        if($this->accountStorage->checkAuth($data['login'],$data['password'])){
            $this->view->makeWelcomPage();
        }
        else{
            $this->view->makeLoginErrorPage();
        }
    }

    public function disconnection(){
        $this->accountStorage->disconnect();
        $this->view->makeWelcomPage();
    }

}
