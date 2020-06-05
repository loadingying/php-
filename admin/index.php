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
  $org_true = $users['organization'];
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

  include_once 'function/activity_people.php';
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
    <link rel="stylesheet" href="../style/css/index.css">
    <title>活动管理系统</title>
  </head>
<body>
  <main id="main">
    <header id="header-container">
      <div class="container">
        <div class="row" id="header">
          <div class="col-2" id="logo">
            <a style="color:black;" target="_blank" href="<?php echo $thisUrlRoot.'/admin/about.php'; ?>">活动管理系统</a>
          </div>
          <div class="col">
            <div id="organization" class="float-left"><i class="icon icon-group"></i>&nbsp<?php echo $orgs['name']; ?>&nbsp&nbsp</div>
<?php
  if($users['op']<4)
  { echo '<a href="'.$thisUrlRoot.'/admin/admin.php" id="startadmin" class="float-left">进入管理平台</a>&nbsp&nbsp';
  }
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
              echo '您好，'.$users['classname'];
            ?> &nbsp&nbsp</div>
          </div>
        </div>
      </div>
    </header>
    <div class="container" id="main-container">
      <div class="row">
          <div class="row" id="hd_container_fa">
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
      echo '<a class="list-group-item active" href="'.$thisUrlRoot.'/admin/index.php?org='.$value["id"].'">'.$value["name"].'<a>';
    }
    else
    {
    echo '<a class="list-group-item" href="'.$thisUrlRoot.'/admin/index.php?org='.$value["id"].'">'.$value["name"].'<a>';
    }
  }
  echo '<a class="list-group-item" href="'.$thisUrlRoot.'/admin/org_op.php">管理组织<a>';
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
<div id="myModals">

</div>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script src="../style/js/zui.datatable.js"></script>
<script>
<?php
echo <<<STRINGQ
  $('table.datatablejxz').datatable({
      data: {
          cols: [
              {width: 80, text: '名称', type: 'string', flex: false, colClass: 'text-center'},
              {width: 170, text: '开始时间', type: 'date', flex: false},
              {width: 256, text: '概述', type: 'string', flex: false, colClass: ''},
              {width: 80, text: '活动状态', type: 'string', flex: false, colClass: 'text-center'},
              {width: 80, text: '状态', type: 'string', flex: false, colClass: 'text-center'},
              {width: 80, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
STRINGQ;
$gosum = 0;
$query = 'select * from activity where unix_timestamp(starttime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(endtime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
foreach ($result as $value){
  $status = query_people_activity($link,$value['id'],$users['id']);
  $a_act = '<a  target="_blank" href="' . $thisUrlRoot . '/admin/activity_look.php?act=' . $value["id"] . '">详情</a>';
  if($status==2&&$org_true == $users['organization'])
  {
    $a_act = $a_act.',<a data-position="100px" data-toggle="modal" data-target="#myModalgo'.$gosum.'">签到</a>';
    $gos[$gosum]=$value["id"];
    $gosum+=1;
  }
  $status = query_people_activity_string($status);
  echo "{checked: true, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','进行中','{$status}','{$a_act}']},";
};
$query = 'select * from activity where unix_timestamp(signendtime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(starttime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
foreach ($result as $value){
  $status = query_people_activity_string(query_people_activity($link,$value['id'],$users['id']));
  $a_act = '<a  target="_blank" href="' . $thisUrlRoot . '/admin/activity_look.php?act=' . $value["id"] . '">详情</a>';
  echo "{checked: true, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','等待开始','{$status}','{$a_act}']},";
};
$query = 'select * from activity where unix_timestamp(signstarttime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(signendtime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
foreach ($result as $value){
  $status = query_people_activity($link,$value['id'],$users['id']);
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_look.php?act=' . $value["id"] . '">详情</a>';
  if($status<2&&$org_true == $users['organization'])
  {
    if($status==1)
    {
      $a_act = $a_act.',<a href="' . $thisUrlRoot . '/admin/activity_join.php?act=' . $value["id"] . '&option=signout">取消报名</a>';
    }
    else
    {
      $a_act = $a_act.',<a href="' . $thisUrlRoot . '/admin/activity_join.php?act=' . $value["id"] . '&option=sign">报名</a>';
    }
  }
  $status = query_people_activity_string($status);
  echo "{checked: true, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','报名中','{$status}','{$a_act}']},";
};
$query = 'select * from activity where unix_timestamp(publictime) < unix_timestamp(\''. $date_now . '\') and unix_timestamp(signstarttime) > unix_timestamp(\''. $date_now . '\') and organization = '.$orgs['id'];
$result = execute($link,$query);
foreach ($result as $value){
  $status = query_people_activity_string(query_people_activity($link,$value['id'],$users['id']));
  $a_act = '<a  target="_blank" href="' . $thisUrlRoot . '/admin/activity_look.php?act=' . $value["id"] . '">详情</a>';
  echo "{checked: true, data: ['{$value["name"]}', '{$value["starttime"]}', '{$value["description"]}','等待报名','{$status}','{$a_act}']},";
};
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
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_look.php?act=' . $value["id"] . '">详情</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["endtime"]}', '{$value["description"]}','已结束','{$a_act}']},";
};

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
$query = 'select * from activity where unix_timestamp(publictime) > unix_timestamp(\''. $date_now . '\')  and organization = '.$orgs['id'];
$result = execute($link,$query);
foreach ($result as $value){
  $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/activity_look.php?act=' . $value["id"] . '">详情</a>';
  echo "{checked: false, data: ['{$value["name"]}', '{$value["publictime"]}', '{$value["description"]}','策划中','{$a_act}']},";
};
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
?>
var myContent = '';
myContent +='<?php
  $content = '';
  if(isset($gos)&&$gos)
  {
    foreach($gos as $key => $value)
    {
      $content .= '<div class="modal fade" id="myModalgo'.$key.'"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">签到</h4><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button></div><div class="modal-body"><form action="start_activity.php?option=join&act='.$value.'" method="post" ><div class="form-group"><label for="exampleInputInviteCode3">签到码</label><input type="text" class="form-control" id="exampleInputInviteCode3" name="passwd"></div><button type="submit" class="btn btn-primary">签到</button></form></div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button></div></div></div></div>';
    }
  }
  echo $content;
?>';
$('#myModals').append(myContent);
</script>
</html>
<?php
  close_mysql($link);