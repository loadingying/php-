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
  //被查看人的信息
  $people = mysqli_real_escape_string($link,$_GET['id']);
  $query = 'select * from people where id=\''.$people.'\'';
  $result = execute($link,$query);
  $people = $result[0];
  $org = mysqli_real_escape_string($link,$people['organization']);
  $query = 'select * from organization where id=\''.$org.'\'';
  $result = execute($link,$query);
  $orgs = $result[0];
  if($people['op']==0) {$op_name = '超级管理员';}
  elseif($people['op']==1) {$op_name = '副管理员';}
  elseif($people['op']==2) {$op_name = '组织管理员';}
  elseif($people['op']==3) {$op_name = '组织副管理员';}
  elseif($people['op']==4) {$op_name = '普通成员';}
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
  #meaasge{
    width: 80%;
    margin-left: auto;
    margin-right: auto;
  }
  #footer
  {
    float: right;
    padding: 2px 40px;
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
    <div id="main-body" class="row">
      <div id="meaasge">
        <div class="list-group">
          <a class="list-group-item">昵称:&nbsp&nbsp&nbsp&nbsp<strong><?php echo $people['classname']; ?></strong></a>
<?php 
  if($people['id']==$users['id'])
  {
    echo '<a class="list-group-item">账户名:&nbsp&nbsp&nbsp&nbsp<strong>';
    echo $people['name'];
    echo '</strong></a>';
  }
?>
          <a class="list-group-item">所属组织:&nbsp&nbsp&nbsp&nbsp<strong><?php echo $orgs['name']; ?></strong></a>
          <a class="list-group-item">组织内ID:&nbsp&nbsp&nbsp&nbsp<strong><?php echo $people['organizationID']; ?></strong></a>
          <a class="list-group-item">职务:&nbsp&nbsp&nbsp&nbsp<strong><?php echo $op_name; ?></strong></a>
          <a class="list-group-item">信用:&nbsp&nbsp&nbsp&nbsp<strong><?php echo $people['credit']; ?></strong></a>
        </div>
      </div>
    </div>
<?php 
  if($people['id']==$users['id'])
  { echo "<footer id=\"footer\" class=\"row\">
      <a ";
    echo "href=\"".$thisUrlRoot."/admin/person_editier.php?id=".$people['id']."\"";
    echo "class=\"btn\" >编辑</a>
    </footer>";
  }
?>
  </main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
</html>
<?php
  close_mysql($link);