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
  if($users['op']>=4)
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  if (isset($_GET['org']))
  {
    if($users['op']<=1)
    {
      $users['organization']=$_GET['org'];
    }
  }
  $org = mysqli_real_escape_string($link,$users['organization']);
  $query = 'select * from organization where id=\''.$org.'\'';
  $result = execute($link,$query);
  $orgs = $result[0];
  $query = 'select * from op where organization=\''.$orgs['id'].'\'';
  $result = execute($link,$query);
  foreach ($result as $key => $value) {
    if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
    if($value['name']=='createPeople'){ $ops['createPeople']=$value;}
    if($value['name']=='delPeople'){ $ops['delPeople']=$value;}
  }
  if(!isset($_GET['option']))
  {
    header('Location: '. $thisUrlRoot .'/admin/admin.php?option=activity');
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
    <link rel="stylesheet" href="../style/css/admin.css">
    <title>管理页面</title>
  </head>
<body>
  <main id="main">
    <header id="header-container">
      <div class="container">
        <div class="row" id="header">
          <div class="col-2" id="logo">
            <a style="color:black;" target="_blank" href="<?php echo $thisUrlRoot.'/admin/about.php'; ?>">管理中心</a>
          </div>
          <div class="col">
            <div id="organization" class="float-left"><i class="icon icon-group"></i>&nbsp<?php echo $orgs['name']; ?>&nbsp&nbsp</div>
            <a href="<?php echo $thisUrlRoot;?>" id="exitadmin" class="float-left">退出管理平台</a> &nbsp&nbsp
<?php
  if($users['op']<2)
  {
    echo <<<ORGG
            <!-- 对话框触发按钮 -->
            <button type="button" class="btn btn-primary" data-position="100px" data-toggle="modal" data-target="#myModal">更换组织</button>
ORGG;
  }
?>
          </div>
          <div class="col-5 ml-auto" id="login">
            <div class="dropdown dropdown-hover float-right">
            <button class="btn" type="button" data-toggle="dropdown"><?php
              echo $users['classname'] . '';
            ?><span class="caret"></span></button>
            <ul class="dropdown-menu">
              <li><a target="_blank" href="<?php echo $thisUrlRoot.'/admin/person.php?id='.$users['id']; ?>">个人中心</a></li>
              <li><a href="<?php echo $thisUrlRoot . '/admin/logout.php' ?>">注销</a></li>
            </ul>
            </div>
            <div class="float-right"><?php
              if($users['op']==0) {echo '您好,超级管理员!';}
              elseif($users['op']==1) {echo '您好,副管理员!';}
              elseif($users['op']==2) {echo '您好,一级管理员!';}
              elseif($users['op']==3) { echo '您好,二级管理员!';}
              elseif($users['op']==4) {echo '您好,用户!';}
            ?> &nbsp&nbsp</div>
          </div>
        </div>
      </div>
    </header>
    <div class="container" id="main-container">
      <div class="row">
        <div class="col-3">
          <ul class="nav nav-tabs nav-stacked">
            <li class="<?php if($_GET['option']=='activity'){echo 'active';} ?>"><a href="<?php if($_GET['option']=='activity'){echo 'javascript:void(0);';}else{echo $thisUrlRoot.'/admin/admin.php?option=activity&org='.$orgs['id'];} ?>">活动</a></li>
            <li class="<?php if($_GET['option']=='people'){echo 'active';} ?>" ><a href="<?php if($_GET['option']=='people'){echo 'javascript:void(0);';}else{echo $thisUrlRoot.'/admin/admin.php?option=people&org='.$orgs['id'];} ?>">成员</a></li>
            <li class="<?php if($_GET['option']=='option'){echo 'active';} ?>" ><a href="<?php if($_GET['option']=='option'){echo 'javascript:void(0);';}else{echo $thisUrlRoot.'/admin/admin.php?option=option&org='.$orgs['id'];} ?>">权限</a></li>
          </ul>
        </div>
        <div class="col-9">

<?php
if($_GET['option']=='people')
{
echo <<<PEOPLET
          <div id="collapseExamplePeople">
            <table class="table datatablePeople" id="People_table"></table>
          </div>


PEOPLET;
}
?>
<?php
if($_GET['option']=='option')
{
echo <<<PEOPLETOP
          <div id="collapseExampleOp">
            <table class="table datatableOp" id="People_table"></table>
          </div>


PEOPLETOP;
}
?>
<?php
if($_GET['option']=='activity')
{
  echo <<< ACTIVI
          <div class="row">
            <ul class="nav nav-secondary"  id="hd_container">
              <li class="col active" id="actjxz"><a href="javascript:void(0);" id="actjxz_a">进行中</a></li>
              <li class="col" id="actyjs"><a href="javascript:void(0);" id="actyjs_a">已结束</a></li>
              <li class="col" id="actchz"><a href="javascript:void(0);" id="actchz_a">策划中</a></li>
            </ul>
          </div>
          <hr>
          <div class="row">
            <div id="collapseExamplejxz">
              <table class="table datatablejxz" id="jxz_table"></table>
            </div>
            <div id="collapseExampleyjs" style="display:none;">
              <table class="table datatableyjs" id="jxz_table"></table>
            </div>
            <div id="collapseExamplechz" style="display:none;">
              <table class="table datatablechz" id="jxz_table"></table>
            </div>
ACTIVI;
}
?>

          </div>
        </div>
      </div>
    </div>
  </main>
<?php
if($users['op']<2)
{
  echo <<<ORGGG

  <!-- 对话框HTML -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">更换组织</h4>
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
          
        </div>
        <div class="modal-body">
          <div class="list-group">
ORGGG;

  $query = 'select * from organization';
  $result = execute($link,$query);
  foreach ($result as $value)
  {
    if($orgs['id']==$value['id'])
    {
      echo '<a class="list-group-item active" href="'.$thisUrlRoot.'/admin/admin.php?option=activity&org='.$value["id"].'">'.$value["name"].'<a>';
    }
    else
    {
    echo '<a class="list-group-item" href="'.$thisUrlRoot.'/admin/admin.php?option=activity&org='.$value["id"].'">'.$value["name"].'<a>';
    }
  }
  echo '<a target="_blank" class="list-group-item" href="'.$thisUrlRoot.'/admin/org_op.php">管理组织<a>';
  echo <<<ORGGGG
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        </div>
      </div>
    </div>
  </div>
ORGGGG;
  }
?>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script src="../style/js/zui.datatable.js"></script>
<script>
<?php
if($_GET['option']=='option')
{ echo <<<STRINGQRR
  $('table.datatableOp').datatable({
      data: {
          cols: [
              {width: 100, text: '权限', type: 'string', flex: false, colClass: 'text-center'},
              {width: 170, text: '最低行使职位', type: 'date', flex: false},
              {width: 150, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
STRINGQRR;

foreach($ops as $key => $value)
{
  if($value['op']==0) {$op_name = '超级管理员';}
  elseif($value['op']==1) {$op_name = '副管理员';}
  elseif($value['op']==2) {$op_name = '组织管理员';}
  elseif($value['op']==3) {$op_name = '组织副管理员';}
  elseif($value['op']==4) {$op_name = '普通成员';}

  if($value['name']=='createActivity'){ $op_des='创建活动';}
  elseif($value['name']=='createPeople'){ $op_des='创建成员';}
  elseif($value['name']=='delPeople'){ $op_des='删除成员';}
  $a_act = '<a href="' . $thisUrlRoot . '/admin/op_activity.php?option=op&op=up&id=' . $value["id"]  . '">提升要求</a>，<a href="' . $thisUrlRoot . '/admin/op_activity.php?option=op&op=down&id=' . $value["id"] . '">降低要求</a>';
  echo "{checked: false, data: ['{$op_des}','{$op_name}','{$a_act}']},";
}

echo <<<STRINGPRR
          ]
      },
      // 其他选项选项
  });
STRINGPRR;

}
?>



<?php
if($_GET['option']=='people')
{ echo <<<STRINGQR
  $('table.datatablePeople').datatable({
      data: {
          cols: [
              {width: 80, text: '姓名', type: 'string', flex: false, colClass: 'text-center'},
              {width: 120, text: '学号/ID', type: 'date', flex: false},
              {width: 80, text: '信用', type: 'string', flex: false, colClass: ''},
              {width: 80, text: '职位', type: 'string', flex: false, colClass: 'text-center'},
              {width: 150, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
STRINGQR;

$query = 'select * from people where organization = '.$orgs['id'];
$result = execute($link,$query);
$sum = sizeof($result);
foreach ($result as $value){
  if($value['op']==0) {$op_name = '超级管理员';}
  elseif($value['op']==1) {$op_name = '副管理员';}
  elseif($value['op']==2) {$op_name = '组织管理员';}
  elseif($value['op']==3) {$op_name = '组织副管理员';}
  elseif($value['op']==4) {$op_name = '普通成员';}
  $a_act = '<a href="' . $thisUrlRoot . '/admin/op_activity.php?option=people&op=up&people=' . $value["id"] . '">提升权限</a>，<a href="' . $thisUrlRoot . '/admin/op_activity.php?option=people&op=down&people=' . $value["id"] . '">降低权限</a>，<a href="' . $thisUrlRoot . '/admin/op_activity.php?option=people&op=del&people=' . $value["id"] . '">删除成员</a>';
  $a_classname = '<a target="_blank" href="' . $thisUrlRoot . '/admin/person.php?id='.$value['id'].'">'.$value["classname"].'</a>';
  echo "{checked: false, data: ['{$a_classname}', '{$value["organizationID"]}', '{$value["credit"]}','{$op_name}','{$a_act}']},";
};
echo "{checked: false, data: ['总数', '".$sum."', '','','']},";
if($users['op']<=$ops['createPeople']['op']){
  echo "{checked: false, data: ['', '', '','','<a target=\"_blank\" href=\"" . $thisUrlRoot . "/admin/create_people.php?organization=".$orgs['id']."\">创建新成员</a>']},";
}
echo <<<STRINGPR
          ]
      },
      // 其他选项选项
  });
STRINGPR;

}
?>
<?php
if($_GET['option']=='activity')
{ echo <<<STRINGQ
  $('table.datatablejxz').datatable({
      data: {
          cols: [
              {width: 80, text: '名称', type: 'string', flex: false, colClass: 'text-center'},
              {width: 170, text: '开始时间', type: 'date', flex: false},
              {width: 256, text: '概述', type: 'string', flex: false, colClass: ''},
              {width: 80, text: '状态', type: 'string', flex: false, colClass: 'text-center'},
              {width: 80, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
STRINGQ;
$sum = 0;
$query = 'select * from activity where unix_timestamp(starttime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(endtime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
$sum += sizeof($result);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '">设置</a>，<a href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '&option=del">删除</a>';
  echo "{checked: true, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','进行中','{$a_act}']},";
};
$query = 'select * from activity where unix_timestamp(signendtime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(starttime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
$sum += sizeof($result);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '">设置</a>，<a href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '&option=del">删除</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','即将开始','{$a_act}']},";
};
$query = 'select * from activity where unix_timestamp(signstarttime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(signendtime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
$sum += sizeof($result);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '">设置</a>，<a href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '&option=del">删除</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','报名中','{$a_act}']},";
};
$query = 'select * from activity where unix_timestamp(publictime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(signstarttime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
$sum += sizeof($result);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '">设置</a>，<a href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '&option=del">删除</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','等待报名','{$a_act}']},";
};
echo "{checked: false, data: ['总数', '".$sum."', '','','']},";
echo <<<STRINGP
          ]
      },
      // 其他选项选项
  });
STRINGP;
echo <<<STRINGW
  $('table.datatableyjs').datatable({
      data: {
          cols: [
              {width: 80, text: '名称', type: 'string', flex: false, colClass: 'text-center'},
              {width: 170, text: '结束时间', type: 'date', flex: false},
              {width: 256, text: '概述', type: 'string', flex: false, colClass: ''},
              {width: 80, text: '状态', type: 'string', flex: false, colClass: 'text-center'},
              {width: 80, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
STRINGW;
$query = 'select * from activity where unix_timestamp(endtime) < unix_timestamp(\''. $date_now . '\')  and organization = '.$orgs['id'];
$result = execute($link,$query);
$sum = sizeof($result);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '">设置</a>，<a href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '&option=del">删除</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["endtime"]}', '{$value["description"]}','已结束','{$a_act}']},";
};
echo "{checked: false, data: ['总数', '".$sum."', '','','']},";
echo <<<STRINGPQ
          ]
      },
      // 其他选项选项
  });
STRINGPQ;

echo <<<STRINGWE
  $('table.datatablechz').datatable({
      data: {
          cols: [
              {width: 80, text: '名称', type: 'string', flex: false, colClass: 'text-center'},
              {width: 170, text: '发布时间', type: 'date', flex: false},
              {width: 256, text: '概述', type: 'string', flex: false, colClass: ''},
              {width: 80, text: '状态', type: 'string', flex: false, colClass: 'text-center'},
              {width: 80, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
STRINGWE;
$query = 'select * from activity where unix_timestamp(createtime) > unix_timestamp(\''. $date_now . '\')  and organization = '.$orgs['id'];
$result = execute($link,$query);
$sum = sizeof($result);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '">设置</a>，<a href="' . $thisUrlRoot . '/admin/activity_editier.php?act=' . $value["id"] . '&option=del">删除</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["publictime"]}', '{$value["description"]}','策划中','{$a_act}']},";
};
echo "{checked: false, data: ['总数', '".$sum."', '','','']},";
if($users['op']<=$ops['createActivity']['op']){
  echo "{checked: false, data: ['', '', '','','<a target=\"_blank\" href=\"" . $thisUrlRoot . "/admin/activity_editier_new.php?org=".$orgs['id']."\">创建新活动</a>']},";
}
echo <<<STRINGPQR
          ]
      },
      // 其他选项选项
  });
