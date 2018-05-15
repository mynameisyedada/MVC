<?php
//公共的函数 文件
/**
 * 显示的视图文件
 * @param $url 模块/控制器/操作
 */
function view($url = '',$var = array(),$layout = true){ //当惨数为空时，表示当前模块当前控制器当前操作的视图文件
    
    foreach ($var as $k => $v){
        $$k = $v;
    }
    
    if(empty($url)){
        $file = APP . "{$GLOBALS['m']}/view/{$GLOBALS['c']}/{$GLOBALS['a']}.html";
    }else{
        $arr = explode('/', $url);  //explode此函数返回由字符串组成的数组，每个元素都是 string 的一个子串，它们被字符串 delimiter 作为边界点分割出来。
        if(count($arr) == 1){   //通过判断数组内元素的个数，判断用户输入了几个参数
            //只输入一个，表示当前模块下的view文件夹下的当前控制器下的用户输入的操作.html
            $file = APP . "{$GLOBALS['m']}/view/{$GLOBALS['c']}/{$arr[0]}.html";
        }else if(count($arr) == 2){
            //输入两个参数表示，当前模块下的view文件夹下的用户输入的控制器下的用户输入的操作.html
            $file = APP . "{$GLOBALS['m']}/view/{$arr[0]}/{$arr[1]}.html";
        }else{
            $file = APP . "{$arr[0]}/view/{$arr[1]}/{$arr[2]}.html";
        }
    }
    //     $file = APP . "$m/view/$c/$a.html";
    
    $header_file = RES.'header.html';
    $footer_file = RES.'footer.html';
    
    if(is_file($file)){
        if($layout){
            include $header_file;
        }  
        
        include $file;
        
        if($layout){
            include $footer_file;
        }
        
    }else{
        die("视图{$file}文件没找到");
    }
}

/**
 * 生产一个url地址
 * @param $url 模块/控制器/操作
 */
function url($url = ''){
//     print_r($_SERVER);
//     die;
    if(empty($url)){
       return "{$_SERVER['PHP_SELF']}?m={$GLOBALS['m']}&c={$GLOBALS['c']}&a={$GLOBALS['a']}";
    }else{
        $arr = explode('/', $url);  //不是空数组
     
        if(count($arr) == 1){   //通过判断数组内元素的个数，判断用户输入了几个参数
             return "{$_SERVER['PHP_SELF']}?m={$GLOBALS['m']}&c={$GLOBALS['c']}&a={$arr[0]}";
        }else if(count($arr) == 2){
             return "{$_SERVER['PHP_SELF']}?m={$GLOBALS['m']}&c={$arr[0]}&a={$arr[1]}";
        }else{
             return "{$_SERVER['PHP_SELF']}?m={$arr[0]}&c={$arr[1]}&a={$arr[2]}";
        }
    }
}