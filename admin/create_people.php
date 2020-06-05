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
  $org = mysqli_real_escape_string($link,$users['organization']);
  $query = 'select * from organization where id=\''.$org.'\'';
  $result = execute($link,$query);
  $orgs = $result[0];
  $query = 'select * from op where organization=\''.$orgs['id'].'\' and name=\'createPeople\'';
  $result = execute($link,$query);
  $ops['createPeople']=$result[0];
  if($users['op']>$ops['createPeople']['op'])
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  if (isset($_GET['organization']))
  {
    if($users['op']<=1)
    {
      $users['organization']=$_GET['organization'];
    }
  }
  $_POST['organization']=$_GET['organization'];


  include_once './function/activity_people.php';
  $message = '';
  if(isset($_POST['user']))
  {
    // var_dump($_POST);
    if($_POST['user']=='')
    {
      $message='请填写用户名';
    }
    else
    {
      if(!preg_match("/^[A-Za-z0-9]+$/",$_POST['user']))
      {
        $message='用户名只能是数字和字母组成';
        $_POST['user']='';
      }
      else
      {
        $query_value = mysqli_real_escape_string($link,$_POST['user']);
        $query = 'select * from people where name= \''.$query_value . '\'';
        $result = execute($link,$query);
        if($result)
        {
          $message='用户名已存在';
          $_POST['user']='';
        }
        if(!$_POST['organizationID'])
        {
          $message='未填写组织内ID';
          $_POST['organizationID']='';
        }
        if($_POST['organization']!=0&&$_POST['user']!=''&&$_POST['organizationID'])
        {
          if(!preg_match("/^[A-Za-z0-9]+$/",$_POST['organizationID']))
          {
            $message='组织内ID只能是数字和字母';
            $_POST['organizationID']='';
          }
          else
          {
            $query_value = mysqli_real_escape_string($link,$_POST['organizationID']);
            $query = 'select * from people where organization=\''.$_POST['organization'].'\' and organizationID=\''.$query_value.'\'';
            $result = execute($link,$query);
            if($result)
            {
              $message='组织内ID已存在';
              $_POST['organizationID']='';
            }
          }
        }
        if(!$_POST['classname'])
        {
          $message='请填写昵称';
          $_POST['classname']='';
        }
        elseif(!preg_match("/^[A-Za-z0-9\x7f-\xff]+$/",$_POST['classname']))
        {
          $message='昵称只能是汉字和字母数字构成';
          $_POST['classname']='';
        }
      }
    }
    if($_POST['user']=='')
    {
      $_POST['user']='';
    }
    elseif($_POST['password1']=='')
    {
      $message='请输入密码';
    }
    elseif(!preg_match("/^[A-Za-z0-9@*&^%!#~`?]+$/",$_POST['password1']))
    {
      $message='密码存在非法字符，特殊字符只能是{@*&^%!#~`?}';
    }
    elseif($_POST['password1']!=$_POST['password2'])
    {
      $message='两次密码不一致';
    }
    elseif($_POST['classname']=='')
    {
      $_POST['classname']=='';
    }
    elseif($_POST['organizationID']=='')
    {
      $_POST['organizationID']='';
    }
    else
    {
      $name = mysqli_real_escape_string($link,$_POST['user']);
      $classname = mysqli_real_escape_string($link,$_POST['classname']);
      $password = mysqli_real_escape_string($link,$_POST['password1']);
      $organization = mysqli_real_escape_string($link,$_POST['organization']);
      $organizationID = mysqli_real_escape_string($link,$_POST['organizationID']);
      $query = 'INSERT INTO people (id,name,classname,passwd,organizationID,organization,op,credit) VALUES (NULL, \''.$name.'\',\''.$classname.'\',\''.$password.'\',\''.$organizationID.'\',\''.$organization.'\',\'4\',\'100\');';
      $result = execute_bool($link,$query);
      $query = 'select * from people where name=\''.$name.'\'';
      $result = execute($link,$query);
      if(!$result)
      {
        header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php?message=数据库异常');
        exit();
      }
      create_people($link, $result[0]);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php?message=注册成功');
      exit();
    }
  }
  header('Content-type:text/html;charset=utf-8'); 
?>
<!DOCTYPE html>
<html lang="zh_cn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/css/bootstrap.css">
  <link rel="stylesheet" href="../style/css/zui.css">
  <style>
    #main{
      position: absolute;
      width: 100%;
      height: 100%;
    }   
    #fromLogin{
      display: block;
      position: relative;
      top:20%;
    }
    #logo{
      display: block;
      margin-left: 38%;
      text-align: 50px;
    }
    #message{
      color: red;
    }
  </style>
  <title>添加人员</title>
</head>
<body>
<main id="mian">
  <form class="form-horizontal" id="fromLogin" method="post">
    <div class="form-group">
      <h1 id="logo">添加人员</h1>
    </div>
    <div class="form-group">
      <label for="exampleInputAccount9" class="col-sm-2 required">账号名</label>
      <div class="col-md-6 col-sm-10">
        <input type="text" class="form-control" id="exampleInputAccount9" name="user" placeholder="学号/用户名">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword4" class="col-sm-2 required" >密码</label>
      <div class="col-md-6 col-sm-10">
        <input type="password" class="form-control" id="exampleInputPassword4" name="password1" placeholder="密码">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword4" class="col-sm-2 required" >确认密码</label>
      <div class="col-md-6 col-sm-10">
        <input type="password" class="form-control" id="exampleInputPassword3" name="password2" placeholder="密码">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputAccount3" class="col-sm-2 required">昵称</label>
      <div class="col-md-6 col-sm-10">
        <input type="text" class="form-control" id="exampleInputAccount56" name="classname" placeholder="姓名/昵称">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword4" class="col-sm-2" >组织:</label>
      <div class="col-md-6 col-sm-10">
        <label for="exampleInputPassword5" class="col-sm-2" ><?php echo $orgs['name']; ?></label>
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputAccount9" class="col-sm-2 required">组织内ID</label>
      <div class="col-md-6 col-sm-10">
        <input type="text" class="form-control" id="exampleInputAccount3" name="organizationID" placeholder="学号/号数">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <div id="your-dom-id" class="nc-container"></div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
      <!-- 验证 -->
        <div id="message"><?php if($message){echo $message;} ?></div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default btn-primary">添加</button>
      </div>
    </div>
  </form>
</main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
</html>
<?php
  close_mysql($link);