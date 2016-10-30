<?php

/*在首页显示查询的次数*/
 $times=file_get_contents('../info/count.txt');
 $arr=array('times'=>$times);
 echo json_encode($arr);
