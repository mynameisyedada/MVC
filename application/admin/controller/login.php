<?php
//登录控制器
function index(){
    $errstr = '';
    $usr = '';
    if(isset($_POST['usr'])){
        if(strtoupper($_SESSION['VerifyCode']) == strtoupper($_POST['vcode'])){
            $pwd = md5($_POST['pwd']);
            //sql执行语句
            $row = findOne('users','*',"username='{$_POST['usr']}' and password='$pwd'");
            if($row){
                $_SESSION['username'] = $_POST['usr'];
                if($_POST['remeber'] == 1){
                    setcookie('username',$_POST['usr'],time()+24*60*60);
                }
                $url = url('index/index');
                header("location:$url");
            }else{
                $errstr = '账号或者密码错误！';
                $usr = $_POST['usr'];
            }
            
        }else{
            $errstr = '验证码错误！';
        }
        
        
        
    }
    
    
    //密码，错误提示
    view('',array('usr'=>$usr,'errstr'=>$errstr),false);
}

//验证码
function getcode(){
    vCode();
}



//退出
function logout(){
    //删除用户名
    unset($_SESSION['username']);
    //把时间戳改成负数
    setcookie('username','',time()-3600);
    
    //跳转到首页
    $url = url('index');
    header("location:$url");
}