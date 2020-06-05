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

  include_once 'function/activity_people.php';
  if($_GET['option']=='sign')
  {
    if(query_activity_sign_people($link,$_GET['act']))
    {
      $query = 'update peopleandactivity set status=\'2\' where peopleid=\''.$users['id'].'\' and activityid=\''.$_GET['act'].'\'';
      $result = execute_bool($link,$query);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/index.php?option=people&message=报名成功');
      exit();
    }
    else
    {
      $query = 'update peopleandactivity set status=\'1\' where peopleid=\''.$users['id'].'\' and activityid=\''.$_GET['act'].'\'';
      $result = execute_bool($link,$query);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/index.php?option=people&message=报名成功');
      exit();
    }

  }
  if($_GET['option']=='signout')
  {
    $query = 'update peopleandactivity set status=\'0\' where peopleid=\''.$users['id'].'\' and activityid=\''.$_GET['act'].'\'';
    $result = execute_bool($link,$query);
    header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/index.php?option=people&message=取消报名成功');
    exit();

  }
  if($_GET['option']=='join'&&$_GET['people'])
  {
    $query = 'select * from people where id=\''.$_GET['people'].'\'';
    $result = execute($link,$query);
    $People = $result[0];
    $query = 'select * from op where organization=\''.$People['organization'].'\'';
    $result = execute($link,$query);
    foreach ($result as $key => $value) {
      if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
      if($value['name']=='createPeople'){ $ops['createPeople']=$value;}
      if($value['name']=='delPeople'){ $ops['delPeople']=$value;}
    }
    if($users['op']<=$ops['createActivity']['op'])
    {
      $query = 'update peopleandactivity set status=\'2\' where peopleid=\''.$People['id'].'\' and activityid=\''.$_GET['act'].'\'';
      $result = execute_bool($link,$query);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=录取成功&url=/admin/activity_editier.php?act='.$_GET['act']);
      exit();
    }
    else
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
      exit();
    }

  }
  if($_GET['option']=='quitjoin'&&$_GET['people'])
  {
    $query = 'select * from people where id=\''.$_GET['people'].'\'';
    $result = execute($link,$query);
    $People = $result[0];
    $query = 'select * from op where organization=\''.$People['organization'].'\'';
    $result = execute($link,$query);
    foreach ($result as $key => $value) {
      if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
      if($value['name']=='createPeople'){ $ops['createPeople']=$value;}
      if($value['name']=='delPeople'){ $ops['delPeople']=$value;}
    }
    if($users['op']<=$ops['createActivity']['op'])
    {
      $query = 'update peopleandactivity set status=\'0\' where peopleid=\''.$People['id'].'\' and activityid=\''.$_GET['act'].'\'';
      $result = execute_bool($link,$query);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=取消录取成功&url=/admin/activity_editier.php?act='.$_GET['act']);
      exit();
    }
    else
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
      exit();
    }

  }
