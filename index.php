<?php
  //include './admin/index.php';
  // include './admin/admin.php';
  // include './admin/test.php';
  // echo 'http://'.$_SERVER["HTTP_HOST"].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]; 
  // echo $_SERVER["HTTP_HOST"];
  header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/index.php');
  exit();