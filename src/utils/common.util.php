<?php

class CommonUtil {
	
	public static function GetClientIpAddress() {
		$ipaddress = NULL;
		if (getenv("HTTP_X_CLUSTER_CLIENT_IP")) {
			$ipaddress = getenv("HTTP_X_CLUSTER_CLIENT_IP");
		} else if (getenv("HTTP_X_FORWARDED_FOR")) {
			$ipaddress = getenv("HTTP_X_FORWARDED_FOR");
		} else {
			$ipaddress = getenv("REMOTE_ADDR");
		}
		return $ipaddress;
	}

	public static function addUrlMap($url_record,$retries=3) {
		if(!$retries) {
			//log failure cause of retries
			return NULL;
		}
		$uuid = UUID_GEN::random_uuid(UUID_GEN::DEFAULT_NUM_DIGITS);
		//unset($url_record);
		$dao = new Dao(Entities::URL_TABLE);
		$url_record['uuid'] = $uuid;
		$url_record['timestamp'] = date("Y-m-d\TH:i:s\Z");
		$conditionExpression['conditionExpression'] = 'attribute_not_exists(uuid)';
		$result = $dao->put($url_record,$conditionExpression);
		if(!$result) {
			$retries--;
			self::addUrlMap($url_record,$retries);
		}

		return array($url_record,$result);
	}
}
?>