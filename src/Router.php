<?php
include_once 'view/View.php';
include_once 'control/Controller.php';
class Router
{
    public function main($carStorage){
        session_start();
        $feedback = key_exists("feedback", $_SESSION) ? $_SESSION['feedback'] : "";
        $affiche = new View($this,$feedback);
        $_SESSION['feedback'] = "";
        $isConnected = !empty($_SESSION['user']);
        $controller = new Controller($affiche,$carStorage);
        if(array_key_exists("id",$_GET)){
            if($isConnected){
                $controller->showInformation($_GET["id"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("upload",$_GET)){
          $controller->uploadPage($_POST);
        }
        elseif(array_key_exists("liste",$_GET)){
            $controller->showList();
        }
        elseif(array_key_exists("debug",$_GET)){
          $controller->showDebugPage();
        }
        elseif(array_key_exists("action",$_GET)){
          if($isConnected){
              if($_GET["action"] == "nouveau"){
                  $controller->newCar();
              }
              elseif($_GET["action"] == "sauverNouveau"){
                  $controller->saveNewCar($_POST);
              }
              else{
                  $affiche->makeUnknownCarPage();
              }
          }
          else{
            $affiche->makeUnauthorizedPage();
          }
        }
        elseif(array_key_exists("demandeSupression",$_GET)){
            if($isConnected){
                $controller->askCarDeletion($_GET["demandeSupression"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("supression",$_GET)){
            if($isConnected){
                $controller->deleteCar($_GET["supression"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("demandeModification",$_GET)){
            if($isConnected){
                $controller->optionModification($_GET["demandeModification"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("modification",$_GET)){
            if($isConnected){
                $controller->modification($_GET["modification"],$_POST);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("propo",$_GET)){
            $affiche->makeAproposPage();
        }
        elseif (array_key_exists("login",$_GET)){
            $affiche->makeLoginFormPage();
        }
        elseif (array_key_exists("loginSend",$_GET)){
            $controller->login($_POST);
        }
        elseif(array_key_exists("disconnect",$_GET)){
            $controller->disconnection();
        }
        else{
            $controller->showWelcomPage();
        }
    }
    public function POSTredirect($url,$feedback){
        $_SESSION['feedback'] = $feedback;
        header("Location: ".htmlspecialchars_decode($url), true, 303);
        die;
    }
    //TODO faire en sorte que les liens soit du type /....
    public function getCarURL($id){
        return "?id=" . $id;
    }
    public function getCarCreationURL(){
      return "?action=nouveau";
    }
    public function getCarSaveURL(){
      return "?action=sauverNouveau";
    }
    public function getCarAskDeletionURL($id){
        return "?demandeSupression=" . $id;
    }
    public function getCarDeletionURL($id){
        return "?supression=" . $id;
    }
    public function getCarOptionModificationURL($id){
        return "?demandeModification=" . $id;
    }
    public function getCarModificationURL($id){
        return "?modification=" . $id;
    }
    public function getUploadUrl(){
      return "?upload";
    }
    public function getList(){
      return "?liste";
    }
    public function getAPropos(){
        return "?propo";
    }
    public function getLogin(){
        return "?login";
    }
    public function getLoginSend(){
        return "?loginSend";
    }
    public function getDisconnectUser(){
        return "?disconnect";
    }
}
