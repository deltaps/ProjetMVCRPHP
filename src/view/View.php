<?php
include_once 'model/Car.php';
class View{
    private $title;
    private $content;
    private $router;
    private $menu;
    private $feedback;

    public function __construct($router,$feedback){
        $this->router = $router;
        $this->menu = array('accueil' => '?', 'liste' => $this->router->getList(), 'creationObjet' => $this->router->getCarCreationURL(), 'propos' => $this->router->getAPropos(), 'connexion' => $this->router->getLogin(), 'deconnexion' => $this->router->getDisconnectUser());
        $this->feedback = $feedback;
    }

    public function render(){
        echo("
        <!doctype html>
        <html lang=\"fr\">
            <head>
              <meta charset=\"utf-8\">
              <title>". $this->title ."</title>
            </head>
            <body>
                <nav>
                <ul>");
          foreach ($this->menu as $key => $value) {
              if(empty($_SESSION['user'])){
                  if($key === 'creationObjet'){
                      continue;
                  }
                  if($key === "deconnexion"){
                      continue;
                  }
              }
              else{
                  if($key === 'connexion'){
                      continue;
                  }
              }
            echo("<li>");
            echo("<a href=" . $value . ">". $key . "</a>");
            echo("</li>");
          }
         echo("
                </ul>
                </nav>
                <h1>". $this->title ."</h1>
                " . $this->content . "
                <style type='text/css'>
                  img{
                    width:10em;
                    margin-left:90%;
                  }
                </style>
                <img src='./img/uploadedFile.png'>
            </body>
        </html>
        ");
    }

    public function makeTestPage(){
        $this->title = "Titre quelconque";
        $this->content = "<p>Bonjour je ne sai spas ce que je fais ici, mais au moins je suis afficher</p>";
        $this->render();
    }

    public function makeCarPage($car,$id){
        $this->title = "Page sur " . $car->getName();
        $this->content = $car->getName() . " est une voiture de la marque " . $car->getBrand() . "
        elle possède " . $car->getHorsePower() . " chevaux, " . $car->getTorque() . " de torque, et elle a été faite en " . $car->getYear();
        $this->content .= "<form method='POST' action=". $this->router->getCarAskDeletionURL($id) .">
                            <button type='submit'>Supprimer cette voiture </button>
                            </form>";
        $this->content .= "<form method='POST' action=". $this->router->getCarOptionModificationURL($id) .">
                            <button type='submit'>Modifier cette voiture </button>
                            </form>";
        $this->render();
    }

    public function makeUnknownCarPage(){
        $this->title = "Voiture inconnue";
        $this->content = "Voiture inconnue";
        $this->render();
    }

    public function makeWelcomPage(){
        $this->title = "Bienvenue sur le site";
        $this->content = "Vous pouvez rechercher une voiture";
        $this->content = $this->content . "<form enctype='multipart/form-data' action=" . $this->router->getUploadUrl() . "
                                    method='POST'>

      <input type='file' name='pj'>

      <button type='submit'>Valider</button>

      </form>";
        $this->render();
    }

    public function makeListPage($tableauVoitures){
        $this->title = "Liste de tout les voitures";
        $this->content = "<ul>";
        foreach ($tableauVoitures as $id => $voiture){
          $this->content = $this->content . "<li><a href='" . $this->router->getCarURL($id) . "'>" . $voiture->getName() . "</a></li>";
        }
        $this->content = $this->content . "</ul>";
        $this->render();
    }
    public function makeDebugPage($variable) {
	      $this->title = 'Debug';
	      $this->content = '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
        $this->render();
    }
    public function makeErrorPage($error){
        $this->title = "Erreur";
        $this->content = "<p>" . $error . "</p>";
        $this->render();
    }
    public function makeCarCreationPage(CarBuilder $carBuilder){
      if($carBuilder->getData() === null){
        $this->title = "Ajout d'une voiture";
        $this->content = "<form method='POST' action=". $this->router->getCarSaveURL().">
            <div>
                <label for='name'>Nom :</label>
                <input type='text' id='name' name='name'>
            </div>
            <div>
                <label for='brand'>marque:</label>
                <input type='text' id='brand' name='brand'>
            </div>
            <div>
                <label for='horsePower'>Chevaux :</label>
                <input type='text' id='horsePower' name='horsePower'>
            </div>
            <div>
                <label for='torque'>Couple :</label>
                <input type='text' id='torque' name='torque'>
            </div>
            <div>
                <label for='year'>Année :</label>
                <input type='text' id='year' name='year'>
            </div>
            <div>
              <button type='submit'>Envoyer </button>
            </div>
            </form>";
          $this->render();
      }
      else{
        $this->title = "Ajout d'une voiture";
        $error = $carBuilder->getError();
        $data = $carBuilder->getData();
        $this->content = "<form method='POST' action=". $this->router->getCarSaveURL().">
              <p> Il y a une, ou plusieurs erreurs</p>
            <div>
                <label for='name'>Nom :</label>
                <input type='text' id='name' name='name' value=". $data["name"] .">
                 ". $error["name"] ."
            </div>
            <div>
                <label for='brand'>Marque :</label>
                <input type='text' id='brand' name='brand' value=". $data["brand"] .">
                ". $error["brand"] ."
            </div>
            <div>
                <label for='horsePower'>Chevaux :</label>
                <input type='text' id='horsePower' name='horsePower' value=". $data["horsePower"] .">
                ". $error["horsePower"] ."
            </div>
            <div>
                <label for='torque'>Couple :</label>
                <input type='text' id='torque' name='torque' value=". $data["torque"] .">
                ". $error["torque"] ."
            </div>
            <div>
                <label for='year'>Année :</label>
                <input type='text' id='year' name='year' value=". $data["year"] .">
                ". $error["year"] ."
            </div>
            <div>
              <button type='submit'>Envoyer </button>
            </div>
            </form>";
          $this->render();
      }
    }
    public function makeAskSupressionPage($id){
        $this->title = "Voulez-vous vraiment supprimer?";
        $this->content = "<p>êtes vous sûr de vouloir supprimer la voiture possédant l'id : ". $id . "?</p>";
        $this->content .= "<form method='POST' action=". $this->router->getCarDeletionURL($id) .">
                            <button type='submit'>Supprimer la voiture </button>";
        $this->render();
    }
    public function makeCarModificationPage($id, CarBuilder $carBuilder, $already){
        $this->title = "Modification de la voiture";
        if(!$already){
            $data = $carBuilder->getData();
            $this->content = "<form method='POST' action=". $this->router->getCarModificationURL($id).">
            <div>
                <label for='name'>Nom :</label>
                <input type='text' id='name' name='name' value=". $data["name"] .">
            </div>
            <div>
                <label for='brand'>Marque :</label>
                <input type='text' id='brand' name='brand' value=". $data["brand"] .">
            </div>
            <div>
                <label for='horsePower'>Chevaux :</label>
                <input type='text' id='horsePower' name='horsePower' value=". $data["horsePower"] .">
            </div>
            <div>
                <label for='torque'>Couple :</label>
                <input type='text' id='torque' name='torque' value=". $data["torque"] .">
            </div>
            <div>
                <label for='year'>Année :</label>
                <input type='text' id='year' name='year' value=". $data["year"] .">
            </div>
            <div>
              <button type='submit'>Envoyer </button>
            </div>
            </form>";
            $this->render();
        }
        else{
            $error = $carBuilder->getError();
            $data = $carBuilder->getData();
            $this->content = "<form method='POST' action=". $this->router->getCarModificationURL($id).">
              <p> Il y a une ou plusieurs erreurs </p>
            <div>
                <label for='name'>Nom :</label>
                <input type='text' id='name' name='name' value=". $data["name"] .">
                 ". $error["name"] ."
            </div>
            <div>
                <label for='brand'>Marque :</label>
                <input type='text' id='brand' name='brand' value=". $data["brand"] .">
                ". $error["brand"] ."
            </div>
            <div>
                <label for='horsePower'>Chevaux :</label>
                <input type='text' id='horsePower' name='horsePower' value=". $data["horsePower"] .">
                ". $error["horsePower"] ."
            </div>
            <div>
                <label for='torque'>Couple :</label>
                <input type='text' id='torque' name='torque' value=". $data["torque"] .">
                ". $error["torque"] ."
            </div>
            <div>
                <label for='year'>Année :</label>
                <input type='text' id='year' name='year' value=". $data["year"] .">
                ". $error["year"] ."
            </div>
            <div>
              <button type='submit'>Envoyer </button>
            </div>
            </form>";
            $this->render();
        }
    }
    public function makeAproposPage(){
        $this->title = "A propos";
        $this->content = "<p> PRONOST Sacha, Numéro étudiant :<strong> 21901956 </strong> Groupe : <strong>4B</strong> </p>
        <p> SIEPKA Aurélien, Numéro étudiant : <strong>21906664</strong> Groupe : <strong>4A</strong></p>
        ";
        $this->render();
    }
    public function makeLoginFormPage(){
        $this->title = "Connexion";
        $this->content = "<form method='POST' action=". $this->router->getLoginSend().">
        <label>Nom : <input type='text' name='login' /></label>
        <label>Mot de passe : <input type='password' name='password' /></label>
        <button>Se connecter</button>
        </form>";
        $this->render();
    }
    public function makeLoginErrorPage(){
        $this->title = "Connexion";
        $this->content = "<p>Erreur, votre login ou passsword est incorect</p>
        <form method='POST' action=". $this->router->getLoginSend().">
        <label>Nom : <input type='text' name='login' /></label>
        <label>Mot de passe : <input type='password' name='password' /></label>
        <button>Se connecter</button>
        </form>";
        $this->render();
    }
    public function makeUnauthorizedPage(){
        $this->title = "Accées non autorisé";
        $this->content = "<p>Vous ne pouvez pas accéder a cette page avec votre status actuelle</p>";
        $this->render();
    }
    public function displayCarCreationSuccess($id){
      $url = $this->router->getCarURL($id);
      $this->router->POSTredirect($url,"rien pour le moment");
    }
    public function displayCarCreationFailure(){
      $url = $this->router->getCarCreationURL();
      header('Location:' . $url);
      //TODO exercice 5 du tp 16
    }
}
