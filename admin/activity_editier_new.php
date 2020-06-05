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
  if(!isset($_GET['org'])||$_GET['org']=='')
  {
    header('Location: '. $thisUrlRoot .'/admin/message.php?message=非法访问');
    exit();
  }
  $org = mysqli_real_escape_string($link,$_GET['org']);
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
      $messages='创建活动成功';
      $activityname = mysqli_real_escape_string($link,$_POST['activityname']);
      $createtime = $date_now;
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
      $file = '';
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
      $query = 'insert into activity (id,name,organization,createtime,publictime,signstarttime,signendtime,starttime,endtime,classification,sort,description,content,file) VALUES (NULL, \''.$activityname.'\', \''.$orgs['id'].'\', \''.$createtime.'\', \''.$publictime.'\', \''.$signtime.'\', \''.$signendtime.'\', \''.$starttime.'\', \''.$endtime.'\', \''.$classification.'\', \'0\', \''.$description.'\', \''.$content.'\', \''.$file.'\')';
      $result = execute_bool($link,$query);
      $query = 'select * from activity where name= \''.$activityname.'\' and createtime= \''.$createtime.'\'';
      $result = execute($link,$query);
      if(!$result)
      {
        header('Location: '. $thisUrlRoot .'/admin/message.php?message=失败，入库异常');
        exit();
      }
      create_activity($link,$result[0]);
      $query = 'insert into activityoption (id,name,activity,value) values (NULL,\'signpeople\',\''.$result[0]['id'].'\','.$_POST["activityoption"].')';
      $result = execute_bool($link,$query);
      header('Location: '. $thisUrlRoot .'/admin/message.php?message='.$messages);
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
    }
    .messages{
      color: red;
    }
  </style>
</head>
<body>
  <main id="main" class="container">
    <header class="row">
      <h1 style="margin:auto;">创建新活动</h1>
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
          <select class="form-control" name="classification" id="classification">
            <option value="0">请选择一种分类</option>
<?php
  $query = 'select * from classification';
  $result = execute($link,$query);
  foreach($result as $value)
  {
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
            <input type="radio" name="activityoption" value="1" checked> 是
          </label>
          <label class="radio-inline">
            <input type="radio" name="activityoption" value="0"> 否
          </label>
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>详细内容:</strong>
        </div>
        <div class="col">
          <textarea id="contentSimple" name="content" id="content" class="form-control kindeditorSimple" style="height:300px;"></textarea>
        </div>
        <div class="col-1">
        </div>
      </div>
      <div class="row t">
        <div class="col-2">
          <strong>上传附件:</strong>
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
          <button type="submit" class="btn btn-default" id="submit">提交</button>
        </div>
        <div class="col-1">
        </div>
      </div>
  </from>
  </main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script src="../style/js/kindeditor.min.js"></script>
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
</script>
</html>
<?php
  close_mysql($link);