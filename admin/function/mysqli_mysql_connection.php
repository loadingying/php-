<?php
  function link_mysql(){
    $link=mysqli_connect('mysql','activityDate','#########','activityDate','3306');
    if(mysqli_connect_errno())
    {
      exit(mysqli_connect_error());
    }
    mysqli_set_charset($link,'utf8');
    return $link;
  }
  function close_mysql($link){
    if($link)
    {
      mysqli_close($link);
    }
    return true;
  }
  function execute($link,$query)
  {
    $result = mysqli_query($link,$query);
    if(mysqli_connect_errno())
    {
      exit(mysqli_connect_error());
    }
    if($result)
    {
      $result = mysqli_fetch_all($result,MYSQLI_ASSOC);
    }
    return $result;
  }
  function execute_bool($link,$query)
  {
    $result = mysqli_query($link,$query);
    if(mysqli_connect_errno())
    {
      exit(mysqli_connect_error());
    }
    return $result;
  }
