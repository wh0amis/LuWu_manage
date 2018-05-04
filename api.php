<?php
header('content-type:application/json;charset=utf8');
ini_set('date.timezone','Asia/Shanghai');
session_start();
error_reporting(0);
require("config/config.php");//包含数据库连接文件
$action = $_GET["type"];

//用户登录函数
//苏正东
//@2018/4/18
function login(){
  $username = htmlspecialchars($_POST['loginName']);
  $password = MD5($_POST['password']);
  //检测用户名及密码是否正确
  $check_query = mysql_query("select * from user where username='$username' limit 1");
  if($result = mysql_fetch_array($check_query)){
      if($password === $result["password"]){
          $userinfo=array($result['id'],$result['username'],$result['password'],time());
          $_SESSION["userinfo"]=$userinfo; 
          #$_SESSION['userinfo'] = $result['id']+$result['username']+$result['password']+time();
          $data =array(
          "success"=> true,
          "msg"=> "登录成功",
          "data"=> $result["username"]
          );
          echo json_encode($data);
      }else{
          $data =array(
          "success"=> false,
          "msg"=> "密码错误",
          "data"=> $result["username"]
          );
          echo json_encode($data);
      }
      #header("Location: login.php?errno=1");
      #exit();
  } else {
      $data =array(
      "success"=> false,
      "msg"=> "登录失败",
      "data"=> $result
      );
      echo json_encode($data);
      #header("Location: login.php?errno=1");
      #exit();
  }
}

//用户接口函数
//苏正东
//@2018/4/11

function user_list($page,$limit){
  $query = "select * from user";
  $count = mysql_num_rows(mysql_query($query));
  $result_query = mysql_query("select * from user limit {$page}0,{$limit}");
  $results = array();
    while ($row = mysql_fetch_assoc($result_query)) {
      $results[] = $row;
      }
    unset($results[0]["password"]);
    if($results){
      $data =array(
      "code"=> 0,
      "msg"=> "",
      "count"=> $count, //总数量
      "data"=> $results
      );
      echo json_encode($data);
    }else{
      return mysql_error();
    }
}


//用户注销函数
//苏正东
//@2018/4/18
function logout(){
  if(isset($_SESSION['userinfo'])){
        session_unset();
        session_destroy();//销毁一个会话中的全部数据
        setcookie(session_name(),'',time()-3600);//销毁与客户端的卡号
        header('location:login.php');
    }else{
        header('location:login.php');
    }
}




//数据接口函数
//苏正东
//@2018/4/11

function result($page,$limit){
  $query = "select * from v_Manage";
  $count = mysql_num_rows(mysql_query($query));
  $result_query = mysql_query("select * from v_Manage limit {$page}0,{$limit}");
  $results = array();
    while ($row = mysql_fetch_assoc($result_query)) {
      $results[] = $row;
      }
    if($results){
      $len = count($results);
      for($i=0;$i<$len;$i++){  //循环入栈，目的是给每个值压入一个存活天数
        $startdate = $results[$i]["d_time"];
        $life_time = floor((strtotime(date("Y-m-d h:i:s"))-strtotime($startdate))/86400);
        $results[$i]["life_time"] = "$life_time";
      }
      $data =array(
  		"code"=> 0,
  		"msg"=> "",
  		"count"=> $count, //总数量
  		"data"=> $results
  		);
  		echo json_encode($data);
    }else{
      return mysql_error();
    }
}


//漏洞新增or编辑函数
//苏正东
//2018/4/17

