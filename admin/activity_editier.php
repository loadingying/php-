<?php
  include_once 'function/exists_login.php';
  exists_login();
  include_once 'function/mysqli_mysql_connection.php';
  $link = link_mysql();
  $thisUrlRoot = 'http://' . $_SERVER["HTTP_HOST"];
  date_default_timezone_set('Asia/Shanghai');
  $date_now = mysqli_real_escape_string($link,date('Y-m-d H:i:s'));
  $date_now_int = strtotime($date_now);
  $users = mysqli_real_escape_string($link,$_SESSION['user']);
  $query = 'select * from people where name=\''.$users.'\'';
  $result = execute($link,$query);
  $users = $result[0];
  $messages='';
  if(!isset($_GET['act'])||$_GET['act']=='')
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  $query = 'select * from activity where id=\''.$_GET['act'].'\'';
  $result = execute($link,$query);
  $act = $result[0];
  $act_start_time = strtotime($act['starttime']);
  $act_end_time = strtotime($act['endtime']);
  $org = mysqli_real_escape_string($link,$act['organization']);
  $query = 'select * from organization where id=\''.$org.'\'';
  $result = execute($link,$query);
  $orgs = $result[0];

  include_once './function/activity_people.php';
  $query = 'select * from op where organization=\''.$orgs['id'].'\'';
  $result = execute($link,$query);
  foreach ($result as $key => $value) {
    if($value['name']=='createActivity'){ $ops['createActivity']=$value;}
  }
  if($users['op']>$ops['createActivity']['op'])
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }

  if(isset($_GET['option'])&&$_GET['option']=='del')
  {
    del_activity($link,$act['id']);
    $query = 'delete from activityoption where activity=\''.$act['id'].'\'';
    $result = execute_bool($link,$query);
    if($act['file'])
    {
      $files = explode(";",$act['file']);
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
    $query = 'delete from activity where id=\''.$act['id'].'\'';
    $result = execute_bool($link,$query);
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=删除成功');
    exit();
  }
  if($_POST)
  {
    // var_dump($_POST);
    if(!$_POST['activityname'])
    {
      $messages='活动名未填写';
    }
    elseif(!preg_match("/^[A-Za-z0-9\x7f-\xff\-)(@!‘’“”\'\":：]+$/",$_POST['activityname']))
    {
      $messages='不合法的活动名';
    }
    elseif(!$_POST['publicdate']||!$_POST['publictime'])
    {
      $messages='发布时间未填写';
    }
    elseif(!$_POST['signdate']||!$_POST['signtime'])
    {
      $messages='报名时间未填写';
    }
    elseif(!$_POST['signenddate']||!$_POST['signendtime'])
    {
      $messages='报名结束时间未填写';
    }
    elseif(!$_POST['startdate']||!$_POST['starttime'])
    {
      $messages='开始时间未填写';
    }
    elseif(!$_POST['enddate']||!$_POST['endtime'])
    {
      $messages='结束时间未填写';
    }
    elseif(!$_POST['classification']||$_POST['classification']==0)
    {
      $messages='未选择分类';
    }
    else
    {
      $messages='修改成功';
      $activityname = mysqli_real_escape_string($link,$_POST['activityname']);
      $publictime = mysqli_real_escape_string($link,$_POST['publicdate'].' '.$_POST['publictime'].':00');
      $signtime = mysqli_real_escape_string($link,$_POST['signdate'].' '.$_POST['signtime'].':00');
      $signendtime = mysqli_real_escape_string($link,$_POST['signenddate'].' '.$_POST['signtendime'].':00');
      $starttime = mysqli_real_escape_string($link,$_POST['startdate'].' '.$_POST['starttime'].':00');
      $endtime = mysqli_real_escape_string($link,$_POST['enddate'].' '.$_POST['endtime'].':00');
      $query = 'select * from classification where id=\''.$_POST['classification'].'\'';
      $result = execute($link,$query);
      $classification = mysqli_real_escape_string($link,$result[0]['name']);
      $description = mysqli_real_escape_string($link,$_POST['description']);
      $content = mysqli_real_escape_string($link,$_POST['content']);
      $file = $act['file'];
      // var_dump($_FILES);
      if($_FILES['file']['name'][0])
      {
        foreach($_FILES['file']['name'] as $key => $value)
        {
          if($_FILES['file']['error'][$key]>0)
          {
            $messages .=','.$value.'上传失败';
            continue;
          }
          if($_FILES['file']['size'][$key]>204800000)
          {
            $messages .=','.$value.'上传失败(文件不应大于200M)';
            continue;
          }
          $endfile = substr(strrchr($value, '.'), 1);
          $newfile = rand().rand().'.'.$endfile;
          $date_month = date('Ym');
          if(!is_dir('../upload/'.$date_month))
          {
            mkdir('../upload/'.$date_month);
          }
          if(file_exists('../upload/'.$date_month.'/'.$newfile))
          {
            $messages .= ','.$value.'上传失败(#111)';
            continue;
          }
          $newpath = mysqli_real_escape_string($link,'../upload/'.$date_month.'/'.$newfile);
          $_FILES["file"]["type"][$key] = mysqli_real_escape_string($link,$_FILES["file"]["type"][$key]);
          $_FILES["file"]["size"][$key] = mysqli_real_escape_string($link,$_FILES["file"]["size"][$key]);
          $value = mysqli_real_escape_string($link,$value);
          move_uploaded_file($_FILES["file"]["tmp_name"][$key], $newpath);
          $query = 'insert into file (id,type,name,path,size,sort) VALUES (NULL, \''. $_FILES["file"]["type"][$key] .'\',\''. $value .'\', \''. $newpath .'\' , \''. $_FILES["file"]["size"][$key] .'\', \'0\')';
          $result = execute_bool($link,$query);
          $query = 'select * from file where path=\''.$newpath.'\'';
          $result = execute($link,$query);
          $file .= $result[0]['id'].';';
        }
      }
      $file = mysqli_real_escape_string($link,$file);
      if($activityname!=$act['name'])
      {
        $query = 'update activity set name=\''.$activityname.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($publictime!=$act['publictime'])
      {
        $query = 'update activity set publictime=\''.$publictime.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($signtime!=$act['signstarttime'])
      {
        $query = 'update activity set signtime=\''.$signtime.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($signendtime!=$act['signendtime'])
      {
        $query = 'update activity set signendtime=\''.$signendtime.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($starttime!=$act['starttime'])
      {
        $query = 'update activity set starttime=\''.$starttime.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($endtime!=$act['endtime'])
      {
        $query = 'update activity set endtime=\''.$endtime.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($classification!=$act['classification'])
      {
        $query = 'update activity set classification=\''.$classification.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($description!=$act['description'])
      {
        $query = 'update activity set description=\''.$description.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($content!=$act['content'])
      {
        $query = 'update activity set content=\''.$content.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }
      if($file!=$act['file'])
      {
        $query = 'update activity set file=\''.$file.'\' where id='.$act['id'];
        $result = execute_bool($link,$query);
      }

      $query = 'update activityoption set value=\''.$_POST["activityoption"].'\' where activity='.$act['id'].' and name=\'signpeople\'';
      $result = execute_bool($link,$query);
      header('Location: '. $thisUrlRoot .'/admin/message.php?message='.$messages.'&url=/admin/activity_editier.php?act='.$act['id']);
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
  <link rel="stylesheet" href="../style/css/kindeditor.min.css">
  <link rel="stylesheet" href="../style/css/zui.datatable.css">
  <title>活动编辑</title>
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
    <div class="row">
      
      <div class="row t" id="nav">
        <ul class="nav nav-secondary">
          <li id="people_li" class="active"><a href="avascrpt:void(0);" id="people_id">人员</a></li>
          <li id="edi_li"><a href="avascrpt:void(0);" id="edi_id">编辑</a></li>
        </ul>
      </div>
      <div class="row t">
        <div id="collapseExamplePeople">
          <table class="table datatablePeople" id="People_table"></table>
        </div>
        <div id="fromediter" style="display:none;">
          <form id="fromed" method="post" enctype="multipart/form-data" >
            <div class="row t">
              <div class="col-2">
                <strong>签到码:</strong>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="startpasswd">
              </div>
              <div class="col-2">
                <a class="btn btn-link" href="<?php echo 'start_activity.php?option=updatapasswd&act='.$act['id'];?>">重新生成</a>
              </div>
              <div class="col-1">
              </div>
            </div>
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
                <select class="form-control" name="classification" id="classification">

  <?php
    $query = 'select * from classification where name=\''.$act['classification'].'\'';
    $result = execute($link,$query);
    echo '<option value="' . $result[0]['id'] . '">' . $act['classification'] . '</option>';
    $query = 'select * from classification';
    $result = execute($link,$query);
    foreach($result as $value)
    {
      if($value['name']==$act['classification'])
      {
        continue;
      }
      echo '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
    }
  ?>
                </select>
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
                <label class="radio-inline">
  <?php
    $query = 'select * from activityoption where name=\'signpeople\' and activity=\''.$act['id'].'\'';
    $result = execute($link,$query);
    $activityoption = $result[0]['value'];
  ?>
                  <input type="radio" name="activityoption" value="1" <?php if($activityoption==1){echo 'checked';}?>> 是
                </label>
                <label class="radio-inline">
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
            <div class="row t">
              <div class="col-2">
                <strong>添加附件:</strong>
              </div>
              <div class="col">
                <input type="file" class="form-control" name="file[]" id="file" multiple>
              </div>
              <div class="col-1">
              </div>
            </div>
            <div class="row t">
              <div class="col">
              </div>
              <div class="col-2">
                <div class="messages"><?php if($messages){echo $messages;}?></div>
              </div>
              <div class="col-1">
                <button type="submit" class="btn btn-default" id="submit">确认修改</button>
              </div>
              <div class="col-1">
              </div>
            </div>
            <div class="row t">
              <div id="collapseExampleFile">
                <table class="table datatableFile" id="File_table"></table>
              </div>
            </div>
          </from>
        </div>
      </div>
    </div>
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
  document.getElementById('submit').onclick = (function(event){
    document.getElementById('main').className+='load-indicator loading';
  });
  document.getElementById('activityname').value = '<?php echo $act['name'];?>';
<?php
  $passwd = get_start_passwd($link,$act['id']);
?>
  document.getElementById('startpasswd').value = '<?php echo $passwd;?>';
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
        $a_act = '<a target="_blank" href="' . $thisUrlRoot . '/admin/download_file.php?id=' . $value . '">下载</a>，<a href="' . $thisUrlRoot . '/admin/del_file.php?id=' . $value . '&act='.$act['id'].'">删除</a>';
        echo "{checked: false, data: ['{$result[0]["name"]}','{$a_act}']},";
      }
    }
  }
?>

         ]
      },
      // 其他选项选项
  });


  $('table.datatablePeople').datatable({
      data: {
          cols: [
              {width: 80, text: '名字', type: 'string', flex: false},
              {width: 80, text: '状态', type: 'string', flex: false},
              {width: 40, text: '操作', type: 'string', flex: false, colClass: 'text-center'},
          ],
          rows: [
<?php
  $query = 'select * from peopleandactivity where activityid=\''.$act['id'].'\' and status=3';
  $results = execute($link,$query);
  $sum = sizeof($results);
  foreach($results as $value)
  {
    $query = 'select * from people where id=\''.$value['peopleid'].'\'';
    $result = execute($link,$query);
    $a_act = '';
    echo "{checked: false, data: ['{$result[0]["name"]}', '已参加' ,'{$a_act}']},";
  }
  echo "{checked: false, data: ['', '' ,'已参加人数:{$sum}']},";

  $query = 'select * from peopleandactivity where activityid=\''.$act['id'].'\' and (status=2 or status=4)';
  $results = execute($link,$query);
  $sum = sizeof($results);
  foreach($results as $value)
  {
    $query = 'select * from people where id=\''.$value['peopleid'].'\'';
    $result = execute($link,$query);
    $a_act = '<a href="' . $thisUrlRoot . '/admin/activity_join.php?people=' . $value['peopleid'] . '&act='.$value['activityid'].'&option=quitjoin">取消录取</a>';
    if($date_now_int>$act_start_time&&$date_now_int<$act_end_time)
    {
      $a_act .= ',<a href="' . $thisUrlRoot . '/admin/start_activity.php?people=' . $value['peopleid'] . '&act='.$value['activityid'].'&option=startopjoin">强制签到</a>';
      $status = '已录取';
    }
    if($date_now_int>$act_end_time)
    {
      $a_act = '';
      $status = '未到场';
      if($value['status']==2)
      {
        $query = "update peopleandactivity set status='4' where activityid='".$act['id']."' and peopleid='".$value['peopleid']."'";
        $results = execute_bool($link,$query);
        sub_credit($link,$value['peopleid']);
      }
    }
    echo "{checked: false, data: ['{$result[0]["name"]}', '{$status}' ,'{$a_act}']},";
  }
  echo "{checked: false, data: ['', '' ,'{$status}人数:{$sum}']},";

  $query = 'select * from peopleandactivity where activityid=\''.$act['id'].'\' and status=1';
  $results = execute($link,$query);
  $sum = sizeof($results);
  foreach($results as $value)
  {
    $query = 'select * from people where id=\''.$value['peopleid'].'\'';
    $result = execute($link,$query);
    $a_act = '<a href="' . $thisUrlRoot . '/admin/activity_join.php?people=' . $value['peopleid'] . '&act='.$value['activityid'].'&option=join">录取</a>';
    echo "{checked: false, data: ['{$result[0]["name"]}', '已报名' ,'{$a_act}']},";
  }
  echo "{checked: false, data: ['', '' ,'已报名人数:{$sum}']},";

  $query = 'select * from peopleandactivity where activityid=\''.$act['id'].'\' and status=0';
  $results = execute($link,$query);
  $sum = sizeof($results);
  foreach($results as $value)
  {
    $query = 'select * from people where id=\''.$value['peopleid'].'\'';
    $result = execute($link,$query);
    $a_act = '';
    echo "{checked: false, data: ['{$result[0]["name"]}', '未报名' ,'{$a_act}']},";
  }
  echo "{checked: false, data: ['', '' ,'未报名:{$sum}']},";

?>

         ]
      },
      // 其他选项选项
  });
  var actpeople = $('#people_li');
  var actedi = $('#edi_li');
  var actpeopled = document.getElementById('people_li');
  var actedid = document.getElementById('edi_li');
  document.getElementById('people_id').onclick = (function(event){
    if(!actpeople.hasClass("active"))
    {
      actpeopled.className += ' active';
      document.getElementById('collapseExamplePeople').style.display="block";
    }
    if(actedi.hasClass("active"))
    {
      var y = actedid.getAttribute("class");
      var classVal = y.replace("active","");
      actedid.setAttribute("class",classVal);
      document.getElementById('fromediter').style.display="none";
    }
  });
  document.getElementById('edi_id').onclick = (function(event){
    if(!actedi.hasClass("active"))
    {
      actedid.className += ' active';
      document.getElementById('fromediter').style.display="block";
    }
    if(actpeople.hasClass("active"))
    {
      var y = actpeopled.getAttribute("class");
      var classVal = y.replace("active","");
      actpeopled.setAttribute("class",classVal);
      document.getElementById('collapseExamplePeople').style.display="none";
    }
  });
</script>
</html>
<?php
  close_mysql($link);