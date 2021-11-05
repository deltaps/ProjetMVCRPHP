<?php
class CarStorageMySQL implements CarStorage{

  protected $bd;

  public function __construct($bd){
    $this->bd = $bd;
  }

  public function read($id){
    $requete = "SELECT name,brand,horsePower,torque,year FROM voitures WHERE id = ". $id . ";";
    $response = $this->bd->query($requete);
    if($response != null){
      $allResponse = $response->fetchALL();
      $car = new Car($allResponse[0]["name"],$allResponse[0]["brand"],$allResponse[0]["horsePower"],$allResponse[0]["torque"],$allResponse[0]["year"]);
      return $car;
    }
  }

  public function isOwner($id){
    $requete = "SELECT name,brand,horsePower,torque,year,owner FROM voitures WHERE id = ". $id . ";";
    $response = $this->bd->query($requete);
    if($response != null){
      $allResponse = $response->fetchALL();
      return $allResponse[0]["owner"];
    }
  }

  public function readAll(){
    $requete = "SELECT id,name,brand,horsePower,torque,year FROM voitures";
    $response = $this->bd->query($requete);
    $array = array();
    foreach ($response->fetchALL() as $listeCar) {
      $car = new Car($listeCar["name"],$listeCar["brand"],$listeCar["horsePower"],$listeCar["torque"],$listeCar["year"]);
      $array[$listeCar["id"]] = $car;
    }
    return $array;
  }

  public function create(Car $a){
    $allCar = $this->bd->query("SELECT id,name,brand,horsePower,torque,year FROM voitures");
    $allCar = $allCar->fetchALL();
    $idMax = "0";
    foreach ($allCar as $listeCar){
      if($listeCar["id"] > $idMax){
        $idMax = $listeCar["id"];
      }
    }
    $idPossble = $idMax++;
    $idPossble+= 2;
    $requete = "INSERT INTO voitures VALUES (:id,:name,:brand,:horsePower,:torque,:year,:owner)";
    $stmt = $this->bd->prepare($requete);
    $data = array(':id' => $idPossble, ':name' => $a->getName(), ':brand' => $a->getBrand(), ':horsePower' => $a->getHorsePower(), ':torque' => $a->getTorque(), ':year' => $a->getYear(), ':owner' => $_SESSION['user']->getNom());
    $stmt->execute($data);
  }

  public function delete($id){
    $requete = "DELETE FROM voitures WHERE id=:id";
    $stmt = $this->bd->prepare($requete);
    $data = array(':id' => $id);
    $stmt->execute($data);
  }

  public function modify($id,$a){
    $this->delete($id);
    $requete = "INSERT INTO voitures VALUES (:id,:name,:brand,:horsePower,:torque,:year)";
    $stmt = $this->bd->prepare($requete);
    $data = array(':id' => $id, ':name' => $a->getName(), ':brand' => $a->getBrand(), ':horsePower' => $a->getHorsePower(), ':torque' => $a->getTorque(), ':year' => $a->getYear());
    $stmt->execute($data);
  }
}
 ?>
