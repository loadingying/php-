<?php
  function exists_login()
  {
    session_start();
    if(isset($_SESSION['login'])&&$_SESSION['login']==true)
    {
      return true;
    }
    header('Location: login.php');
    exit();
  }
  function outlogin()
  {
    session_start();
    session_destroy();
  }