function add($id){
  if($id == 0){
    $vname = htmlspecialchars($_POST['vname']);
    $evaluator = htmlspecialchars($_POST['evaluator']);
    $d_time = htmlspecialchars($_POST['d_time']);
    $rank = htmlspecialchars($_POST['rank']);
    $b_system = htmlspecialchars($_POST['b_system']);
    $department = htmlspecialchars($_POST['department']);
    $if_fuce = htmlspecialchars($_POST['if_fuce']);
    $content = htmlspecialchars($_POST['content']);
    $username = htmlspecialchars($_POST['username']);
    $status = htmlspecialchars($_POST['status']);
    $r_time = date("Y-m-d h:i:s");
    if(!empty($vname)){
      $result = mysql_query("insert into v_manage (v_name,evaluator,d_time,rank,b_system,department,if_fuce,content,username,r_time,status) values('$vname','$evaluator','$d_time','$rank','$b_system','$department','$if_fuce','$content','$username','$r_time','$status')");
      $rc = mysql_affected_rows();
      if($rc === 1){
        $data =array(
          "success"=> true,
          "msg"=> "添加成功",
          "data"=> $result
        );
        echo json_encode($data);
      }else{
        $data =array(
        "success"=> false,
        "msg"=> "添加失败",
        "data"=> $result
        );
        echo json_encode($data);
      }
    }else{
      echo "参数错误";
    }
  }else{
    $vname = htmlspecialchars($_POST['vname']);
    $evaluator = htmlspecialchars($_POST['evaluator']);
    $d_time = htmlspecialchars($_POST['d_time']);
    $rank = htmlspecialchars($_POST['rank']);
    $b_system = htmlspecialchars($_POST['b_system']);
    $department = htmlspecialchars($_POST['department']);
    $if_fuce = htmlspecialchars($_POST['if_fuce']);
    $content = htmlspecialchars($_POST['content']);
    $username = htmlspecialchars($_POST['username']);
    $status = htmlspecialchars($_POST['status']);
    $r_time = date("Y-m-d h:i:s");
    if(!empty($vname)){
      $result = mysql_query("update v_manage SET v_name='$vname',rank='$rank',d_time='$d_time',content='$content',evaluator='$evaluator',b_system='$b_system',username='$username',r_time='$r_time',if_fuce='$if_fuce',department='$department',status='$status' WHERE id='$id'");
      $rc = mysql_affected_rows();
      if($rc === 1){
        $data =array(
          "success"=> true,
          "msg"=> "编辑成功",
          "data"=> $result
        );
        echo json_encode($data);
      }else{
        $data =array(
        "success"=> false,
        "msg"=> "编辑失败",
        "data"=> $result
        );
        echo json_encode($data);
      }
    }else{
      echo "参数错误";
    }
  }
}


//漏洞编辑函数--已整合到add函数
//苏正东
//2018/4/17
/*
function edit(){
  $id = $_GET["id"];
  $vname = htmlspecialchars($_POST['vname']);
  $evaluator = htmlspecialchars($_POST['evaluator']);
  $d_time = htmlspecialchars($_POST['d_time']);
  $rank = htmlspecialchars($_POST['rank']);
  $b_system = htmlspecialchars($_POST['b_system']);
  $department = htmlspecialchars($_POST['department']);
  $if_fuce = htmlspecialchars($_POST['if_fuce']);
  $content = htmlspecialchars($_POST['content']);
  $username = htmlspecialchars($_POST['username']);
  $r_time = date("Y-m-d h:i:s");
  if(!empty($vname)){
    $result = mysql_query("update v_manage SET v_name="$vname",rank="$rank",d_time="$d_time",content="$content",evaluator="$evaluator",b_system="$b_system",username="$username",r_time="$r_time",if_fuce="$if_fuce",department="$department" WHERE id="$id"");
    $rc = mysql_affected_rows();
    if($rc === 1){
      $data =array(
        "success"=> true,
        "msg"=> "编辑成功",
        "data"=> $result
      );
      echo json_encode($data);
    }else{
      $data =array(
      "success"=> false,
      "msg"=> "编辑失败",
      "data"=> $result
      );
      echo json_encode($data);
    }
  }else{
    echo "参数错误";
  }
}
*/

//漏洞删除函数
//苏正东
//2018/4/12

function del($id){
  if($id){
    $result = mysql_query("delete from v_Manage where id = '$id'");
    $rc = mysql_affected_rows();
    if($rc === 1){
      $data =array(
        "success"=> true,
        "msg"=> "删除成功",
        "data"=> $result
      );
      echo json_encode($data);
    }else{
      $data =array(
      "success"=> false,
      "msg"=> "删除失败",
      "data"=> $result
      );
      echo json_encode($data);
    }
  }else{
    echo "参数错误";
  }
}

switch($action)
{
  case "login":
    login();
    break;

  case "user_list":
    $page = intval($_GET["page"]) -1;
    $limit = intval($_GET["limit"]);
    user_list($page,$limit);
    break;

  case "logout":
    logout();
    break;

  case "list":
    $page = intval($_GET["page"]) -1;
    $limit = intval($_GET["limit"]);
    result($page,$limit);
    break;

  case "add":
    $id = $_POST["id"];
    add($id);
    break;

  case "edit":
    edit();
    break;

  case "del":
    $id = $_POST["id"];
    del($id);
    break;
}
exit();
?>

