<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/blog/
*/

include_once('ad-injection-config.php');

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_display_ad_file')){
function adshow_display_ad_file($adfile){
	$ad_path = dirname(__FILE__).'/ads/'.$adfile;
	adshow_display_ad_full_path($ad_path);
}
}

if (!function_exists('adshow_display_ad_full_path')){
function adshow_display_ad_full_path($ad_path){
	$showads = adshow_show_adverts();
	if ($showads !== true){
		if (adinj_config_debug_mode()){
			echo "<!--ADINJ DEBUG: ad blocked at run time reason=$showads-->";
		}
		return;
	}
	if (file_exists($ad_path)){
		$ad = file_get_contents($ad_path);
		if ($ad === false) die("could not read from file: $ad_path");
		if (stripos($ad_path, 'random_1.txt') > 0){ // TODO something better than this
			echo adinj_config_add_tags_rnd(adshow_eval_php($ad));
		} else if (stripos($ad_path, 'top_1.txt') > 0){
			echo adinj_config_add_tags_top(adshow_eval_php($ad));
		} else if (stripos($ad_path, 'bottom_1.txt') > 0){
			echo adinj_config_add_tags_bottom(adshow_eval_php($ad));
		} else {
			echo adshow_eval_php($ad);
		}
	} else {
		echo "ADINJ WARNING: file does not exist: $ad_path";
	}
}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_fromasearchengine')){
function adshow_fromasearchengine(){
	$referrer = $_SERVER['HTTP_REFERER'];
	$searchengines = adinj_config_search_engine_referrers();
	foreach ($searchengines as $se) {
		if (stripos($referrer, $se) !== false) {
			return true;
		}
	}
	// Also return true if the visitor has recently come from a search engine
	// and has the adinj cookie set.
	return ($_COOKIE["adinj"]==1);
}
}

if (!function_exists('adshow_blocked_ip')){
function adshow_blocked_ip(){
	$visitorIP = $_SERVER['REMOTE_ADDR'];
	return in_array($visitorIP, adinj_config_blocked_ips());
}
}

if (!function_exists('adshow_show_adverts')){
function adshow_show_adverts(){
	if (adshow_blocked_ip()) return "blockedip";
	if (adinj_config_sevisitors_only()){
		if (!adshow_fromasearchengine()) return "referrer";
	}
	return true;
}
}

// From: Exec-PHP plugin
if (!function_exists('adshow_eval_php')){
function adshow_eval_php($code)	{
	ob_start();
	eval("?>$code<?php ");
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
}

//////////////////////////////////////////////////////////////////////////////

?>