<?php 
//后台新闻控制器
function index(){
    $rows = page('news','*','','time desc');
    
    foreach($rows['rows']  as &$v){
        $v['time'] = date('Y-m-d H:i:s',$v['time']);
    }
    
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
        echo json_encode($rows);
        die;
    }
    view('',array('data' => $rows));
}

//新增数据
function add(){
    $errstr = '';
    if(isset($_POST['title'])){
        //1.判断类型 image/png image/jpg image/jpeg image/gif
        //2.控制文件的大小
        //3.获取文件的扩展名
        //4.给上传的文件取一个随机的名字
        //5.确定保存的位置，将临时文件移动到指定的位置，并重命名为心得名字
        //6.文件名保存到数据库
        $image = $_FILES['image'];
        $fileType = array('image/png','image/jpg','image/jpeg','image/gif');
        $maxSize = 1024*1024;   //不能超过1MB
        if(in_array($image['type'],$fileType)){
            if($image['size'] < $maxSize){
                $exp = strrchr($image['name'],'.'); //扩展名
                $dir = 'uploads/'.$GLOBALS['c'].'/'.date('Y-m-d').'/';
                if(!is_dir($dir)){  //判断目录是否存在
                    mkdir($dir,777,true);   //创建目录
                }
                //rand，第一个参数表示最小的随机数，第二个参数表示最大参数，就比如下面的是四个随机数
                $file = time().rand(1000,9999).$exp;    //这里的文件名是时间戳+四位随机数
                $file = $dir.$file;
                move_uploaded_file($image['tmp_name'],$file);
            }else{
                $errstr = '文件不能超过1MB';
            }
        }else{
            $errstr = '"$fileTy"文件类型不支持';
        }
        
        
        $_POST['time'] = time();
        $i = dbAdd('news',$_POST);
        if($i){
            $url = url('index');
            header("location:$url");
        }else{
            $errstr = '添加失败！';
        }
    }
    $category = findAll('category','id,title',"type='news'");
    view('',array('errstr' => $errstr,'category' => $category));
}

//修改
function edit(){
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $row = findOne('news','*',"id='$id'");
        $errstr = "";
        if($row){
            if(isset($_POST['title'])){
                $i = dbUpdate('news', "id='$id'");
                if($i){
                    $url = url('index');
                    header("location:$url");
                }else{
                    $errstr = "修改失败！";
                }
            }
            $category = findAll('category','id,title',"type='news'");
            view('',array('row'=>$row,'errstr'=>$errstr,'category' => $category));
        }else{
            echo '修改失败！';
        }
        
    }else{
        echo '修改失败！';
    }
    
}

//删除
function del(){
    if(isset($_POST['id'])){
        $id = $_POST['id'];
        if(is_numeric($id)){
            $i = dbDelete('news', "id='$id'");
            if($i){
//                 $url = url('index');
//                 header("location:$url");
                echo 'Y';
                die;
            }
        }
    }
    echo '删除失败！';
}

?>