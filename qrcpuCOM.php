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
	public $is_test = true;//开启后返回测试信息,免走接口
	public $appcode = '';
	public $cpu_id = '';
	public $cpu_key = '';
	public $timestamp = 0;
	public $host = 'http://cpu.market.alicloudapi.com';

	
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
		$this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;


	}

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
			'msg'=>'暂时数据',
		];

		$path = '/template.html';
		$query_data = [
			'signature'=>md5($this->cpu_key . $this->timestamp.$cat_id.$kwd.$page_index.$page_size),//简易签名
		];
		$this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}
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
			'signature'=>md5($this->cpu_key . $this->timestamp.$template_id),//简易签名
		];
		$this->curl_request($path,$query_data);
	
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
			'qrdata'=>$qrdata,
		];
		$this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;
	}

	
	/*
		图片转base64
	*/
	public function base64_encode_image ($image_file) {
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
			'msg'=>'生成失败',
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
			'imgdata'=>$imgdata,
		];
		$this->curl_request($path,$query_data);
	
		//解析json
		$content = json_decode($content,true);
		if($content){
			return $content;
		}
		return $error_rs;

	}





}

