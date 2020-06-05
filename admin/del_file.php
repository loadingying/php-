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
  if(!isset($_GET['act'])||$_GET['act']=='')
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  $query = 'select * from activity where id=\''.$_GET['act'].'\'';
  $result = execute($link,$query);
  $act = $result[0];
  $query = 'select * from op where organization=\''.$act['organization'].'\'';
    $result = execute($link,$query);
    foreach ($result as $key => $value) {
      if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
      if($value['name']=='createPeople'){ $ops['createPeople']=$value;}
      if($value['name']=='delPeople'){ $ops['delPeople']=$value;}
    }

  if($users['op']<=$ops['createActivity']['op'])
  {
    $query = 'select * from file where id='.$_GET['id'];
    $result = execute($link,$query);
    if(!$result)
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=找不到你要的文件');
      exit();
    }
    $files = $result[0];
    if($act['file'])
    {
      $filese = explode(";",$act['file']);
      $newfile = '';
      foreach($filese as $value)
      {
        if($value)
        {
          if($value==$files['id'])
          {
            unlink($files['path']);
            $query = 'delete from file where id=\''.$files['id'].'\'';
            $result = execute_bool($link,$query);
          }
          else
          {
            $newfile .= $value.';';
          }
        }
      }
      $query = 'update activity set file=\''.$newfile.'\' where id=\''.$act['id'].'\'';
      $result = execute_bool($link,$query);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=删除成功&url=/admin/activity_editier.php?act='.$_GET['act']);
      exit();
    }
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=异常错误');
    exit();

  }
  else
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }