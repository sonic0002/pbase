<?php
/*
 * CURL class is to handle the HTTP requests and responses
 */
class CURL {
	const USERAGENT = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36';

	// data type to send
	const DT_JSON  = 1;
	const DT_FORM  = 2;
	const DT_QUERY = 3;

	// Headers
	const HEADER_USER_AGENT   = 'User-Agent';
	const HEADER_CONTENT_TYPE = 'Content-Type';

	public static function get($url, $header = [], $timeout = 120){
		$ch = curl_init();

		$userAgent = self::USERAGENT;
		if(isset($header[self::HEADER_USER_AGENT]) && !empty($header[self::HEADER_USER_AGENT])){
			$userAgent = $header[self::HEADER_USER_AGENT];
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // disable ssl certificate verifcation
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //set our user agent
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent); //set our user agent
		curl_setopt($ch, CURLOPT_POST, false); //set how many paramaters to post
		curl_setopt($ch, CURLOPT_URL,$url); //set the url we want to use
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //set the url we want to use
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // This is to ensure get the response back
		
		if(count($header) > 0) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, false);
		}
		
		$result = curl_exec($ch); //execute and get the results

		curl_close($ch);

		return $result;
	}

	public static function post($url, $data = [], $header = [], $dataType = self::DT_JSON) {
		$ch = curl_init();

		// Build fields string
		if($dataType == self::DT_FORM) {
			$fieldsString = implode("&", $data);
		} else if($dataType == self::DT_QUERY){
			$fieldsString = http_build_query($data);
		} else {
			$fieldsString = json_encode($data);
		}

		if(count($header) == 0){
			$header = array(      
				"Accept: application/json",                                                                 
				'Content-Type: application/json',                                                                                
				'Content-Length: '.strlen($fieldsString)
			);
		}

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // disable ssl certificate verifcation
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, self::USERAGENT); //set our user agent
		curl_setopt($ch, CURLOPT_POST, TRUE); //set how many paramaters to post
		curl_setopt($ch, CURLOPT_URL, $url); //set the url we want to use
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString); //set the user_name field to 'joeyjohns'
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // This is to ensure get the response back

		$result = curl_exec($ch); //execute and get the results

		curl_close($ch);

		return $result;
	}
}