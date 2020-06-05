<?php




    // header('Content-type:application/octet-stream'); 
    // phpinfo();
    // use SimpleXMLElement;
    $data = function_exists("mysqli_connect");
    var_dump($data);

    // echo dirname(__DIR__).DIRECTORY_SEPARATOR;


    // include_once 'function/mysqli_mysql_connection.php';
    // $link = link_mysql();
    // include_once '../admin/function/activity_people.php';
    // $query = 'select * from activity where id=12';
    // $result = execute($link,$query);
    // create_activity($link,$result[0]);
    var_dump($_POST);
    var_dump($_FILES);




    // create_people('','');

    // $file = 'x.y.z.png';
    // echo substr(strrchr($file, '.'), 1);



//   date_default_timezone_set('Asia/Shanghai');
//   $link = link_mysql();
//   echo '<br>';
//   $date = mysqli_real_escape_string($link,date('Y-m-d H:i:s'));
// $query = 'select * from activity where unix_timestamp(signstarttime) < unix_timestamp(\''. $date . '\') ';
//   var_dump($query); 
//   echo '<br>';
//   $result = mysqli_query($link,$query);
//   $result = mysqli_fetch_all($result,MYSQLI_ASSOC);
//   var_dump($result);
//   echo '<br>';



?>
<form id="fromed" method="post" enctype="multipart/form-data" >
<input type="file" class="form-control" name="file[]" id="file" multiple>
<button type="submit" class="btn btn-default" id="submit">提交</button>
</from>