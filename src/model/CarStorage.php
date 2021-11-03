<?php

interface CarStorage{
    public function read($id);
    public function readAll();
    public function create(Car $a);
    public function delete($id);
}
