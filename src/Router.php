<?php
include_once 'view/View.php';
include_once 'control/Controller.php';
class Router{

    public function main($carStorage,$accountStorage){
        session_start();
        $feedback = key_exists("feedback", $_SESSION) ? $_SESSION['feedback'] : "";
        $affiche = new View($this,$feedback);
        $_SESSION['feedback'] = "";
        $isConnected = !empty($_SESSION['user']);
        $controller = new Controller($affiche,$carStorage,$accountStorage);
        if(array_key_exists("id",$_GET)){
            if($isConnected){
                $controller->showInformation($_GET["id"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("upload",$_GET)){
          $controller->uploadPage($_GET["upload"]);
        }
        elseif(array_key_exists("liste",$_GET)){
            $controller->showList($_GET["liste"]);
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
        elseif(array_key_exists("demandeImageModification",$_GET)){
            if($isConnected){
                $controller->optionImageModification($_GET["demandeImageModification"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("demandeImageSupression",$_GET)){
            if($isConnected){
                $controller->optionImageSupression($_GET["demandeImageSupression"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("demandeImageNumSupression",$_GET)){
            if($isConnected){
                $controller->imageSupression($_GET["demandeImageNumSupression"],$_GET["image"]);
            }
            else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("demandeImageAjout",$_GET)){
            if($isConnected){
                $affiche->makeCarImageAdd($_GET["demandeImageAjout"]);
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
        elseif(array_key_exists("creationAccount",$_GET)){
            $affiche->makeCreationAccountPage("");
        }
        elseif(array_key_exists("accountSend",$_GET)){
            $controller->creationAccount($_POST);
        }
        elseif(array_key_exists("menuModificationAccount",$_GET)){
            if($isConnected){
                if($_SESSION['user']->getStatus() === "admin"){
                    $affiche->makeListModificationAccountPage($accountStorage);
                }
                else{
                    $affiche->makeUnauthorizedPage();
                }
            }else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("modificationAccount",$_GET)){
            if($isConnected){
                if($_SESSION['user']->getStatus() === "admin"){
                    foreach ($accountStorage->getTableauCompte() as $compte) {
                        if($compte->getLogin() === $_GET["modificationAccount"]){
                            $vraieCompte = $compte;
                            break;
                        }
                    }
                    $affiche->makeModificationAccountPage($_GET["modificationAccount"],$vraieCompte);
                }
                else{
                    $affiche->makeUnauthorizedPage();
                }
            }else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("applyModificationAccount",$_GET)){
            if($isConnected){
                if($_SESSION['user']->getStatus() === "admin"){
                    $controller->modificationAccount($_GET["applyModificationAccount"],$_POST);
                }
                else{
                    $affiche->makeUnauthorizedPage();
                }
            }else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif(array_key_exists("demandeSupressionCompte",$_GET)){
            if($isConnected){
                if($_SESSION['user']->getStatus() === "admin"){
                    $affiche->makeAskDeletionAccountPage($_GET["demandeSupressionCompte"]);
                }
                else{
                    $affiche->makeUnauthorizedPage();
                }
            }else{
                $affiche->makeUnauthorizedPage();
            }
        }
        elseif (array_key_exists("supressionCompte",$_GET)){
            if($isConnected){
                if($_SESSION['user']->getStatus() === "admin"){
                    $controller->supressionAccount($_GET["supressionCompte"]);
                }
                else{
                    $affiche->makeUnauthorizedPage();
                }
            }else{
                $affiche->makeUnauthorizedPage();
            }
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
    public function getCarOptionImageModificationURL($id){
        return "?demandeImageModification=" . $id;
    }
    public function getCarSupressionImageURL($id){
        return "?demandeImageSupression=" . $id;
    }
    public function getCarSupressionAskImageURL($id,$image){
        return "?demandeImageNumSupression=" . $id . "&image=" . $image;
    }
    public function getCarAddImageURL($id){
        return "?demandeImageAjout=" . $id;
    }
    public function getUploadUrl($id){
      return "?upload=" . $id;
    }
    public function getList($page){
      return "?liste=" . $page;
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
    public function getCreationAccount(){
        return "?creationAccount";
    }
    public function getAccountSend(){
        return "?accountSend";
    }
    public function getMenuModificationAccount(){
        return "?menuModificationAccount";
    }
    public function getModificationAccount($id){
        return "?modificationAccount=" . $id;
    }
    public function getApplyModificationAccount($id){
        return "?applyModificationAccount=" . $id;
    }
    public function getAccountAskDeletionURL($id){
        return "?demandeSupressionCompte=" . $id;
    }
    public function getAccountDeletionURL($id){
        return "?supressionCompte=" . $id;
    }
}
