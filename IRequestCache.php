<?php
interface IRequestCache
{
	public static function init();
	public static function close();
	public static function get_curl_request();
	public static function get_db_request();
	public static function my_curl_reset();
	public static function my_curl_setopt(int $option , mixed $value);
	public static function is_cached(string $url, array $irrelevant_parameters=array());
	public static function remove_from_cache(string $url, array $irrelevant_parameters=array());
	public static function query($url, $irrelevant_parameters=array(),$force_curl=false);
	public static function get_clean_url(string $url, array $irrelevant_parameters);
}