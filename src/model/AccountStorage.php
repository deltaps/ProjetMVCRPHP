<?php

interface AccountStorage{
    public function checkAuth($login,$psw);
}