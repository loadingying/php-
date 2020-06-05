<?php
  include_once './function/exists_login.php';
  exists_login();
  include_once './function/mysqli_mysql_connection.php';
  $link = link_mysql();
  $thisUrlRoot = 'http://' . $_SERVER["HTTP_HOST"];
  date_default_timezone_set('Asia/Shanghai');
  $date_now = mysqli_real_escape_string($link,date('Y-m-d H:i:s'));
  $users = mysqli_real_escape_string($link,$_SESSION['user']);
  $query = 'select * from people where name=\''.$users.'\'';
  $result = execute($link,$query);
  $users = $result[0];
  if($users['op']>=2)
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  include_once './function/activity_people.php';
  if($_GET['option']=='edi'&&$_GET['org'])
  {
    if(!$_POST['name'])
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=组织名不能为空&url=/admin/org_op.php');
      exit();
    }
    elseif(!preg_match("/^[A-Za-z0-9\x7f-\xff]+$/",$_POST['name']))
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=组织名不合法&url=/admin/org_op.php');
      exit();
    }
    else
    {
      $name = mysqli_real_escape_string($link,$_POST['name']);
      $query = 'select * from organization where name=\''.$name.'\'';
      $result = execute($link,$query);
      if($result)
      {
        header('Location: '. $thisUrlRoot .'/admin/message.php?message=组织名已存在&url=/admin/org_op.php');
        exit();
      }
      $query = 'update organization set name=\''.$name.'\' where id='.$_GET['org'];
      execute_bool($link,$query);
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=更新成功&url=/admin/org_op.php');
      exit();
    }
  }
  if($_GET['option']=='new')
  {
    if(!$_POST['name'])
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=组织名不能为空&url=/admin/org_op.php');
      exit();
    }
    elseif(!preg_match("/^[A-Za-z0-9\x7f-\xff]+$/",$_POST['name']))
    {
      header('Location: '. $thisUrlRoot .'/admin/message.php?message=组织名不合法&url=/admin/org_op.php');
      exit();
    }
    else
    {
      $name = mysqli_real_escape_string($link,$_POST['name']);
      $query = 'select * from organization where name=\''.$name.'\'';
      $result = execute($link,$query);
      if($result)
      {
        header('Location: '. $thisUrlRoot .'/admin/message.php?message=组织名已存在&url=/admin/org_op.php');
        exit();
      }
      $query = 'insert into organization(id,name,sort) VALUES (NULL,\''.$name.'\', \'0\')';
      execute_bool($link,$query);
      $query = 'select * from organization where name=\''.$name.'\'';
      $result = execute($link,$query);
      $orgs = $result[0];
      $query = 'insert into op (id,organization,name,op,sort) VALUES (NULL,'.$orgs['id'].',\'createActivity\', 3, \'0\'),(NULL,'.$orgs['id'].',\'createPeople\', 3, \'0\'),(NULL,'.$orgs['id'].',\'delPeople\', 3, \'0\')';
      execute_bool($link,$query);

      header('Location: '. $thisUrlRoot .'/admin/message.php?message=创建成功&url=/admin/org_op.php');
      exit();
    }
  }
  if($_GET['option']=='del'&&$_GET['id'])
  {
    //先删除人员和报名状态表
    $query = 'select * from people where organization=\''.$_GET['id'].'\'';
    $results = execute($link,$query);
    foreach($results as $value)
    {
      $query = "DELETE FROM people where id='".$value['id']."'";
      $result = execute_bool($link,$query);
      del_people($link,$value);
    }
    //删除op表
    $query = "DELETE FROM op where organization='".$_GET['id']."'";
    $result = execute_bool($link,$query);
    //删除活动
    $query = 'select * from activity where organization=\''.$_GET['id'].'\'';
    $results = execute($link,$query);
     foreach($results as $values)
    {
      del_activity($link,$values['id']);
      $query = 'delete from activityoption where activity=\''.$values['id'].'\'';
      $result = execute_bool($link,$query);
      if($values['file'])
      {
        $files = explode(";",$values['file']);
        foreach($files as $value)
        {
          if($value)
          {
            $query = 'select * from file where id=\''.$value.'\'';
            $result = execute($link,$query);
            unlink($result[0]['path']);
            $query = 'delete from file where id=\''.$value.'\'';
            $result = execute_bool($link,$query);
          }
        }
      }
      $query = 'delete from activity where id=\''.$values['id'].'\'';
      $result = execute_bool($link,$query);
      
    }
    //删除组织
    $query = "DELETE FROM organization where id='".$_GET['id']."'";
    $result = execute_bool($link,$query);
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=删除成功&url=/admin/org_op.php');
    exit();
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
  <link rel="stylesheet" href="../style/css/zui.datatable.css">
  <title>组织管理</title>
  <style>
    #main{
      padding: 15px 3px 70px 3px;
      background-color: #EBF2F9;
    }
  </style>
</head>
<body>
  <main id="main" class="container">
    <div class="row t">
      <div id="collapseExampleFile">
        <table class="table datatableorg" id="File_table"></table>
      </div>
    </div>
  </main>
<div id="myModals">

</div>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script src="../style/js/zui.datatable.js"></script>
<script>
  $('table.datatableorg').datatable({
    data: {
        cols: [
            {width: 80, text: '组织名', type: 'string', flex: false},
            {width: 40, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
        ],
        rows: [
<?php

  $query = 'select * from organization';
  $results = execute($link,$query);
  $gosum = 0;
  foreach($results as $value)
  {

  
    $a_act = '<a data-position="100px" data-toggle="modal" data-target="#myModalgo'.$gosum.'">设置</a>，<a href="' . $thisUrlRoot . '/admin/org_op.php?id=' . $value['id'] . '&option=del">删除</a>';
    $gos[$gosum]=$value["id"];
    $gosname[$gosum]=$value["name"];
    $gosum+=1;
    echo "{checked: false, data: ['{$value["name"]}','{$a_act}']},";
  }
  $a_act = '<a data-position="100px" data-toggle="modal" data-target="#myModalgonew">创建新组织</a>';
  echo "{checked: false, data: ['','{$a_act}']},";
?>
           ]
      },
      // 其他选项选项
  });

var myContent = '';
myContent +='<?php
  $content = '<div class="modal fade" id="myModalgonew"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">创建新组织</h4><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button></div><div class="modal-body"><form action="org_op.php?option=new" method="post" ><div class="form-group"><label for="exampleInputInviteCode3">组织名</label><input type="text" class="form-control" id="exampleInputInviteCode3" name="name"></div><button type="submit" class="btn btn-primary">创建</button></form></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button></div></div></div></div>';
  if(isset($gos)&&$gos)
  {
    foreach($gos as $key => $value)
    {
      $content .= '<div class="modal fade" id="myModalgo'.$key.'"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">设置</h4><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button></div><div class="modal-body"><form action="org_op.php?option=edi&org='.$value.'" method="post" ><div class="form-group"><label for="exampleInputInviteCode3">组织名</label><input type="text" class="form-control" id="exampleInputInviteCode3" name="name" value="'.$gosname[$key].'"></div><button type="submit" class="btn btn-primary">修改</button></form></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button></div></div></div></div>';
    }
  }
  echo $content;
?>';
$('#myModals').append(myContent);
</script>
</html>