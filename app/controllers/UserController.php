<?php
//app\controllers\UserController.php

require_once 'app/models/User.php'; 
class UserController
{
  public function mostrar()
  {
    $userModel = new User;
    echo $userModel->mostrarAlgo();
  }
}
