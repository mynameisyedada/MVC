<?php
//开启session
session_start();
//设置编码
header('content-type:text/html;charset=utf-8'); 
//定义时区
date_default_timezone_set("PRC");
//初始化文件
include COMMON . 'config.php';  //设置文件
include COMMON . 'db.fun.php';  //mysql函数文件
include COMMON . 'function.php';//视图文件
include COMMON . 'img.fun.php'; //验证码文件

// http://127.0.0.1/yedada/mvc/index.php?m=admin&c=index&a=index
//从get请求里面url获取地址
$m = empty($_GET['m'])?'site':$_GET['m'];   //模块,默认是前台
$c = empty($_GET['c'])?'index':$_GET['c'];  //控制器
$a = empty($_GET['a'])?'index':$_GET['a'];  //操作

//开始登陆验证
if($m = 'admin'){
    if(isset($_SESSION['username'])){
        //已经登录，不能去登录页面
        if($c == 'login' && $a == 'index'){ //如果已经登录，不能去的登录页面
            $url = url('index/index');
            header("location:$url");
        }
    }else{
        if(isset($_COOKIE['username'])){    //没有session，但有cookie
            $_SESSION['username'] = $_COOKIE['username'];   //把cookie的值传递给session
            if($c == 'login' && $a == 'index'){ //如果已经登录不能去登录页面
                $url = url('index/index');
                header("location:$url");
            }
        }else{  //没有登录
            //只要不是在登录页面,就不用网登录页面跳
            if($c != 'login'){
                $url = url('login/index');
                header("location:$url");
            }
        }
    }
}

// <base href="<?$GLOBALS['res']"? >>
// <base href="<?=RES ? >" />
// <base href="<?=$res ? >" />
$res = APP.$m.'/view/public/';  //定义资源文件路径

define('RES',$res);

//资源文件
$file = APP . $m . '/controller/' . $c . '.php';    //拼接控制器文件地址
if(is_file($file)){ //判断这个文件是否存在
    include $file;  //存在就引入文件
    $a();   //调用这个$a就可以
}else{
    die("没有{$c}这个控制器文件");
}