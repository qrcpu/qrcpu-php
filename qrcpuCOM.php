<?php


/*
	配置
	1.购买套餐 ：https://market.aliyun.com/products/57126001/cmapi021204.html
	2.查看appcode ：https://market.console.aliyun.com/
	3.获取cpu_id和cpu_key ：http://www.qrcpu.com/user/dev.html
*/

/*

include_once('qrcpuCOM.php');
$config = array(
	'appcode'=> '7a11d5482f274b78-------',
	'cpu_id'=>'cpu16-------',
	'cpu_key'=>'afVvsKl-------',
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


返回信息说明
	status  0失败  1成功
	data 成功时返回数据，或失败时提示核对数据
	msg  失败时返回提示信息
*/

namespace qrcpu;


class qrcpuCOM
{
	public $is_test = false;//开启后返回测试信息,免走接口
	public $appcode = '';
	public $cpu_id = '';
	public $cpu_key = '';
	public $timestamp = 0;
	public $host = 'http://qrapi.market.alicloudapi.com';

	public $auto_compress = true;//base64前自动压缩图片
	public $compress_max_width = 600;
	public $compress_max_height = 800;
	public $compress_quality = 80;

	
	public function __construct($config)
	{
		$this->appcode = $config['appcode'];
		$this->cpu_id = $config['cpu_id'];
		$this->cpu_key = $config['cpu_key'];
		$this->timestamp = time();
	}

	/*
		curl_request aliyun
	*/
	public function curl_request($path,$query_data){
		
		//$path = '/qrdecode.html';
		if(!$path){
			return false;
		}
		//各自处理
		if(!isset($query_data['signature'])){
			return false;
		}

		$query_data['cpu_id'] = $this->cpu_id;
		$query_data['timestamp'] = $this->timestamp;
		//$query_data['signature'] = md5($this->cpu_key . $this->timestamp . $qrdata);
		$bodys = http_build_query($query_data);

		$headers = [
			'Authorization:APPCODE '.$this->appcode,
			'Content-Type:application/x-www-form-urlencoded; charset=UTF-8'
		];
	
		$url = $this->host . $path;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		if (1 == strpos("$".$this->host, "https://"))
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
		$content = curl_exec($curl);
		
		return $content;
	}
	

	/*
		获取全部分类信息
	*/
	public function category(){
		//test

		if($this->is_test)
		{
			return json_decode('{"status":1,"count":1,"data":[{"cat_id":29,"cat_name":"分类11"}],"msg":"success"}',true);
		}
		
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'获取失败',
		];

