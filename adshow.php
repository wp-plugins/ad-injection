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
		if (!adshow_functions_exist_impl('adinj_config_add_tags_rnd')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_add_tags_top')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_add_tags_bottom')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_sevisitors_only')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_search_engine_referrers')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_blocked_ips')){ return false; }
		if (!adshow_functions_exist_impl('adinj_config_debug_mode')){ return false; }
	}
	return true;
}
function adshow_functions_exist_impl($function){
	if (!function_exists($function)){
		echo "<!--ADINJ DEBUG:".__FILE__." Error: $function does not exist. Might be because config file is missing. Re-save settings to fix. -->";
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

function adinj_config_blocked_ips() { 
	$list = adinj_quote_list('blocked_ips');
	return preg_split("/[,'\s]+/", $list, -1, PREG_SPLIT_NO_EMPTY);
}

function adinj_config_debug_mode() { 
	return adinj_ticked('debug_mode');
}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_display_ad_file')){
function adshow_display_ad_file($adfile){
	if (!adshow_functions_exist()){ return false; }

	if (adinj_config_debug_mode()){
		echo "<!--ADINJ DEBUG: adshow_display_ad_file($adfile)-->";
	}
	$plugin_dir = dirname(__FILE__);
	$ad_path1 = $plugin_dir.'/ads/'.$adfile;
	if (file_exists($ad_path1)){
		adshow_display_ad_full_path($ad_path1);
		return;
	}
	
	$ad_path2 = dirname($plugin_dir).'/ad-injection-data/'.$adfile;
	if (file_exists($ad_path2)){
		adshow_display_ad_full_path($ad_path2);
		return;
	}
	echo "
<!--ADINJ DEBUG: could not read ad from either:
	$ad_path1
	$ad_path2
If you have just upgraded you may need to re-save your ads to regenerate the ad files.
-->";
}
}

if (!function_exists('adshow_display_ad_full_path')){
function adshow_display_ad_full_path($ad_path){
	if (!adshow_functions_exist()){ return false; }

	$showads = adshow_show_adverts();
	if ($showads !== true){
		if (adinj_config_debug_mode()){
			echo "<!--ADINJ DEBUG: ad blocked at run time reason=$showads-->";
		}
		return;
	}
	if (file_exists($ad_path)){
		$ad = file_get_contents($ad_path);
		if ($ad === false) echo "\n<!--ADINJ DEBUG: could not read ad from file: $ad_path-->";
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
		echo "\n<!--ADINJ DEBUG: ad file does not exist: $ad_path.\nIf you have just upgraded you may need to re-save your ads to regenerate the ad files.\n-->";
	}
}
}

//////////////////////////////////////////////////////////////////////////////

if (!function_exists('adshow_display_ad_file_v2')){
function adshow_display_ad_file_v2($adfiles, $adfiles_frequency = array(), $options = array(), $altfiles = array(), $altfiles_frequency = array()){
	if (!adshow_functions_exist()){ return false; }
	if (adinj_config_debug_mode()){	echo "<!--ADINJ DEBUG: adshow_display_ad_file() quantity=".sizeof($adfiles)."-->\n"; }
	
	if (empty($adfiles)){
		echo "<!--ADINJ DEBUG: Error: adfiles is empty-->\n";
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
			return false;
		}
	}
	
	if (empty($adfile)){
		$adfile = adshow_pick_value($adfiles, $adfiles_frequency);
	}
	
	if (adinj_config_debug_mode()){ echo "<!--ADINJ DEBUG: adshow_display_ad_file($adfile)-->";	}
	
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
	if (!is_array($values)){
		// single value passed in
		return $values;
	}
	$val = NULL;

	// values is an array
	if (empty($frequency)){
		// each value has an equal chance of being picked
		$val = array_rand(array_flip($values));
	} else {
		// each value has its own probability of being picked
		$count = sizeof($values);
		if ($count != sizeof($frequency)){
			echo "<!--ADINJ DEBUG: size of arrays don't match ".$count."!=".sizeof($frequency)."-->";
			return NULL;
		}
		$total = array_sum($frequency);
		$rand = rand(0, $total);
		$cumulative = 0;
		for ($i=0; $i<$count; ++$i){
			$cumulative += $frequency[$i];
			if ($rand <= $cumulative){
				$val = $values[$i];
				if (adinj_config_debug_mode()){ echo "<!--ADINJ DEBUG: picked value at position $i:$val-->";	}
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
		echo "\n<!--ADINJ DEBUG: ad file does not exist: $ad_path.\nIf you have just upgraded you may need to re-save your ads to regenerate the ad files.\n-->";
		return false;
	}

	$ad = file_get_contents($ad_path);
	if ($ad === false) echo "\n<!--ADINJ DEBUG: could not read ad from file: $ad_path-->";
	$ad = adshow_eval_php($ad);
	echo adshow_add_formatting($ad, $ops);
}
}

if (!function_exists('adshow_add_formatting')){
function adshow_add_formatting($ad, $ops = array()){
	if (strlen($ops['align']) > 0 ||
		strlen($ops['clear']) > 0 ||
		strlen($ops['margin_top']) > 0 ||
		strlen($ops['margin_bottom']) > 0) {
		$clear = "";
		$top = "";
		$bottom = "";
		if (strlen($ops['clear']) > 0) $clear="clear:" . $ops['clear'] . ";";
		if (strlen($ops['margin_top']) > 0) $top="margin-top:" . $ops['margin_top'] . "px;";
		if (strlen($ops['margin_bottom']) > 0) $bottom="margin-bottom:" . $ops['margin_bottom'] . "px;";
		$cssrules = $clear . $top . $bottom;
		
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
	if (!adshow_functions_exist()){ return false; }

	$visitorIP = $_SERVER['REMOTE_ADDR'];
	return in_array($visitorIP, adinj_config_blocked_ips());
}
}

if (!function_exists('adshow_show_adverts')){
function adshow_show_adverts(){
	if (!adshow_functions_exist()){ return false; }

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