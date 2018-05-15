<?php 
//index控制器
/**
 * index操作
 */
function index(){
    $url = url('login/logout');
    echo "<a href='$url'>退出</a>";
}
// index();