		$path = '/category.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp),//简易签名
		];
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;


	}
	/*
		获取模板列表
	*/
	public function template($cat_id = 0 , $kwd = '',$page_index=1,$page_size=10){
		

		//test
		if($this->is_test)
		{
			return json_decode('
{"status":1,"count":38,"page_index":0,"page_total":3,"data":[{"template_id":159,"title":"测试模板1","template_qrcode":{"cover":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png","thumb":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png"},"status":null,"status_msg":null,"totals":0,"totals_today":0,"dateline":1534489273}],"msg":"success","cat_list":[{"cat_id":29,"cat_name":"分类11"}]}',true);

		}
		
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'暂无数据',
		];

		if($page_index<=0){
			$page_index = 1;
		}
		if($page_size<=0){
			$page_size = 10;
		}


		$path = '/template.html';
		$query_data = [
			'cat_id'=>$cat_id,
			'kwd'=>$kwd,
			'page_index'=>$page_index,
			'page_size'=>$page_size,
			'signature'=>md5($this->cpu_key . $this->timestamp.$cat_id.$kwd.$page_index.$page_size),//简易签名
		];
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}
	/*
		获取模板详情
	*/
	public function template_view($template_id = 0){
		
		//test
		if($this->is_test)
		{
			return json_decode('{"status":1,"data":{"template_id":159,"cat_id":0,"title":"测试模板1","totals":210,"content":"asdfcontent","copyright":"3333copyright","totals_today":0,"totals_time":0,"dateline":1534489273,"template_qrcode":{"cover":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png","thumb":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png"}},"msg":"success"}',true);

		}
		
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'未找到模板',
		];

		$path = '/template/view.html';
		$query_data = [
			'template_id'=>$template_id,
			'signature'=>md5($this->cpu_key . $this->timestamp.$template_id),//简易签名
		];
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}

	/*
		彩色二维码
	*/
	public function qrcustom($param){

		if($this->is_test)
		{
			return json_decode('{"status":1,"data":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png","expires_in":1534839257,"msg":"success"}',true);
		}
		
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'生成失败',
		];

		$param['qrdata'] = trim($param['qrdata']);//这里不用 urlencode 

		$path = '/qrcustom.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp),//简易签名
		];
		$query_data = array_merge($query_data,$param);

		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}

	/*
		调用api网关
		return array
	*/
	public function qrencode($template_id = 0,$qrdata = ''){

		//test
		if($this->is_test)
		{
			return json_decode('{"status":1,"data":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png","expires_in":1534839257,"msg":"success"}',true);
		}

		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'生成失败',
		];
		
		if($template_id <=0){
			$error_rs['msg'] = '请选择二维码模板';
			return $error_rs;
		}
		if(empty($qrdata)){
			$error_rs['msg'] = '请输入二维码内容';
			return $error_rs;
		}


		$path = '/qrencode.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp . $template_id.$qrdata),//简易签名
			'template_id'=>$template_id,
			'qrdata'=>urlencode($qrdata),//注意要时行 urlencode
		];
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;
	}
	
		

	/** 
	* 简单的压缩图片为 jpg
	* @param sting $imgsrc 图片保存路径 
	* @param string $imgdst 压缩后保存路径 [空则替换原图，压缩非jpg格式建议传参数] 
	* @param int $max_width = 600;//压缩后最大宽度
	* @param int $max_height = 800;//压缩后最大高度
	* @param int $quality = 80;//压缩质量
	*/
	public function easy_compress($imgsrc,$imgdst='',$max_width = 600,$max_height = 800,$quality = 80){ 
	
		if($this->compress_max_width>0)
		{
			$max_width = $this->compress_max_width;
		}

		if($this->compress_max_height>0)
		{
			$max_height = $this->compress_max_height;
		}

		if($this->compress_quality>0)
		{
			$quality = $this->compress_quality;
		}
		if(empty($imgdst)){
			$imgdst = $imgsrc;
		}

		list($width,$height,$img_type) = getimagesize($imgsrc); 

		$new_width = $width;
		$new_height = $height;
	 
		if(($max_width && $width > $max_width) || ($max_height && $height > $max_height))
		{
			if($max_width && $width>$max_width)
			{
				$widthratio = $max_width/$width;
				$resizewidth_tag = true;
			}
	 
			if($max_height && $height>$max_height)
			{
				$heightratio = $max_height/$height;
				$resizeheight_tag = true;
			}
	 
			if($resizewidth_tag && $resizeheight_tag)
			{
				if($widthratio<$heightratio)
					$ratio = $widthratio;
				else
					$ratio = $heightratio;
			}
	 
			if($resizewidth_tag && !$resizeheight_tag)
				$ratio = $widthratio;
			if($resizeheight_tag && !$resizewidth_tag)
				$ratio = $heightratio;
	 
			$new_width = $width * $ratio;
			$new_height = $height * $ratio;
		}

	  switch($img_type){ 
		case 1: 
			$image_wp=imagecreatetruecolor($new_width, $new_height); 
			$image = imagecreatefromgif($imgsrc); 
			imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
			imagejpeg($image_wp, $imgdst,75); 
			imagedestroy($image_wp); 
			break; 
		case 2: 
			$image_wp=imagecreatetruecolor($new_width, $new_height); 
			$image = imagecreatefromjpeg($imgsrc); 
			imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
			imagejpeg($image_wp, $imgdst,75); 
			imagedestroy($image_wp); 
			break; 
		case 3: 
			$image_wp=imagecreatetruecolor($new_width, $new_height); 
			$image = imagecreatefrompng($imgsrc); 
			imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
			imagejpeg($image_wp, $imgdst,75); 
			imagedestroy($image_wp); 
			break; 
	  } 
	} 

	
	/*
		图片转base64
	*/
	public function base64_encode_image ($image_file) {
		
		//是否先压缩
		if($this->auto_compress)
		{
			$this->easy_compress($image_file);
		}
	
		$base64_image = '';
		$image_info = getimagesize($image_file);
		$image_data = fread(fopen($image_file, 'r'), filesize($image_file));
		$base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
		return $base64_image;
	}

	/*
		调用api网关
		响应http状态码，大于等于200小于300表示成功；大于等于400小于500为客户端错误；大于500为服务端错误。
		return array
	*/
	public function qrdecode($imgurl='',$imgpath=''){
		
		//test
		if($this->is_test)
		{
			return json_decode('{"status":1,"data":"test-qrdecode","msg":"success"}',true);
		}
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'解码失败',
		];


		$imgdata = '';
		if(empty($imgurl) && $imgpath){
			$imgdata = $this->base64_encode_image($imgpath);
		}

		//无效参数
		if(empty($imgurl) && empty($imgdata)){
			$error_rs['msg'] = 'imgurl和imgpath至少提供一个';
			return $error_rs;
		}
		if($imgdata){
			$imgurl = '';
		}

		$path = '/qrdecode.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp . $imgurl.$imgdata),//简易签名
			'imgurl'=>$imgurl,
			'imgdata'=>urlencode($imgdata),
		];
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}


	/*
		----不建议使用----
		wwei.cn 彩色二维码生成 
		更多参数请看：https://market.aliyun.com/products/57126001/cmapi021204.html
		$param = [
				'size'=>$qrsize,
				'qrdata'=>$qrdata,
				'xt'=>$xt_index,
				//码眼 +　前景
				'p_color'=>$qrcolor,
				'i_color'=>$qrcolor,
				'fore_color'=>$qrcolor,
			]
	*/
	public function yunapi_qrencode($param){
		//test

		if($this->is_test)
		{
			return json_decode('{"status":1,"data":"http:\/\/www.qrcpu.com/static\/images\/logo150x45.png","expires_in":1534839257,"msg":"success"}',true);
		}
		
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'获取失败',
		];

		$param['qrdata'] = urlencode($param['qrdata']);//注意要 urlencode 

		$path = '/yunapi/qrencode.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp),//简易签名
		];
		$query_data = array_merge($query_data,$param);
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			if($content['status'] ==1)
			{
				if(isset($content['data']['qr_filepath']))
				{
					//兼容旧版 - 统一返回格式
					return array(
						'status'=>1,
						'data'=>$content['data']['qr_filepath'],
						'msg'=>'success',
					);
				}
			}
			
			return $content;
			
		}
		return $error_rs;

	}

	/*
		----不建议使用----
		wwei.cn 解码
		return array
	*/
	public function yunapi_qrdecode($imgurl='',$imgpath=''){
		
		//test
		if($this->is_test)
		{
			return json_decode('{"status":1,"data":"test-qrdecode","msg":"success"}',true);
		}
		$error_rs = [
			'status'=>0,
			'data'=>'',
			'msg'=>'解码失败',
		];


		$imgdata = '';
		if(empty($imgurl) && $imgpath){
			$imgdata = $this->base64_encode_image($imgpath);
		}
		//无效参数
		if(empty($imgurl) && empty($imgdata)){
			$error_rs['msg'] = 'imgurl和imgpath至少提供一个';
			return $error_rs;
		}
		if($imgdata){
			$imgurl = '';
		}

		$path = '/yunapi/qrdecode.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp . $imgurl.$imgdata),//简易签名
			'imgurl'=>$imgurl,
			'imgdata'=>$imgdata,
		];
		$content = $this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}


}

