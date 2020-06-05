<?php 
  session_start();
  $thisUrlRoot = 'http://' . $_SERVER["HTTP_HOST"];
  if(isset($_SESSION['login']))
  {
     if($_SESSION['login']==true)
     {
       if(isset($_GET['url']))
        {
          header('Location: '. $thisUrlRoot .'/admin/message.php?message=您已登录&url=' . $_GET['url']);
          exit();
        }
        else
        {
          header('Location: '. $thisUrlRoot .'/admin/message.php?message=您已登录');
          exit();
        }
     }
  }
  $message = '';
  if(isset($_POST['user']))
  {
    include_once 'function/mysqli_mysql_connection.php';
    $link = link_mysql();
    $user = mysqli_real_escape_string($link,$_POST['user']);
    $query = 'select * from people where name=\''.$user.'\'';
    $result = execute($link,$query);
    if(!$result)
    {
      $message ='用户名不存在';
    }
    else
    {
      $result = $result[0];
      if($result['passwd']!=$_POST['password'])
      {
        $message ='密码错误';
      }
      else
      {
        $message = '登陆成功';
        $_SESSION['login']=true;
        $_SESSION['user']=$_POST['user'];
        if(isset($_GET['url']))
        {
          header('Location: ' . $thisUrlRoot );
        }
        else
        {
          header('Location: ' . $thisUrlRoot . $_GET['url'] );
        }
       
      }
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
  <link rel="stylesheet" href="../style/css/login.css">
  <title>登录</title>
</head>
<body>
  <main id="main">
  <form class="form-horizontal" id="fromLogin" method="post">
    <div class="form-group">
    <h1 id="logo">登录</h1>
  </div>
  <div class="form-group">
    <label for="exampleInputAccount9" class="col-sm-2 required">账号</label>
    <div class="col-md-6 col-sm-10">
      <input type="text" class="form-control" id="exampleInputAccount9" name="user" placeholder="学号/用户名">
    </div>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword4" class="col-sm-2 required" >密码</label>
    <div class="col-md-6 col-sm-10">
      <input type="password" class="form-control" id="exampleInputPassword4" name="password" placeholder="密码">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <div id="message"><?php if($message){echo $message;} ?></div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default btn-primary">登录</button>
      <a id="registered_a" href="<?php echo $thisUrlRoot . '/admin/registered.php';?>" class="btn btn-primary" >去注册</a>
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
  if(isset($_POST['user']))
  {
  close_mysql($link);
  }