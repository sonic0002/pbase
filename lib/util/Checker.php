<?php
@session_start();
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $DOCUMENT_ROOT.'/vendor/autoload.php';

// Checker class is to check the validity of emails and strings
class Checker{
	public static function startsWith($haystack,$needle,$case=true) {
		if($case){return (strcmp(substr($haystack, 0, strlen($needle)),$needle)===0);}
		return (strcasecmp(substr($haystack, 0, strlen($needle)),$needle)===0);
	}

	public static function endsWith($haystack,$needle,$case=true) {
		if($case){return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);}
		return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)),$needle)===0);
	}

	//Check whether a string is empty
	public static function isEmpty($str){
		return empty($str) || ($str == "");
	}

	//Check whether a string is an email address
	public static function isEmail($email){
		$email_reg="/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/";
		return preg_match($email_reg,$email);
	}

	public static function isImage($fileInfo, $checkExt = false) {
		$mime = array('image/gif' ,
						'image/jpeg' ,
						'image/png',
						'image/psd',
						'image/bmp',
						'image/tiff',
						'image/jp2',
						'image/iff',
						'image/vnd.wap.wbmp',
						'image/xbm' ,
						'image/vnd.microsoft.icon'
				);

		// First check if file exists and is readable
		if (!is_readable($fileInfo)) {
			return false;
		}

		// Try to get image info - returns false if not an image
		$imageInfo = @getimagesize($fileInfo);
		if ($imageInfo === false) {
			return false;
		}

		// Check MIME type
		$isImage = false;
		foreach($mime as $item) {
			if(strtolower($imageInfo["mime"]) == $item) {
				$isImage = true;
				break;
			}
		}

		if(!$isImage){
			return false;
		}

		if($checkExt){
			// Check file extension (keep your existing extension checks)
			$fileInfo = strtolower($fileInfo);
			if(self::endsWith($fileInfo, ".svg") 
				|| self::endsWith($fileInfo, ".svgz")
				|| self::endsWith($fileInfo, ".jpg")
				|| self::endsWith($fileInfo, ".jpeg") 
				|| self::endsWith($fileInfo, ".png")
				|| self::endsWith($fileInfo, ".gif")
				|| self::endsWith($fileInfo, ".bmp")
				|| self::endsWith($fileInfo, ".tiff")
				|| self::endsWith($fileInfo, ".webp")){
				$isImage = true;
			} else {
				$isImage = false;
			}
		}

		return $isImage;
	}

	public static function isBot() {
		$userAgent = "";
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		}
		
		// Simple timing check
		$currentTime = time();
		$minInterval = 1; // Minimum seconds between requests
		
		if (isset($_SESSION['last_request_time'])) {
			if (($currentTime - $_SESSION['last_request_time']) < $minInterval) {
				return true; // Too frequent requests
			}
		}
		$_SESSION['last_request_time'] = $currentTime;
		
		// Expanded list of bot keywords
		$botKeywords = [
			'bot', 'crawl', 'slurp', 'spider', 'mediapartners', 'google', 'yahoo', 'baidu', 
			'bing', 'msn', 'teoma', 'yandex', 'sogou', 'exabot', 'facebot', 
			'ia_archiver', 'semrush', 'ahrefs', 'mj12bot', 'dotbot', 'baiduspider', 
			'bingbot', 'twitterbot', 'applebot', 'linkedinbot', 'msnbot', 'slackbot',
			'telegrambot', 'amazonbot', 'googlebot', 'yandexbot', 'duckduckbot', 'archive.org',
			'rogerbot', 'screaming', 'pingdom', 'phantomjs', 'headless', 'selenium', 'chrome-lighthouse'
		];

		// Check for empty or suspicious user agent
		if (empty($userAgent) || strlen($userAgent) < 5 || strlen($userAgent) > 500) {
			return true;
		}

		// Check for common bot user agent keywords
		foreach ($botKeywords as $keyword) {
			if (strpos($userAgent, $keyword) !== false) {
				return true;
			}
		}

		// Check for missing or suspicious headers
		if (!isset($_SERVER['HTTP_ACCEPT']) || 
			!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || 
			!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			return true;
		}

		// Check for common bot behaviors
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
			return true;
		}

		// Check for suspicious connection properties
		if (isset($_SERVER['HTTP_FROM']) || 
			isset($_SERVER['HTTP_REQUEST_TYPE']) || 
			isset($_SERVER['HTTP_CLIENT_IP'])) {
			return true;
		}

		// Check for known datacenter IPs (optional, as it might block legitimate users)
		$knownDatacenters = [
			'34.', '35.', // Google Cloud
			'52.', '54.', // AWS
			'104.196.', // Google Cloud
			'13.32.', // AWS CloudFront
		];
		
		// $ipAddress = "";
		if(isset($_SERVER['REMOTE_ADDR'])){
			$ipAddress = $_SERVER['REMOTE_ADDR'];
		}
		foreach ($knownDatacenters as $datacenter) {
			if (strpos($ipAddress, $datacenter) === 0) {
				return true;
			}
		}

		return false;
	}

	// Check whether a needle is in hetstack
	public static function contain($heystack, $needle){
		if(strpos(strtolower($heystack), strtolower($needle)) !== FALSE && strpos(strtolower($heystack), strtolower($needle)) >= 0){
			return true;
		}
		return false;
	}

	// Check whether containing array of needles
	public static function containInArray($heystack, $needleArray){
		$status = false;
		foreach($needleArray as $needle){
			$status |= self::contain($heystack, $needle);
			if($status){
				break;
			}
		}
		return $status;
	}

	public static function isHttps(){
		return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);
	}

	public static function isLocal(){
		return self::contain($_SERVER['HTTP_HOST'], 'localhost');
	}

	public static function getRealIP() {
		if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			// Cloudflare real IP header
			return $_SERVER["HTTP_CF_CONNECTING_IP"];
		} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			// X-Forwarded-For may contain multiple IPs (first one is real)
			$ipList = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
			return trim($ipList[0]);
		} else {
			// Default to REMOTE_ADDR (may be Cloudflare proxy)
			return $_SERVER["REMOTE_ADDR"];
		}
	}		

	public static function detectBrowser($userAgent) {
		$userAgent = strtolower($userAgent);
		
		// Check for Edge first (since Edge also contains "chrome" and "safari")
		if (strpos($userAgent, 'edg') !== false) {
			return 'edge';
		}
		
		// Check for Chrome (must be before Safari check)
		if (strpos($userAgent, 'chrome') !== false && strpos($userAgent, 'safari') !== false) {
			return 'chrome';
		}
		
		// Check for Safari (should not contain Chrome)
		if (strpos($userAgent, 'safari') !== false && strpos($userAgent, 'chrome') === false) {
			return 'safari';
		}
		
		// Check for Firefox
		if (strpos($userAgent, 'firefox') !== false) {
			return 'firefox';
		}
		
		// Default fallback
		return 'chrome';
	}
}
