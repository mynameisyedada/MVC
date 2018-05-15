<?php
//热行SQL语句，并根据返回类型来返回数据
   function query($sql,$type="select"){
//        echo $sql;
        $link = @mysqli_connect(HOST,USER,PASSWORD,DATABASE) or die('数据库连接失败'); //连接
        mysqli_set_charset($link,'utf8'); //定义编码
        $rel = mysqli_query($link,$sql);  //执行
        switch($type){
            case 'select':
                //如果是查询时，返回数组
                $data = [];
                if($rel){   //如果执行的结果没有数据
                    while($row = mysqli_fetch_assoc($rel)){  //遍历数据结果集
                        $data[] = $row;
                    }
                    return $data;
                }else{ //否则返回false
                    return false;
                }
                 break;
            case 'insert':
                //如果是新增，返回新增ID
                return mysqli_insert_id($link);  //返回新增的ID
                break;
            case 'update':
            case 'delete':
                //如果是删除或修改，返回受影响的行数
                return mysqli_affected_rows($link);
        }
        return false;
   }

   //获取表中的所有字段
   function getCols($table){
        $sql = "show columns from `$table`";  
        $data = query($sql);       
        $data = array_column($data,'Field');
        return $data;
   }

   //新增数据，默认数据来源是$_POST
   function dbAdd($table,$data = false){       
        if(!$data){  //如果没有数据就用$_POST中的数据
            $data = $_POST;
        }
        $colarr = [];
        $valarr = [];
        $cols = getCols($table);  //获取所有字段名
        foreach($data as $k => $v){   //遍历数组，将键和值分开，并且键要加上反引号，值要加上单引号
            if(in_array($k,$cols)){
                $colarr[] = "`$k`";
                $valarr[] = "'$v'";
            }
        }
       
        $colstr = implode(',',$colarr);  //转换成字符串，用，拼接
        $valstr = implode(',',$valarr);
        $sql = "insert into `$table`($colstr)values($valstr)"; 
        return query($sql,'insert');
   }

   //删除
   function dbDelete($table,$where){
        //  "`id`= '' or 1 "         
        if(empty($where)){  //如果条件为空，不执生删除，防止误删
            return false;
        }
        $sql="delete from  `$table`  where  $where ";
        return query($sql,'delete'); 
   }

   //更新，默认数据来源是$_POST
   function dbUpdate($table,$where,$data=false){
        if(empty($where)){
            return false;
        }
        if(!$data){
            $data=$_POST;
        }
        $set='';

        $cols=getCols($table);
        foreach($data as $k=>$v){   
            if(in_array($k,$cols)){
                $set.="`$k`='$v',";
            }
            
        }         
        $set=substr($set,0,-1);

        $sql="update `$table`  set $set   where $where ";
        return query($sql,'update');
   }

   //查找一条数据，返回一个一维数组   
   function findOne($table,$field='*',$where='',$order=''){
        if(!empty($where)){
           
            $where=" where $where ";
        }

        if(!empty($order)){
            $order="order by $order";
        }

        $sql="select $field from `$table` $where  $order limit 1";

        $rows = query($sql);
        if($rows){
            return $rows[0];
        }else{
            return false;
        }
   }

   //查找数据返回一个二维数组 
    function findAll($table,$field='*',$where='',$order='',$limit=''){
        if(!empty($where)){
           
            $where=" where $where ";
        }

        if(!empty($order)){
            $order="order by $order";
        }

        if(!empty($limit)){
            $limit=" limit $limit ";
        }

        $sql="select $field from `$table` $where  $order  $limit";

        return query($sql);
    }

    //获取表中满足条件数据的条数
    function getCount($table,$where=''){
        if(!empty($where)){
            $where=" where $where ";
        }
        $sql = "select count(*) from `$table` $where";
       $rows = query($sql);
       return $rows[0]['count(*)'];
    }
    /*
        分页

        $table  表名
        $field  字段


    */
    /**
     * 分页按钮
     * @param $table    表名
     * @param $field    字段
     * @param $where    条件
     * @param $order    排序
     * @param $num      显示多少条
     * @param $btn_count    按钮个数
     */
    function page($table,$field='*',$where='',$order='',$num=10,$btn_count=5){

        $page=empty($_GET['page']) ? 1 : $_GET['page'];  //获取当前页码，如没有就是第1页 
       
        $count=getCount($table);  //总记录数
        $page_count= ceil($count/$num); //总页数

        if($page>$page_count){
            $page=$page_count;
        }

        if($page<1){
            $page=1;
        }

        if($btn_count>$page_count){  //按钮数不能大于总页数
          $btn_count=$page_count;
        }

        $limit=($page-1)*$num;//计算limit字句的第一个参数，分页数据的起始位置
        $rows=findAll($table,$field,$where,$order,"$limit,$num");

        $lastpage=$page-1;  //上一页

        if($lastpage<1){  //消除bug  ，上一页不能小于1
          $lastpage=1;
        }

        $nextpage=$page+1;  //下一页

        if($nextpage>$page_count){  //下一页不能大于总页数
          $nextpage=$page_count;
        }

        $tmp=floor($btn_count/2);  //在当前页两边显示的按钮数

        $startBtn=$page-$tmp;  //开始按钮=当前页-$tmp
        $endBtn=$page+$tmp;     //结束按钮=当前页+$tmp

        if($startBtn<1){   //当开始按钮小于1时，开始按钮=1，结束按钮=总按钮数
           $startBtn=1;
           $endBtn=$btn_count;

        }

        if($endBtn>$page_count){  //结束按钮大于总页数时，结束按钮=总页数，开始按钮=总页数-总按钮数+1
          $endBtn=$page_count;
          $startBtn=$page_count-$btn_count+1;
        }

        return [
            'rows'=>$rows,
            'page'=>$page,
            'page_count'=>$page_count,
            'startBtn'=>$startBtn,
            'endBtn'=>$endBtn,
            'lastpage'=>$lastpage,
            'nextpage'=>$nextpage,
        ];
    }

    function addDefreatUser(){
        $data = array(
            'username'=>'admin123',
            'pwd'=>md5('admin'),
            'mail'=>'111@qq.com',
            'ctime'=>time()
        );
        dbAdd('users',$data);
    }

    function checkLogin(){
        return isset($_SESSION['username']);
    }