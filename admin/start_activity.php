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

  include_once './function/activity_people.php';

  if($_GET['option']=='join'&&$_GET['act'])
  {
    $passwd = get_start_passwd($link,$_GET['act']);
    if($passwd==$_POST['passwd'])
    {
      set_start($link,$_GET['act'],$users['id']);
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=签到成功');
      exit();
    }
    else
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=签到码错误');
      exit();
    }
  }
  if($_GET['option']=='updatapasswd'&&$_GET['act'])
  {
    update_start_passwd($link,$_GET['act']);
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=更新成功&url=/admin/activity_editier.php?act='.$_GET['act']);
    exit();
  }
  if($_GET['option']=='startopjoin'&&$_GET['act']&&$_GET['people'])
  {
    $query = 'select * from people where id=\''.$_GET['people'].'\'';
    $result = execute($link,$query);
    $People = $result[0];
    // var_dump($People);
    $query = 'select * from op where organization=\''.$People['organization'].'\'';
    $result = execute($link,$query);
    foreach ($result as $key => $value) {
      if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
      if($value['name']=='createPeople'){ $ops['createPeople']=$value;}
      if($value['name']=='delPeople'){ $ops['delPeople']=$value;}
    }
    if($users['op']<=$ops['createActivity']['op'])
    {
      set_start($link,$_GET['act'],$People['id']);
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=强制签到成功&url=/admin/activity_editier.php?act='.$_GET['act']);
      exit();
    }
    else
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
      exit();
    }
  }