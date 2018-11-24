<?php
include_once("AbstractRequestCache.php");
class RequestCache extends AbstractRequestCache
{	
	public static function is_cached(string $url, array $irrelevant_parameters=array())
	{
		$new_url=count($irrelevant_parameters)?self::get_clean_url($url,$irrelevant_parameters):$url;
		$stmt=self::$db->prepare("SELECT id FROM RequestCache WHERE url=:url");
		$stmt->execute(array(":url"=>$new_url));
		
		return $stmt->rowCount();
	}
	public static function remove_from_cache(string $url, array $irrelevant_parameters=array())
	{
		$new_url=count($irrelevant_parameters)?self::get_clean_url($url,$irrelevant_parameters):$url;

		$stmt=self::$db->prepare("DELETE FROM RequestCache WHERE url=:url");
		$stmt->execute(array(":url"=>$new_url));
	}
	public static function query($url, $irrelevant_parameters=array(),$force_curl=false)
	{
		$new_url=count($irrelevant_parameters)?self::get_clean_url($url,$irrelevant_parameters):$url;
		$stmt=self::$db->prepare("SELECT content FROM RequestCache WHERE url=:url");
		$stmt->execute(array(":url"=>$new_url));
		
		if(!$stmt->rowCount() || $force_curl)
		{
			self::$curl_request++;
			curl_setopt(self::$curl, CURLOPT_URL, $url);
			if(($content=curl_exec(self::$curl)) === false)
			{
				echo(curl_error(self::$curl));
				$content=NULL;
			}		
			else
			{
				$stmt=self::$db->prepare("INSERT INTO RequestCache (url,content) VALUES (:url,:content) ON DUPLICATE KEY UPDATE url=:url,content=:content");
				$stmt->execute(array(":url"=>$new_url,":content"=>$content));
			}
		}
		else
		{
			self::$db_request++;
			$results=$stmt->fetchAll();
			$content=$results[0]["content"];
		}
		return $content;
	}
	public static function get_clean_url(string $url, array $irrelevant_parameters)
	{
		$parameters=parse_url($url, PHP_URL_QUERY);
		$parameters_array=explode('&',$parameters);
		$new_parameters=array();
		
		foreach($parameters_array as $parameter)
		{
			if(!in_array(explode("=",$parameter)[0],$irrelevant_parameters))
			{
				$new_parameters[]=$parameter;
			}
		}
		return substr($url,0,strpos($url,$parameters)).implode('&',$new_parameters);
	}
}