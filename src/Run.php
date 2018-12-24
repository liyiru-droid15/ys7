<?php 

namespace droid15;
use droid15\Cache;
class Run{

	static $appKey;
	static $appSecret;

	public function __construct($appKey=null,$appSecret=null)
	{
		if(!$appKey || !$appSecret || !is_string($appKey) || !is_string($appSecret)){
			die('Need appKey and appSecret');
		}
		self::$appSecret = $appSecret;
		self::$appKey = $appKey;
	}

	//设备列表
	public function deviceList($parm=[])
	{
		
		$url = 'https://open.ys7.com/api/lapp/device/list';
		$data = [];
		if(isset($parm['pageStart'])){
			$data['pageStart'] = (int)$parm['pageStart'];
		}
		if(isset($parm['pageSize'])){
			$data['pageSize'] = (int)$parm['pageSize'];
		}
		return $this->queryApi($data,$url);
	}

	//添加设备
	public function deviceAdd($parm)
	{
		$url = 'https://open.ys7.com/api/lapp/device/add';
		//序列号
		$data['deviceSerial'] = isset($parm['deviceSerial'])? $parm['deviceSerial'] : '';
		//验证码（设备机身上的六位大写字母）
		$data['validateCode'] = isset($parm['validateCode'])? $parm['validateCode'] : '';
		if(!$data['deviceSerial']||!$data['validateCode']){
			return false;
		}
		return $this->queryApi($data,$url);
	}

	//删除设备
	public function deviceDel($parm)
	{
		$url = 'https://open.ys7.com/api/lapp/device/delete';
		//序列号
		$data['deviceSerial'] = isset($parm['deviceSerial'])? $parm['deviceSerial'] : '';
		if(!$data['deviceSerial']){
			return false;
		}
		return $this->queryApi($data,$url);
	}

	//设备改名
	public function deviceRename($parm)
	{
		$url = 'https://open.ys7.com/api/lapp/device/name/update';
		$data['deviceSerial'] = isset($parm['deviceSerial'])? $parm['deviceSerial'] :'';
		$data['deviceName'] = isset($parm['deviceName'])? $parm['deviceName'] :'';
		if(!$data['deviceSerial']||!$data['deviceName']){
			return false;
		}
		return $this->queryApi($data,$url);
	}

	//云台
	public function ptz($parm)
	{
		$url = 'https://open.ys7.com/api/lapp/device/ptz/start';
		$data = [];
		//设备序列号，通道号，指令(上下左右)，速度
		$arr = ['deviceSerial','channelNo','direction','speed'];
		foreach ($arr as $k => $v) {
			if(!isset($data[$v])){
				return false;
			}
		}
		return $this->queryApi($data,$url);
	}

	//获取直播地址
	public function getPlayUrl($parm)
	{
		$url = 'https://open.ys7.com/api/lapp/live/address/get';	
		if(!isset($parm['sn'])){
			return false;
		}
		if(!isset($parm['aisle'])){
			$aisle=1;
		}
		//[设备序列号]:[通道号],[设备序列号]:[通道号]
		$data['source'] = $parm['sn'].':'.$aisle;
		unset($parm['sn']);
		return $this->queryApi($parm,$url);
	}
	

	private function queryApi(array $data,$url)
	{
		$data['accessToken'] = $this->getToken();
		return $this->execPost($url,http_build_query($data));	
	}

	protected function getToken()
    {

   		$url = 'https://open.ys7.com/api/lapp/token/get';
   		$data = [
   			'appKey'=>self::$appKey,
   			'appSecret'=>self::$appSecret
   		];    	

    	if(Cache::get('accessToken')){
    		return Cache::get('accessToken');
    	}

    	$res = $this->execPost($url,http_build_query($data));
    	$result = json_decode($res,true);
    	if(!$result||$result['code']!=200){
    		return false;
    		//var_dump($res);die;
    	}
    	
    	Cache::set('accessToken', $result['data']['accessToken']);
    
    	return Cache::get('accessToken');		
    }

    protected function execPost($url,$data)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// post数据
		curl_setopt($ch, CURLOPT_POST, 1);
		// post的变量
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		//设置头部信息
		$headers = array('Content-Type:application/x-www-form-urlencoded; charset=utf-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		//执行请求
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
    }
}