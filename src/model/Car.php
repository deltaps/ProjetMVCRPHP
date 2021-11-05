<?php

class Car{
    protected $name;
    protected $brand;
    protected $horsePower;
    protected $torque;
    protected $year;
    protected $owner;

    public function __construct($name, $brand, $horsePower, $torque, $year)
    {
        $this->name = $name;
        $this->brand = $brand;
        $this->horsePower = $horsePower;
        $this->torque = $torque;
        $this->year = $year;
        $this->owner = null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function getHorsePower()
    {
        return $this->horsePower;
    }

    public function getTorque()
    {
        return $this->torque;
    }

    public function getYear()
    {
        return $this->year;
    }

}