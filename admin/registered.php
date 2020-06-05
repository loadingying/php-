<?php
  include_once 'afs/aliyun-php-sdk-afs-20180112 2/aliyun-php-sdk-core/Config.php';
  use afs\Request\V20180112 as Afs;
  include_once 'function/mysqli_mysql_connection.php';
  $link = link_mysql();
  $message = '';
  include_once './function/activity_people.php';
  if(isset($_POST['user']))
  {
    // var_dump($_POST);
    if($_POST['user']=='')
    {
      $message='请填写用户名';
    }
    else
    {
      if(!preg_match("/^[A-Za-z0-9]+$/",$_POST['user']))
      {
        $message='用户名只能是数字和字母组成';
        $_POST['user']='';
      }
      else
      {
        $query_value = mysqli_real_escape_string($link,$_POST['user']);
        $query = 'select * from people where name= \''.$query_value . '\'';
        $result = execute($link,$query);
        if($result)
        {
          $message='用户名已存在';
          $_POST['user']='';
        }
        if(!$_POST['organizationID'])
        {
          $message='未填写组织内ID';
          $_POST['organizationID']='';
        }
        if($_POST['organization']!=0&&$_POST['user']!=''&&$_POST['organizationID'])
        {
          if(!preg_match("/^[A-Za-z0-9]+$/",$_POST['organizationID']))
          {
            $message='组织内ID只能是数字和字母';
            $_POST['organizationID']='';
          }
          else
          {
            $query_value = mysqli_real_escape_string($link,$_POST['organizationID']);
            $query = 'select * from people where organization=\''.$_POST['organization'].'\' and organizationID=\''.$query_value.'\'';
            $result = execute($link,$query);
            if($result)
            {
              $message='组织内ID已存在';
              $_POST['organizationID']='';
            }
          }
        }
        if(!$_POST['classname'])
        {
          $message='请填写昵称';
          $_POST['classname']='';
        }
        elseif(!preg_match("/^[A-Za-z0-9\x7f-\xff]+$/",$_POST['classname']))
        {
          $message='昵称只能是汉字和字母数字构成';
          $_POST['classname']='';
        }
      }
    }
    if($_POST['user']=='')
    {
      $_POST['user']='';
    }
    elseif($_POST['password1']=='')
    {
      $message='请输入密码';
    }
    elseif(!preg_match("/^[A-Za-z0-9@*&^%!#~`?]+$/",$_POST['password1']))
    {
      $message='密码存在非法字符，特殊字符只能是{@*&^%!#~`?}';
    }
    elseif($_POST['password1']!=$_POST['password2'])
    {
      $message='两次密码不一致';
    }
    elseif($_POST['classname']=='')
    {
      $_POST['classname']=='';
    }
    elseif($_POST['organization']==0)
    {
      $message='请选择组织';
    }
    elseif($_POST['organizationID']=='')
    {
      $_POST['organizationID']='';
    }
    elseif(!($_POST['sessionid']&&$_POST['token']&&$_POST['sig']))
    {
       $message='请进行人机验证';
    }
    else
    {
      //YOUR ACCESS_KEY、YOUR ACCESS_SECRET请替换成您的阿里云accesskey id和secret  
      $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "LTAI4FnMZXnRNpPRcd31bd3o", "aPuJwsaZKHzgIssQrKlyHibrG5AiUA");
      $client = new DefaultAcsClient($iClientProfile);
      DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", "afs", "afs.aliyuncs.com");

      $request = new Afs\AuthenticateSigRequest();
      $request->setSessionId($_POST['sessionid']);// 必填参数，从前端获取，不可更改，android和ios只传这个参数即可
      $request->setToken($_POST['token']);// 必填参数，从前端获取，不可更改
      $request->setSig($_POST['sig']);// 必填参数，从前端获取，不可更改
      $request->setScene("nc_register");// 必填参数，从前端获取，不可更改
      $request->setAppKey("FFFF0N00000000008DD3");//必填参数，后端填写
      $request->setRemoteIp($_SERVER["REMOTE_ADDR"]);//必填参数，后端填写

      $response = $client->getAcsResponse($request);//返回code 100表示验签通过，900表示验签失败
      if($response->Code==100)
      {
        $name = mysqli_real_escape_string($link,$_POST['user']);
        $classname = mysqli_real_escape_string($link,$_POST['classname']);
        $password = mysqli_real_escape_string($link,$_POST['password1']);
        $organization = mysqli_real_escape_string($link,$_POST['organization']);
        $organizationID = mysqli_real_escape_string($link,$_POST['organizationID']);
        $query = 'INSERT INTO people (id,name,classname,passwd,organizationID,organization,op,credit) VALUES (NULL, \''.$name.'\',\''.$classname.'\',\''.$password.'\',\''.$organizationID.'\',\''.$organization.'\',\'4\',\'100\');';
        $result = execute_bool($link,$query);
        $query = 'select * from people where name=\''.$name.'\'';
        $result = execute($link,$query);
        if(!$result)
        {
          header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php?message=数据库异常');
          exit();
        }
        create_people($link, $result[0]);
        header('Location: http://' . $_SERVER["HTTP_HOST"] .'/admin/message.php?message=注册成功');
        exit();
      }
      else
      {
        $message='人机验证未通过，请重试';
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
  <style>
    #main{
      position: absolute;
      width: 100%;
      height: 100%;
    }   
    #fromLogin{
      display: block;
      position: relative;
      top:20%;
    }
    #logo{
      display: block;
      margin-left: 38%;
      text-align: 50px;
    }
    #message{
      color: red;
    }
  </style>
  <title>注册</title>
