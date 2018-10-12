<?php

namespace  samples;

/*
	安装方式
	1. 通用方式 include_once  若不用 命名空间，请注释掉  qrcpuCOM.php  的 namespace qrcpu;
	2. 通过 composer 安装
*/
include_once '../qrcpuCOM.php';

use qrcpu\qrcpuCOM;


/*
	配置
	1.购买套餐 ：https://market.aliyun.com/products/57126001/cmapi021204.html
	2.查看appcode ：https://market.console.aliyun.com/
	3.获取cpu_id和cpu_key ：http://www.qrcpu.com/user/dev.html
*/
$qrcpu_config = array(
	'appcode'=> '7a11--------6051634',//云市场购买API套餐后得到：appcode
	'cpu_id'=>'cpu2----4',//qrcpu.com 官网 > 开发者配置
	'cpu_key'=>'stM----hEaNq',//qrcpu.com 官网 > 开发者配置
);

$qrcpu = new qrcpuCOM($qrcpu_config);

/**/
//获取模板分类
$result = $qrcpu->category();
var_dump($result);


//获取模板列表
$cat_id = 0 ;
$kwd = '';
$page_index=1;
$page_size=10;
$result = $qrcpu->template($cat_id, $kwd,$page_index,$page_size);
var_dump($result);

//获取模板详情
$template_id = 3652;//模板ID
$result = $qrcpu->template_view($template_id);
var_dump($result);


//使用模板 生成二维码
$template_id = 3652;//模板ID
$qrdata = '二维码内容';
$result = $qrcpu->qrencode($template_id,$qrdata);
var_dump($result);
echo '<img src="'.$result['data'].'"/>';





//解码
$imgurl = 'http://www.wwei.cn/static/images/ad/tzm.jpg';//远程图片
$imgpath = '';//'./qrcode.jpg';//本地图片
$qrdata = $qrcpu->qrdecode($imgurl,$imgpath);
var_dump($qrdata);