STRINGPQR;
echo <<<STRINGO
  var actjxz = $('#actjxz');
  var actyjs = $('#actyjs');
  var actchz = $('#actchz');
  var actjxzd = document.getElementById('actjxz');
  var actyjsd = document.getElementById('actyjs');
  var actchzd = document.getElementById('actchz');
  document.getElementById('actjxz_a').onclick = (function(event){
    if(!actjxz.hasClass("active"))
    {
      actjxzd.className += ' active';
      document.getElementById('collapseExamplejxz').style.display="block";
    }
    if(actyjs.hasClass("active"))
    {
      var x = actyjsd.getAttribute("class");
      var classVal = x.replace("active","");
      actyjsd.setAttribute("class",classVal );
      document.getElementById('collapseExampleyjs').style.display="none";
    }
    if(actchz.hasClass("active"))
    {
      var y = actchzd.getAttribute("class");
      var classVal = y.replace("active","");
      actchzd.setAttribute("class",classVal);
      document.getElementById('collapseExamplechz').style.display="none";
    }
  });
  document.getElementById('actyjs_a').onclick = (function(event){
    if(!actyjs.hasClass("active"))
    {
      actyjsd.className += ' active';
      document.getElementById('collapseExampleyjs').style.display="block";
    }
    if(actjxz.hasClass("active"))
    {
      var x = actjxzd.getAttribute("class");
      var classVal = x.replace("active","");
      actjxzd.setAttribute("class",classVal );
      document.getElementById('collapseExamplejxz').style.display="none";
    }
    if(actchz.hasClass("active"))
    {
      var y = actchzd.getAttribute("class");
      var classVal = y.replace("active","");
      actchzd.setAttribute("class",classVal);
      document.getElementById('collapseExamplechz').style.display="none";
    }
  });
  document.getElementById('actchz_a').onclick = (function(event){
    if(!actchz.hasClass("active"))
    {
      actchzd.className += ' active';
      document.getElementById('collapseExamplechz').style.display="block";
    }
    if(actyjs.hasClass("active"))
    {
      var x = actyjsd.getAttribute("class");
      var classVal = x.replace("active","");
      actyjsd.setAttribute("class",classVal );
      document.getElementById('collapseExampleyjs').style.display="none";
    }
    if(actjxz.hasClass("active"))
    {
      var y = actjxzd.getAttribute("class");
      var classVal = y.replace("active","");
      actjxzd.setAttribute("class",classVal);
      document.getElementById('collapseExamplejxz').style.display="none";
    }
  });

STRINGO;
}
?>
</script>
</html>
<?php
  close_mysql($link);