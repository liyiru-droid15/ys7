<?php 

namespace droid15;
class Cache{
	static $cachePath;
	public function __construct(){
		self::$cachePath = 'cache/';
	}

	public static function set($k,$value)
	{
		if(!is_string($k) || !trim($k)){
			return false;
		}
		$key = self::$cachePath.sha1(md5($k.date('Ymd')));
		if(is_object($value)){
			$value = serialize($value);
		}
		$res = file_put_contents($key, $value,LOCK_EX);
		return $res;
	}

	public static function get($k)
	{
		if(!is_string($k) || !trim($k)){
			return false;
		}
		$oldCache = self::$cachePath.sha1(md5( $k.date('Ymd',strtotime('-1 day')) ));
		if(file_exists($oldCache)){
			unlink($oldCache);
		}
		$key = self::$cachePath.sha1(md5($k.date('Ymd')));
		if(!file_exists($key)){
			return false;
		}

		$res = file_get_contents($key);
		return $res;
	}
}