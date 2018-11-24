<?php
//
include_once("IRequestCache.php");
abstract class AbstractRequestCache implements IRequestCache
{
	protected static $curl;
	protected static $db;
	protected static $curl_request;
	protected static $db_request;
	
	public static function init()
	{
		include("db_config.inc.php");
		self::my_curl_reset();
	}
	public static function close()
	{
		curl_close(self::$curl);
		self::$db=NULL;
	}
	public static function get_curl_request()
	{
		return self::$curl_request;
	}
	public static function get_db_request()
	{
		return self::$db_request;
	}
	public static function my_curl_reset()
	{
		self::$curl=curl_init();
		curl_setopt(self::$curl, CURLOPT_RETURNTRANSFER, true);
	}
	public static function my_curl_setopt(int $option , mixed $value)
	{
		curl_setopt(self::$curl,$option,$value);
	}
	abstract public static function is_cached(string $url, array $irrelevant_parameters=array());
	abstract public static function remove_from_cache(string $url, array $irrelevant_parameters=array());
	abstract public static function query($url, $irrelevant_parameters=array(),$force_curl=false);
	abstract public static function get_clean_url(string $url, array $irrelevant_parameters);
}