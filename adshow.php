<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

if (!defined('ADINJ_NO_CONFIG_FILE')){
$adinj_dir = dirname(__FILE__);
if (file_exists($adinj_dir.'/ad-injection-config.php')){
	include_once($adinj_dir.'/ad-injection-config.php');
} else if (file_exists($adinj_dir.'/../../ad-injection-config.php')) {
	include_once($adinj_dir.'/../../ad-injection-config.php');
} else {
	echo '<!--ADINJ DEBUG: ad-injection-config.php could not be found. Re-save your settings to re-generate it.-->';
}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_functions_exist')){
// Used to downgrade fatal errors to printed errors to make debugging easier
// and so that a problem doesn't disable the whole website. 
function adshow_functions_exist(){
	if (!defined('ADINJ_NO_CONFIG_FILE')){
		if (!adshow_functions_exist_impl('adinj_config_sevisitors_only')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_search_engine_referrers')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_block_ips')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_blocked_ips')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_block_keywords')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_blocked_keywords')){ return false; }
		//if (!adshow_functions_exist_impl('adinj_config_block_hours')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_debug_mode')){ return false; }
	}
	return true;
}
function adshow_functions_exist_impl($function){
	if (!function_exists($function)){
		echo "<!--ADINJ DEBUG:".__FILE__." Error: $function does not exist. Might be because config file is missing or out of date. Re-save settings to fix. -->";
		return false;
	}
	return true;
}
}

if (defined('ADINJ_NO_CONFIG_FILE')){
function adinj_config_sevisitors_only() { 
	return adinj_ticked('sevisitors_only');
}

function adinj_config_search_engine_referrers() {
	$list = adinj_quote_list('ad_referrers');
	return preg_split("/[,'\s]+/", $list, -1, PREG_SPLIT_NO_EMPTY);
}

function adinj_config_block_ips() {
	return adinj_ticked('block_ips');
}

function adinj_config_blocked_ips() { 
	$list = adinj_quote_list('blocked_ips');
	return preg_split("/[,'\s]+/", $list, -1, PREG_SPLIT_NO_EMPTY);
}

function adinj_config_block_keywords() {
	return adinj_ticked('block_keywords');
}

function adinj_config_blocked_keywords() { 
	$list = adinj_quote_list('blocked_keywords');
	return preg_split("/[,'\s]+/", $list, -1, PREG_SPLIT_NO_EMPTY);
}

function adinj_config_block_hours() {
	$ops = adinj_options();
	return $ops['block_ads_for_hours'];
}

function adinj_config_debug_mode() { 
	return adinj_ticked('debug_mode');
}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_display_ad_file_v2')){
function adshow_display_ad_file_v2($adfiles, $adfiles_frequency = array(), $options = array(), $altfiles = array(), $altfiles_frequency = array()){
	if (!adshow_functions_exist()){ return false; }
	if (adinj_config_debug_mode()){	echo "<!--ADINJ DEBUG: adshow_display_ad_file() quantity=".sizeof($adfiles)."-->\n"; }
	
	if (empty($adfiles) && empty($altfiles)){
		echo "<!--ADINJ DEBUG: Error: adfiles and altfiles are empty-->\n";
		return false;
	}
	
	$adfile = "";
	
	$showads = adshow_show_adverts();
	if ($showads !== true){
		if (adinj_config_debug_mode()){	echo "<!--ADINJ DEBUG: ad blocked at run time reason=$showads-->\n"; }
		$alt_content_file = adshow_pick_value($altfiles, $altfiles_frequency);
		if (!empty($alt_content_file)){
			if (adinj_config_debug_mode()){	echo "<!--ADINJ DEBUG: alt content file defined:$alt_content_file-->\n"; }
			$adfile = $alt_content_file;
		} else {
			if (adinj_config_debug_mode()){	echo "<!--ADINJ DEBUG: no alt content file defined-->\n"; }
			return false;
		}
	}
	
	if (empty($adfile)){
		$adfile = adshow_pick_value($adfiles, $adfiles_frequency);
	}
	
	if (adinj_config_debug_mode()){ echo "<!--ADINJ DEBUG: adshow_display_ad_file($adfile)-->\n";	}
	
	$plugin_dir = dirname(__FILE__);
	
	$ad_path = dirname($plugin_dir).'/ad-injection-data/'.$adfile;
	if (file_exists($ad_path)){
		adshow_display_ad_full_path_v2($ad_path, $options);
		return;
	}
	echo "
<!--ADINJ DEBUG: could not read ad: $ad_path
If you have just upgraded you may need to re-save your ads to regenerate the ad files.
-->";
}
}

if (!function_exists('adshow_pick_value')){
function adshow_pick_value($values, $frequency){
	if (empty($values)){
		return "";
	}
	if (!is_array($values)){
		// single value passed in
		return $values;
	}
	$val = "";

	// values is an array
	if (empty($frequency)){
		// each value has an equal chance of being picked
		$val = array_rand(array_flip($values));
	} else {
		// each value has its own probability of being picked
		$count = sizeof($values);
		if ($count != sizeof($frequency)){
			echo "<!--ADINJ DEBUG: size of arrays don't match ".$count."!=".sizeof($frequency)."-->\n";
			return "";
		}
		$total = array_sum($frequency);
		$rand = rand(0, $total);
		$cumulative = 0;
		for ($i=0; $i<$count; ++$i){
			$cumulative += $frequency[$i];
			if ($rand <= $cumulative){
				$val = $values[$i];
				if (adinj_config_debug_mode()){ echo "<!--ADINJ DEBUG: picked value at position $i: $val-->\n";	}
				break;
			}
		}
	}
	return $val;
}
}

