<?php
  include_once 'function/exists_login.php';
  exists_login();
  outlogin();
  $thisUrlRoot = 'http://' . $_SERVER["HTTP_HOST"];
  header('Content-type:text/html;charset=utf-8');
?>
<!DOCTYPE html>
<html lang="zh_cn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登出成功</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/css/bootstrap.css">
  <link rel="stylesheet" href="../style/css/zui.css">
  <meta http-equiv="refresh" content="2; url='<?php
    echo $thisUrlRoot;
  ?>'">
  <style>
  #main{
    width:80%;
    margin:10% 10% 10% 10%;
  }
  </style>
</head>
<body>
  <main id="main">
    <div class="alert alert-success">登出成功</div>
  </main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
</html>