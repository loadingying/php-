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
  if($_GET['id']!=$users['id'])
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  $org = mysqli_real_escape_string($link,$users['organization']);
  $query = 'select * from organization where id=\''.$org.'\'';
  $result = execute($link,$query);
  $orgs = $result[0];
  $people=$users;
  if($people['op']==0) {$op_name = '超级管理员';}
  elseif($people['op']==1) {$op_name = '副管理员';}
  elseif($people['op']==2) {$op_name = '组织管理员';}
  elseif($people['op']==3) {$op_name = '组织副管理员';}
  elseif($people['op']==4) {$op_name = '普通成员';}
  if(isset($_POST['classname']))
  {
    if($_POST['classname']==''){ $message='昵称不为空';}
    elseif(!preg_match("/^[A-Za-z0-9\x7f-\xff]+$/",$_POST['classname'])){ $message='昵称只能是汉字和字母数字构成';}
    elseif($_POST['classname']==''){ $message='组织内ID不为空';}
    elseif(!preg_match("/^[A-Za-z0-9]+$/",$_POST['organizationID'])){ $message='组织内ID只能是数字和字母';}
    else
    {
      $classname = mysqli_real_escape_string($link,$_POST['classname']);
      $organizationID = mysqli_real_escape_string($link,$_POST['organizationID']);
      $query = 'update people set classname=\''. $classname .'\' where id=\''.$users['id'].'\'';
      $result = execute_bool($link,$query);
      $query = 'update people set organizationID=\''. $organizationID .'\' where id=\''.$users['id'].'\'';
      $result = execute_bool($link,$query);
      header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php' . '?message=修改成功');
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
  <title>个人中心</title>
  <style>
  #main{
    padding-bottom: 30px;
    background-color: #F1F1F1;
  }
  #header{
    background-color: #DDDDDD;
  }
  #logo{
    padding: 10px 40px;
  }
  #user{
    padding: 10px 40px;
    float: right; 
  }
  #meaasges{
    width: 80%;
    margin-left: auto;
    margin-right: auto;
  }
  #footer
  {
    float: right;
    padding: 2px 40px;
  }
  #message{
    color: red;
    display:block;
    padding:2px 20px;
  }
  </style>
</head>
<body>
  <main id="main" class="container">
    <header id="header" class="row">
      <div class="col-3">
        <div id="logo">
        <h2>个人中心</h2>
        </div>
      </div>
      <div class="col-9">
        <div id="user">
        <h2>你好，<?php echo $users['classname']; ?></h2>
        </div>
      </div>
    </header>
    <form id="fromed" method="post">
      <div id="main-body" class="row">
        <div id="meaasges">
          <div class="list-group">
            <a class="list-group-item">昵称:&nbsp&nbsp&nbsp&nbsp
              <input type="text" class="form-control" id="calssname" name="classname">
            </a>
            <a class="list-group-item">组织内ID:&nbsp&nbsp&nbsp&nbsp
              <input type="text" class="form-control" id="organizationID" name="organizationID">
            </a>
          </div>
        </div>
      </div>
      <footer id="footer" class="row">
        <div id="message"><?php if($message){echo $message;} ?></div>
        <button type="submit" class="btn">确认修改</button>
      </footer>
  </from>
  </main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script>
  document.getElementById('calssname').value="<?php echo $users['classname']; ?>";
  document.getElementById('organizationID').value="<?php echo $users['organizationID']; ?>";
</script>
</html>
<?php
  close_mysql($link);