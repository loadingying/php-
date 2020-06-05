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
  if(!isset($_GET['act'])||$_GET['act']=='')
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  $query = 'select * from activity where id=\''.$_GET['act'].'\'';
  $result = execute($link,$query);
  $act = $result[0];
  $org = mysqli_real_escape_string($link,$act['organization']);
  $query = 'select * from organization where id=\''.$org.'\'';
  $result = execute($link,$query);
  $orgs = $result[0];
  header('Content-type:text/html;charset=utf-8'); 
?>
<!DOCTYPE html>
<html lang="zh_cn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/css/bootstrap.css">
  <link rel="stylesheet" href="../style/css/zui.css">
  <link rel="stylesheet" href="../style/css/kindeditor.min.css">
  <link rel="stylesheet" href="../style/css/zui.datatable.css">
  <title>活动查看</title>
  <style>
    #main{
      padding: 15px 3px 70px 3px;
      background-color: #F1F1F1;
    }
    header{
      padding: 0px 0px 6px 0px;
    }
    .t{
      margin: 20px 0px;
      padding-left:20px;
    }
    .messages{
      color: red;
    }
    #nav{
      padding-top: 30px;
    }
  </style>
</head>
<body>
  <main id="main" class="container">
    <header class="row">
      <h1 style="margin:auto;">活动详情</h1>
    </header>
    <form id="fromed" method="post" enctype="multipart/form-data" >
      <div class="row t">
        <div class="col-2">
          <strong>活动名:</strong>
        </div>
        <div class="col">
          <input type="text" class="form-control" placeholder="活动名" name="activityname" id="activityname">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>所属组织:&nbsp</strong>
        </div>
        <div class="col">
          <strong><?php echo $orgs['name'];?></strong>
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>发布时间:</strong>
        </div>
        <div class="col">
          <input type="date" class="form-control" name="publicdate" id="publicdate">
        </div>
        <div class="col">
          <input type="time" class="form-control" name="publictime" id="publictime">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>报名时间:</strong>
        </div>
        <div class="col">
          <input type="date" class="form-control" name="signdate" id="signdate">
        </div>
        <div class="col">
          <input type="time" class="form-control" name="signtime" id="signtime">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>结束报名时间:</strong>
        </div>
        <div class="col">
          <input type="date" class="form-control" name="signenddate" id="signenddate">
        </div>
        <div class="col">
          <input type="time" class="form-control" name="signendtime" id="signendtime">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>开始时间:</strong>
        </div>
        <div class="col">
          <input type="date" class="form-control" name="startdate" id="startdate">
        </div>
        <div class="col">
          <input type="time" class="form-control" name="starttime" id="starttime">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>结束时间:</strong>
        </div>
        <div class="col">
          <input type="date" class="form-control" name="enddate" id="enddate">
        </div>
        <div class="col">
          <input type="time" class="form-control" name="endtime" id="endtime">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>活动分类:</strong>
        </div>
        <div class="col">
          <input type="text" class="form-control" value="

<?php
$query = 'select * from classification where name=\''.$act['classification'].'\'';
$result = execute($link,$query);
echo $act['classification'];
?>
          ">
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>活动概述:</strong>
        </div>
        <div class="col">
          <textarea class="form-control" rows="3" placeholder="活动概述" name="description" id="description"></textarea>
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>是否直接录取:</strong>
        </div>
        <div class="col">
<?php
$query = 'select * from activityoption where name=\'signpeople\' and activity=\''.$act['id'].'\'';
$result = execute($link,$query);
$activityoption = $result[0]['value'];
?>
          <label class="radio-inline" <?php if($activityoption!=1){echo 'style="display:none;"';}?>>
            <input type="radio" name="activityoption" value="1" <?php if($activityoption==1){echo 'checked';}?>> 是
          </label>
          <label class="radio-inline" <?php if($activityoption!=0){echo 'style="display:none;"';}?>>
            <input type="radio" name="activityoption" value="0" <?php if($activityoption==0){echo 'checked';}?>> 否
          </label>
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-1">
          <strong>详细内容:</strong>
        </div>
        <div class="col-9">
          <textarea id="contentSimple" name="content" id="content" class="form-control kindeditorSimple" style="height:300px;width:450px;"><?php echo $act['content']; ?></textarea>
        </div>
      </div>
      </div>
      <div class="row t">
        <div id="collapseExampleFile">
          <table class="table datatableFile" id="File_table"></table>
        </div>
      </div>
    </from>
  </main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script src="../style/js/kindeditor.min.js"></script>
<script src="../style/js/zui.datatable.js"></script>
<script>
  KindEditor.create('textarea.kindeditorSimple', {
      basePath: '../style/css/',
      bodyClass : 'article-content',     // 确保编辑器内的内容也应用 ZUI 排版样式
      cssPath: '../style/css/zui.css', // 确保编辑器内的内容也应用 ZUI 排版样式
      resizeType : 1,
      allowPreviewEmoticons : false,
      allowImageUpload : false,
      items : [
          'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
          'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
          'insertunorderedlist', '|', 'emoticons', 'link'
      ]
  });
  document.getElementById('activityname').value = '<?php echo $act['name'];?>';
<?php 
  $dates = explode(" ",$act['publictime']);
  $date = $dates[0];
  $dates = explode(":",$dates[1]);
  $time = $dates[0].':'.$dates[1];
?>
  document.getElementById('publicdate').value = '<?php echo $date;?>';
  document.getElementById('publictime').value = '<?php echo $time;?>';
<?php 
  $dates = explode(" ",$act['signstarttime']);
  $date = $dates[0];
  $dates = explode(":",$dates[1]);
  $time = $dates[0].':'.$dates[1];
?>
  document.getElementById('signdate').value = '<?php echo $date;?>';
  document.getElementById('signtime').value = '<?php echo $time;?>';
<?php 
  $dates = explode(" ",$act['signendtime']);
  $date = $dates[0];
  $dates = explode(":",$dates[1]);
  $time = $dates[0].':'.$dates[1];
?>
  document.getElementById('signenddate').value = '<?php echo $date;?>';
  document.getElementById('signendtime').value = '<?php echo $time;?>';
<?php 
  $dates = explode(" ",$act['starttime']);
  $date = $dates[0];
  $dates = explode(":",$dates[1]);
  $time = $dates[0].':'.$dates[1];
?>
  document.getElementById('startdate').value = '<?php echo $date;?>';
  document.getElementById('starttime').value = '<?php echo $time;?>';
<?php 
  $dates = explode(" ",$act['endtime']);
  $date = $dates[0];
  $dates = explode(":",$dates[1]);
  $time = $dates[0].':'.$dates[1];
?>
  document.getElementById('enddate').value = '<?php echo $date;?>';
  document.getElementById('endtime').value = '<?php echo $time;?>';
  document.getElementById('description').value = '<?php echo $act['description'];?>';
  
  $('table.datatableFile').datatable({
      data: {
          cols: [
              {width: 80, text: '文件名', type: 'string', flex: false},
              {width: 40, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
<?php
  if($act['file'])
  {
    $files = explode(";",$act['file']);
    foreach($files as $value)
    {
      if($value)
      {
        $query = 'select * from file where id=\''.$value.'\'';
        $result = execute($link,$query);
        $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/download_file.php?id=' . $value . '">下载</a>';
        echo "{checked: false, data: ['{$result[0]["name"]}','{$a_act}']},";
      }
    }
  }
?>

         ]
      },
      // 其他选项选项
  });

</script>
</html>
<?php
  close_mysql($link);