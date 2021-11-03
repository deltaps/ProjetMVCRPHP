<?php
class CarBuilder{
  protected $data;
  protected $error;
  protected $id;

  const NAME_REF = "nom";
  const SPECIES_REF = "espece";
  const AGE_REF = "age";

  public function __construct($data){
    $this->data = $data;
    $this->error = array("name" => "", "brand" => "", "horsePower" => "", "torque" => "", "year" => "");
  }

  public function getData(){
    return $this->data;
  }
  public function getError(){
    return $this->error;
  }
  public function createCar(){
    return new Car($this->data["name"],$this->data["brand"],$this->data["horsePower"],$this->data["torque"],$this->data["year"]);
  }
  public function isValid(){
    $test = true;
    if($this->data["name"] === ""){
      $this->error["name"] = "Votre voiture doit avoir un nom";
      $test = false;
    }
    if($this->data["brand"] === ""){
      $this->error["brand"] = "Votre voiture doit avoir une marque";
      $test = false;
    }
    if($this->data["horsePower"] <= 0){
      $this->error["horsePower"] = "Votre voiture doit avoir un nombre de chevaux valide";
      $test = false;
    }
    if($this->data["torque"] <= 0){
      $this->error["torque"] = "Votre voiture doit avoir un couple valide";
      $test = false;
    }
    if($this->data["year"] <= 0){
      $this->error["year"] = "Votre voiture doit avoir une année valide";
      $test = false;
    }
    return $test;
  }
}

 ?>
