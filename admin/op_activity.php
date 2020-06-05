<?php
  include_once 'function/exists_login.php';//检测是否登录
  exists_login();
  include_once 'function/mysqli_mysql_connection.php';//数据库连接
  $link = link_mysql();
  $thisUrlRoot = 'http://' . $_SERVER["HTTP_HOST"];//获取链接
  date_default_timezone_set('Asia/Shanghai');
  $date_now = mysqli_real_escape_string($link,date('Y-m-d H:i:s'));//获取时间
  $user = mysqli_real_escape_string($link,$_SESSION['user']);
  $query = 'select * from people where name=\''.$user.'\'';
  $result = execute($link,$query);
  $users = $result[0];//获取用户信息
  include_once './function/activity_people.php';
?>
<?php
  if(isset($_GET['option']))
  {
    if($_GET['option']=='people')
    {
      $People = mysqli_real_escape_string($link,$_GET['people']);
      $query = 'select * from people where id=\''.$People.'\'';
      $result = execute($link,$query);
      $People = $result[0];//获取被操作用户信息
      $query = 'select * from op where organization=\''.$People['organization'].'\'';
      $result = execute($link,$query);
      foreach ($result as $key => $value) {
        if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
        if($value['name']=='createPeople'){ $ops['createPeople']=$value;}
        if($value['name']=='delPeople'){ $ops['delPeople']=$value;}
      }
      if($users['op']<$People['op'])
      {
        if($_GET['op']=='up')//提升权限
        {
          $People['op'] = $People['op']-1;
          if($users['op']<$People['op'])
          {
              $query = 'update people set op=\''. $People['op'] .'\' where id=\''.$People['id'].'\'';
              $result = execute_bool($link,$query);
              header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/admin.php?option=people&message=提升权限成功');
              exit();
          }
          else
          {
            header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/admin.php?option=people&message=权限不足');
            exit();
          }
        }
        if($_GET['op']=='down')//降低权限
        {
          $People['op'] = $People['op']+1;
          if($People['op']<=4)
          {
              $query = 'update people set op=\''. $People['op'] .'\' where id=\''.$People['id'].'\'';
              $result = execute_bool($link,$query);
              header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/admin.php?option=people&message=降低权限成功');
              exit();
          }
          else
          {
            header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/admin.php?option=people&message=已是最低权限');
            exit();
          }
        }
        if($_GET['op']=='del'&&$users['op']<=$ops['delPeople']['op'])//删除人员
        {
          $query = 'delete from people where id=\''.$People['id'].'\'';
          $result = execute_bool($link,$query);
          del_people($link,$People);
          header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=删除成功&url=/admin/admin.php?option=people');
          exit();
        }
        else
        {
          header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/admin.php?option=people&message=权限不足');
          exit();
        }


      }
      else
      {
        header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?url=/admin/admin.php?option=people&message=权限不足');
        exit();
      }
    }
    if($_GET['option']=='op')
    {
      $query = 'select * from op where id=\''.$_GET['id'].'\'';
      $result = execute($link,$query);
      $ops = $result[0];
      if($users['op']>$ops['op'])
      {
        header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=权限不足&url=/admin/admin.php?option=option');
        exit();
      }
      else
      {
        if($_GET['op']=='up')
        {
          if($users['op']==$ops['op'])
          {
            header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=权限不足&url=/admin/admin.php?option=option');
            exit();
          }
          $ops['op']-=1;
          $query = 'update op set op=\''. $ops['op'] .'\' where id=\''.$ops['id'].'\'';
          $result = execute_bool($link,$query);
          header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=提升成功&url=/admin/admin.php?option=option');
          exit();
        }
        if($_GET['op']=='down')
        {
          if($ops['op']==4)
          {
            header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=已是最低要求&url=/admin/admin.php?option=option');
            exit();
          }
          $ops['op']+=1;
          $query = 'update op set op=\''. $ops['op'] .'\' where id=\''.$ops['id'].'\'';
          $result = execute_bool($link,$query);
          header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=降低成功&url=/admin/admin.php?option=option');
          exit();
        }
      }
    }
  }
?>




<?php
  close_mysql($link);