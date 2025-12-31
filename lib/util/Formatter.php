<?php
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $DOCUMENT_ROOT.'/vendor/autoload.php';

/*
 * Formatter class is to format the strings and HTML
 */
class Formatter {
	const ALLOW_HTML = true;

	public static function formatString($string, $allowHtml = false){
		// Remove any potential NULL bytes
		$string = str_replace("\0", '', $string);

		if ($allowHtml) {
			// Configure HTMLPurifier with safe HTML settings
			$config = HTMLPurifier_Config::createDefault();
			// Allow common WYSIWYG HTML tags and attributes
			$config->set('HTML.Allowed', 'p[style],b[style],i[style],u[style],strong[style],em[style],a[href|title|style],ul[style],ol[style],li[style],br,span[style],img[src|alt|width|height|style],video[src|controls|width|height|style],'
				. 'pre[class|style],code[class|style],blockquote[style],h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],div[class|style],table[width|border|style],tr[style],td[width|style],th[width|style],tbody[style],thead[style],sup[style],sub[style],hr[style],'
				. 'strike[style],del[style],ins[style],mark[style],small[style],iframe[src|width|height|frameborder|allowfullscreen|allow|style|class|title|loading]');

			$config->set('URI.AllowedSchemes', ['https' => true, 'http' => true]);
			
			// Add configuration for quotes handling
			$config->set('Core.EscapeNonASCIICharacters', true);
			$config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_parent', '_top']);
			$config->set('Attr.EnableID', true);
			$config->set('Attr.DefaultInvalidImageAlt', '');
			$config->set('HTML.Trusted', true);  // Allow more flexibility with quotes in attributes
			$config->set('CSS.AllowTricky', true); // Allow more CSS properties like display
			$config->set('CSS.AllowedProperties', null); // Allow all CSS properties
			// Allow iframes
			$config->set('HTML.SafeIframe', true);
			$config->set('URI.SafeIframeRegexp', '%^(https:|http:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/|w\.soundcloud\.com/player/|maps\.google\.com/maps\?|docs\.google\.com/|drive\.google\.com/|onedrive\.live\.com/|(?:[a-zA-Z0-9-]+\.)+([a-zA-Z0-9-]+))%');
			
			$purifier = new HTMLPurifier($config);
			return $purifier->purify($string);
		} else {
			// For plain text, simply encode all HTML entities
			return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}
	}

	public static function formatStringWithoutTag($data) {
		return strip_tags($data);
	}

	public static function formatStringAsPlainText($data) {
		if($data == null || $data == ''){
			return '';
		}
		return htmlspecialchars($data,ENT_QUOTES,'UTF-8');
	}

	public static function mysqlFormatString($string, $allowHtml = false){
		// First format the string with HTML handling if needed
		$formatted = self::formatString($string, $allowHtml);
		
		// Escape special characters for MySQL
		if (function_exists('mysqli_real_escape_string') && isset($GLOBALS['mysqli'])) {
			// If using MySQLi
			return mysqli_real_escape_string($GLOBALS['mysqli'], $formatted);
		} else if (function_exists('mysql_real_escape_string')) {
			// If using legacy MySQL (not recommended)
			return mysql_real_escape_string($formatted);
		}
		
		// Fallback: manual escaping (not recommended, but better than nothing)
		return addslashes($formatted);
	}
}