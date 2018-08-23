<?php

namespace  samples

/*
	安装方式
	1. 通用方式 include_once  若不用 命名空间，请注释掉  qrcpuCOM.php  的 namespace qrcpu;
	2. 通过 composer 安装
*/
include_once('../qrcpuCOM.php');

use qrcpu\qrcpuCOM;


/*
	配置
	1.购买套餐 ：https://market.aliyun.com/products/57126001/cmapi021204.html
	2.查看appcode ：https://market.console.aliyun.com/
	3.获取cpu_id和cpu_key ：http://www.qrcpu.com/user/dev.html
*/
$config = array(
	'appcode'=> '7a11d5482f274b788d7---------',
	'cpu_id'=>'cpu12------',
	'cpu_key'=>'afVvsKl------k',
);
$qrcpu = new qrcpuCOM($config);

//解码
$imgurl = 'http://www.wwei.cn/static/images/ad/tzm.jpg';//远程图片
$imgpath = '';//'./qrcode.jpg';//本地图片
$qrdata = $qrcpu->qrdecode($imgurl,$imgpath);
var_dump($qrdata);

//生成
$template_id = 159;//模板ID
$qrdata = '二维码内容';
$result = $qrcpu->qrencode($template_id,$qrdata);
var_dump($result);

