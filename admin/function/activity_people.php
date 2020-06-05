<?php
  include_once 'mysqli_mysql_connection.php';
  //查询人员活动状态
  function query_people_activity($link,$actId,$peopleId)
  {
    $query = "select * from peopleandactivity where activityid='".$actId."' and peopleid='".$peopleId."'";
    $result = execute($link,$query);
    if(!$result)
    {
      $query = "insert into peopleandactivity(id,peopleid,activityid,sort,status) VALUES (NULL,'".$peopleId."', '".$actId."', '0', '0')";
      $result = execute_bool($link,$query);
      $query = "select * from peopleandactivity where activityid='".$actId."' and peopleid='".$peopleId."'";
      $result = execute($link,$query);
    }

    return $result[0]['status'];
  }
  //人员状态转字符串
  function query_people_activity_string($status)
  {
    if($status==3){ return '已到场';}
    elseif($status==2){ return '已录取';}
    elseif($status==4){ return '未到场';}
    elseif($status==1){ return '已报名';}
    else{ return '未报名';}
  }
  //查询是否直接录取
  function query_activity_sign_people($link,$actId)
  {
    $query = "select * from activityoption where activity='".$actId."' and name='signpeople'";
    $result = execute($link,$query);
    if(!$result)
    {
      $query = "insert into activityoption(id,name,activity,value) VALUES (NULL,'signpeople', '".$actId."', '1')";
      $result = execute_bool($link,$query);
      $query = "select * from activityoption where activity='".$actId."' and name='signpeople'";
      $result = execute($link,$query);
    }
    return $result[0]['value'];
  }
  //获取签到密码

  function get_start_passwd($link,$actId)
  {
    $query = "select * from activityoption where activity='".$actId."' and name='startpasswd'";
    $result = execute($link,$query);
    if(!$result)
    {
      $query = "insert into activityoption(id,name,activity,value) VALUES (NULL,'startpasswd', '".$actId."', '".rand(100000,999999)."')";
      $result = execute_bool($link,$query);
      $query = "select * from activityoption where activity='".$actId."' and name='startpasswd'";
      $result = execute($link,$query);
    }
    return $result[0]['value'];
  }
  //更新签到密码
  function update_start_passwd($link,$actId)
  {
    $query = "update activityoption set value='".rand(100000,999999)."' where activity='".$actId."' and name='startpasswd'";
    $result = execute_bool($link,$query);
    return true;
  }
  //创建人员
  function create_people($link,$People)
  {
    $query = "select * from activity where organization='".$People['organization']."'";
    $results = execute($link,$query);
    foreach($results as $value)
    {
      $query = "insert into peopleandactivity(id,peopleid,activityid,sort,status) VALUES (NULL,'".$People['id']."', '".$value['id']."', '0', '0')";
      $result = execute_bool($link,$query);
    }
    return true;
  }
  //删除人员
  function del_people($link,$People)
  {
    $query = "DELETE FROM peopleandactivity where peopleid='".$People['id']."'";
    $result = execute_bool($link,$query);
    return true;
  }
  //创建活动
  function create_activity($link,$act)
  {
    $query = "select * from people where organization='".$act['organization']."'";
    $results = execute($link,$query);
    foreach($results as $value)
    {
      $query = "insert into peopleandactivity(id,peopleid,activityid,sort,status) VALUES (NULL,'".$value['id']."', '".$act['id']."', '0', '0')";
      $result = execute_bool($link,$query);
    }
    return true;
  }
  //删除活动
  function del_activity($link,$actId)
  {
    $query = "DELETE FROM peopleandactivity where activityid='".$actId."'";
    $result = execute_bool($link,$query);
    return true;
  }
  //签到
  function set_start($link,$actId,$peopleId)
  {
    $query = "update peopleandactivity set status='3' where activityid='".$actId."' and peopleid='".$peopleId."'";
    $result = execute_bool($link,$query);
    return true;
  }
  //未到场处理
  function sub_credit($link,$peopleId)
  {
    $query = "select * from people where id='".$peopleId."'";
    $result = execute($link,$query);
    $credit = $result[0]['credit']-1;
    $query = "update people set credit=".$credit." where id='".$peopleId."'";
    $result = execute_bool($link,$query);
    return true;
  }