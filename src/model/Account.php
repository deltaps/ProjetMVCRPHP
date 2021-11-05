<?php

class Account{
    protected $nom;
    protected $login;
    protected $mdp;
    protected $status;

    public function __construct($nom, $login, $mdp, $status)
    {
        $this->nom = $nom;
        $this->login = $login;
        $this->mdp = $mdp;
        $this->status = $status;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function getStatus()
    {
        return $this->status;
    }

}