</head>
<body>
<main id="mian">
  <form class="form-horizontal" id="fromLogin" method="post">
    <div class="form-group">
      <h1 id="logo">注册</h1>
    </div>
    <div class="form-group">
      <label for="exampleInputAccount9" class="col-sm-2 required">账号名</label>
      <div class="col-md-6 col-sm-10">
        <input type="text" class="form-control" id="exampleInputAccount9" name="user" placeholder="学号/用户名">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword4" class="col-sm-2 required" >密码</label>
      <div class="col-md-6 col-sm-10">
        <input type="password" class="form-control" id="exampleInputPassword4" name="password1" placeholder="密码">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword4" class="col-sm-2 required" >确认密码</label>
      <div class="col-md-6 col-sm-10">
        <input type="password" class="form-control" id="exampleInputPassword3" name="password2" placeholder="密码">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputAccount3" class="col-sm-2 required">昵称</label>
      <div class="col-md-6 col-sm-10">
        <input type="text" class="form-control" id="exampleInputAccount56" name="classname" placeholder="姓名/昵称">
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword4" class="col-sm-2 required" >选择组织</label>
      <div class="col-md-6 col-sm-10">
        <select class="form-control" name="organization">
          <option value="0">请选择一个组织</option>
<?php
  $query = 'select * from organization';
  $result = execute($link,$query);
  foreach ($result as $value)
  {
    echo '<option value="'.$value["id"].'">'.$value["name"].'</option>';
  }
?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label for="exampleInputAccount9" class="col-sm-2 required">组织内ID</label>
      <div class="col-md-6 col-sm-10">
        <input type="text" class="form-control" id="exampleInputAccount3" name="organizationID" placeholder="学号/号数">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <div id="your-dom-id" class="nc-container"></div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
      <!-- 验证 -->
        <div id="message"><?php if($message){echo $message;} ?></div>
        <input type="text" name="token" style="display:none;"  id="token_id">
        <input type="text" name="sessionid" style="display:none;"  id="sessionid_id">
        <input type="text" name="sig" style="display:none;"  id="sig_id">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-default btn-primary">注册</button>
      </div>
    </div>
  </form>
</main>
</body>
<script src="../style/js/jquery.js"></script>
<script src="../style/js/bootstrap.js"></script>
<script src="../style/js/zui.js"></script>
<script type="text/javascript" charset="utf-8" src="//g.alicdn.com/sd/ncpc/nc.js?t=2015052012"></script>
<script type="text/javascript">
var nc_token = ["FFFF0N00000000008DD3", (new Date()).getTime(), Math.random()].join(':');
var NC_Opt = 
{
    renderTo: "#your-dom-id",
    appkey: "FFFF0N00000000008DD3",
    scene: "nc_register",
    token: nc_token,
    customWidth: 300,
    trans:{},
    elementID: ["usernameID"],
    is_Opt: 0,
    language: "cn",
    isEnabled: true,
    timeout: 3000,
    times:5,
    apimap: {
        // 'analyze': '//a.com/nocaptcha/analyze.jsonp',
        // 'get_captcha': '//b.com/get_captcha/ver3',
        // 'get_captcha': '//pin3.aliyun.com/get_captcha/ver3'
        // 'get_img': '//c.com/get_img',
        // 'checkcode': '//d.com/captcha/checkcode.jsonp',
        // 'umid_Url': '//e.com/security/umscript/3.2.1/um.js',
        // 'uab_Url': '//aeu.alicdn.com/js/uac/909.js',
        // 'umid_serUrl': 'https://g.com/service/um.json'
    },   
    callback: function (data) { 
        document.getElementById('token_id').value = nc_token;
        document.getElementById('sessionid_id').value = data.csessionid;
        document.getElementById('sig_id').value = data.sig;
    }
}
var nc = new noCaptcha(NC_Opt)
nc.upLang('cn', {
    _startTEXT: "请按住滑块，拖动到最右边",
    _yesTEXT: "验证通过",
    _error300: "哎呀，出错了，点击<a href=\"javascript:__nc.reset()\">刷新</a>再来一次",
    _errorNetwork: "网络不给力，请<a href=\"javascript:__nc.reset()\">点击刷新</a>",
})
</script>
</html>
<?php
  close_mysql($link);