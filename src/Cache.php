<?php 

namespace droid15;

//默认缓存目录，建议不修改以免删除重要数据
define("CACHE_PATH", __DIR__.'/cache/');

class Cache {	
	
	/**
	 * 存缓存
	 * @param [string] $k     [缓存键名]
	 * @param [mix] $value [缓存内容]
	 */
	public static function set($k, $value)
	{

		if(!is_string($k) || !trim($k)){
			return false;
		}
		$key = CACHE_PATH.sha1(md5($k.date('Ymd')));

		if(!is_dir(CACHE_PATH)){
			mkdir(CACHE_PATH);
		}

		if(is_object($value)){
			$value = serialize($value);
		}

		if(is_array($value)){
			$value = json_encode($value,JSON_UNESCAPED_UNICODE);
		}

		$res = file_put_contents($key, $value,LOCK_EX);
		return $res;
	}

	/**
	 * 取缓存
	 * @param  [type]  $key     [缓存名]
	 * @param  boolean $distory [是否清理过期缓存]
	 * @return [type]           [description]
	 */
	public static function get($key, $distory=true)
	{
		if(!is_string($key) || !trim($key)){
			return false;
		}
		//取当天的缓存
		$name = sha1(md5($key.date('Ymd')));
		$key = CACHE_PATH.$name;

		if(!file_exists($key)){
			return false;
		}

		if($distory){
			$files = scandir(CACHE_PATH);
			foreach ($files as $k => $v) {
				if($v!='.' && $v!='..' && $v!=$name){
					@unlink(CACHE_PATH.$v);
				}
			}	
		}

		$res = file_get_contents($key);
		return $res;
	}

	/**
	 * 清理缓存目录下所有缓存
	 * @return [type] [description]
	 */
	public static function clearAll()
	{
		if(!is_dir(CACHE_PATH)){
			return false;
		}

		$files = scandir(CACHE_PATH);
		foreach ($files as $k => $v) {
			if($v!='.' && $v!='..'){
				@unlink(CACHE_PATH.$v);
			}
		}	
		
		return true;		
	}
}