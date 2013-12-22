<?php
header('Content-type: text/css');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=86400');
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
header('Pragma: Public');

// Templates
$TEMPLATE = "@font-face{font-family:'%s';font-style:%s;font-weight:%s;src:local('%s'),url(%s) format('svg'),url(%s) format('opentype'),url(%s) format('woff')}";
$TEMPLATE_FORCE = "@font-face{font-family:'%s';font-style:%s;font-weight:%s;src:url(%s) format('svg'),url(%s) format('opentype'),url(%s) format('woff')}";

$query = explode("/", preg_replace("/\/$|^\//", "", urldecode($_SERVER['REQUEST_URI'])));
$cat = json_decode(file_get_contents('./cat.json'), true);

if ($query[0] == "") {
	http_response_code(400);
	exit();
}

foreach ($query as $key=>$val) {
	$val = explode(":", $val);
	$family = preg_replace("/\+/", " ", $val[0]);
	$weights = explode(",", $val[1]);
	$flags = isset($val[2]) ? $val[2] : '';

	foreach ($weights as $weight) {
		$base_url = (empty($_SERVER['HTTPS']) ? 'http:' : 'https:') . '//get.brick.im/' . strtolower(preg_replace("/\s/", '', $family)) . "/";
		$local = $cat[$family][$weight];
		
		// Font URLs
		$otf = $base_url . $weight . ".otf";
		$woff = $base_url . $weight . ".woff";
		$svg = $base_url . $weight . ".svg.gz";
		
		if (preg_match("/i$/", $weight)) {
			$style = 'italic';
			$weight = rtrim($weight, "i");
		} else {
			$style = 'normal';
		}

		// Process flags
		if (strpos($flags,'f') !== false)
			echo sprintf($TEMPLATE_FORCE, $style, $weight, $local, $svg, $otf, $woff);
		else
			echo sprintf($TEMPLATE, $family, $style, $weight, $local, $svg, $otf, $woff);
	}
}
?>