if (!function_exists('adshow_display_ad_full_path_v2')){
function adshow_display_ad_full_path_v2($ad_path, $ops = array()){
	if (!adshow_functions_exist()){ return false; }

	if (!file_exists($ad_path)){
		echo "<!--ADINJ DEBUG: ad file does not exist: $ad_path.\nIf you have just upgraded you may need to re-save your ads to regenerate the ad files.\n-->\n";
		return false;
	}

	$ad = file_get_contents($ad_path);
	if ($ad === false) echo "<!--ADINJ DEBUG: could not read ad from file: $ad_path-->\n";
	$ad = adshow_eval_php($ad);
	echo adshow_add_formatting($ad, $ops);
}
}

if (!function_exists('adshow_add_formatting')){
function adshow_add_formatting($ad, $ops = array()){
	if (strlen($ops['align']) > 0 ||
		strlen($ops['clear']) > 0 ||
		strlen($ops['margin_top']) > 0 ||
		strlen($ops['margin_bottom']) > 0 ||
		strlen($ops['padding_top']) > 0 ||
		strlen($ops['padding_bottom']) > 0) {
		$clear = "";
		$mtop = "";
		$mbottom = "";
		$ptop = "";
		$pbottom = "";
		if (strlen($ops['clear']) > 0) $clear="clear:" . $ops['clear'] . ";";
		if (strlen($ops['margin_top']) > 0) $mtop="margin-top:" . $ops['margin_top'] . "px;";
		if (strlen($ops['margin_bottom']) > 0) $mbottom="margin-bottom:" . $ops['margin_bottom'] . "px;";
		if (strlen($ops['padding_top']) > 0) $ptop="padding-top:" . $ops['padding_top'] . "px;";
		if (strlen($ops['padding_bottom']) > 0) $pbottom="padding-bottom:" . $ops['padding_bottom'] . "px;";
		$cssrules = $clear . $mtop . $mbottom . $ptop . $pbottom;
		
		if ($ops['align'] == 'rand lcr') $ops['align'] = array_rand(array_flip(array('left', 'center', 'right')));
		if ($ops['align'] == 'rand float lr') $ops['align'] = array_rand(array_flip(array('float left', 'float right')));
		if ($ops['align'] == 'rand all') $ops['align'] = array_rand(array_flip(array('left', 'center', 'right', 'float left', 'float right')));
		
		if ($ops['align'] == 'left'){
			$ad = "\n<div style='float:left;" . $cssrules . "'>$ad</div><br clear='all' />";
		} else if ($ops['align'] == 'center'){
			$ad = "\n<div style='" . $cssrules . "'><center>$ad</center></div>";
		} else if ($ops['align'] == 'right'){
			$ad = "\n<div style='float:right;" . $cssrules . "'>$ad</div><br clear='all' />";
		} else if ($ops['align'] == 'float left'){
			$ad = "\n<div style='float:left;" . $cssrules . "margin-right:5px;'>$ad</div>";
		} else if ($ops['align'] == 'float right'){
			$ad = "\n<div style='float:right;" . $cssrules . "margin-left:5px;'>$ad</div>";
		} else {
			$ad = "\n<div style='" . $cssrules . "'>$ad</div>";
		}
	}
	return $ad;
}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_fromasearchengine')){
function adshow_fromasearchengine(){
	if (!adshow_functions_exist()){ return false; }

	// return true if the visitor has recently come from a search engine
	// and has the adinj cookie set.
	if ($_COOKIE["adinj"]==1) return true;
	
	$referrer = $_SERVER['HTTP_REFERER'];
	$searchengines = adinj_config_search_engine_referrers();
	foreach ($searchengines as $se) {
		if (stripos($referrer, $se) !== false) {
			return true;
		}
	}
	return false;
}
}

if (!function_exists('adshow_blocked_referrer')){
function adshow_blocked_referrer(){
	if (!adshow_functions_exist()){ return false; }

	// true if blocked cookie is set
	if ($_COOKIE["adinjblocked"]==1) return true;
	
	$referrer = $_SERVER['HTTP_REFERER'];
	$blocked = adinj_config_blocked_keywords();
	foreach ($blocked as $bl) {
		if (stripos($referrer, $bl) !== false) {
			return true;
		}
	}
	return false;
}
}

if (!function_exists('adshow_blocked_ip')){
function adshow_blocked_ip(){
	if (!adshow_functions_exist()){ return false; }

	$visitorIP = $_SERVER['REMOTE_ADDR'];
	return in_array($visitorIP, adinj_config_blocked_ips());
}
}

if (!function_exists('adshow_show_adverts')){
function adshow_show_adverts(){
	if (!adshow_functions_exist()){ return false; }

	//echo 'ref:'.$_SERVER['HTTP_REFERER'];
	if (adinj_config_block_ips() && adshow_blocked_ip()) return "blockedip";
	if (adinj_config_sevisitors_only()&& !adshow_fromasearchengine()) return "referrer";
	if (adinj_config_block_keywords() && adshow_blocked_referrer()) return "blockedreferrer";
	
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