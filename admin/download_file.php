<?php
  include_once 'function/exists_login.php';
  exists_login();
  include_once 'function/mysqli_mysql_connection.php';
  $link = link_mysql();
  $thisUrlRoot = 'http://' . $_SERVER["HTTP_HOST"];
  date_default_timezone_set('Asia/Shanghai');
  $date_now = mysqli_real_escape_string($link,date('Y-m-d H:i:s'));
  $users = mysqli_real_escape_string($link,$_SESSION['user']);
  $query = 'select * from people where name=\''.$users.'\'';
  $result = execute($link,$query);
  $users = $result[0];

  if(!isset($_GET['id'])||$_GET['id']=='')
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }


  $query = 'select * from file where id='.$_GET['id'];
  $result = execute($link,$query);
  if(!$result)
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=找不到你要的文件');
    exit();
  }
  $files = $result[0];
  $file = $files['path'];
  header('Content-type:'.$files['type']); 
  header('Content-Disposition:attachement;filename='.$files['name']);
  header('Content-Length:'.$files['size']);
  readfile($file);