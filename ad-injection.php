<?php
/*
Plugin Name: Ad Injection
Plugin URI: http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/
Description: Inserts any advert into your blog. Options to exclude by post age, visitor IP, and visitor referrer. Works with WP Super Cache.
Version: 0.8.7
Author: reviewmylife
Author URI: http://www.reviewmylife.co.uk/
License: GPLv2
*/

/*  Copyright 2010 reviewmylife (contact : http://www.reviewmylife.co.uk/)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


//error_reporting(E_ALL ^ E_STRICT);

// Files
define('ADINJ_PATH', WP_PLUGIN_DIR.'/ad-injection');
define('ADINJ_CONFIG_FILE', ADINJ_PATH . '/ad-injection-config.php');
define('ADINJ_AD_PATH', ADINJ_PATH . '/ads');
define('ADINJ_AD_RANDOM_FILE', 'ad_random_1.txt');
define('ADINJ_AD_TOP_FILE', 'ad_top_1.txt');
define('ADINJ_AD_BOTTOM_FILE', 'ad_bottom_1.txt');

// Constants
define('ADINJ_RULE_DISABLED', 'Rule Disabled');
define('ADINJ_ALWAYS_SHOW', 'Always show');

// Global variables
$adinj_total_rand_ads_used = 0;
$adinj_total_all_ads_used = 0;
$adinj_data = array();

if (is_admin()){
	require_once(ADINJ_PATH . '/ad-injection-admin.php');
}

function adinj_admin_menu_hook(){
	add_options_page('Ad Injection', 'Ad Injection', 'manage_options', basename(__FILE__), 'adinj_options_page');
}

function adinj_options_link_hook($links, $file) {
	static $this_plugin;
	if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
	if ($file == $this_plugin){
		$link = "<a href='options-general.php?page=ad-injection.php'>" . __("Settings") . "</a>";
		array_unshift($links, $link);
	}
	return $links;
}

function adinj_options($reset=false){
	global $adinj_data;
	if (empty($adinj_data) || $reset !== false){
		$adinj_data = get_option('adinj_options');
	}
	return $adinj_data;
}

function adinj_option($option){
	$options = adinj_options();
	return $options[$option];
}

// TODO make the cookie domain from wp-config.php accessible to script
//$cookie_domain = COOKIE_DOMAIN; // TODO test
//var adinj_cookie_domain = "$cookie_domain"; //JS line. TODO test
function adinj_print_referrers_hook(){
	if (adinj_ads_completely_disabled_from_page()) return;
	if (adinj_ticked('sevisitors_only')){
		$referrer_list = adinj_quote_list('ad_referrers');
	echo <<<SCRIPT
<script type="text/javascript">
var adinj_referrers = new Array($referrer_list);
adinj_searchenginevisitor();
</script>

SCRIPT;
// TODO add comment to script // Ad Injection plugin
	}
}

function adinj_quote_list($option){
	$options = adinj_options();
	$list = $options[$option];
	
	// I'm sure this whole thing could be done with a much simpler single
	// line of PHP - but right now my brain isn't up to thinking about it!
	$lines = explode("\n", $list);
	foreach ($lines as $line){
		$stripped_lines[] = preg_replace("/\/\/.*/", "", $line);
	}
	$list = implode(" ", $stripped_lines);
	
	$list = preg_replace("/'/", "", $list);
	$referrers = preg_split("/[\s,]+/", $list);
	foreach ($referrers as $referrer){
		$newlist[] = "'" . $referrer . "'";
	}
	return implode(", ", $newlist);
}

function adinj_addsevjs_hook(){
	if (adinj_ads_completely_disabled_from_page()) return;
	// Put the search engine detection / cookie setting script in the footer
	wp_enqueue_script('adinj_sev', WP_PLUGIN_URL.'/ad-injection/adinj-sev.js', NULL, NULL, true);
}

function adinj_get_mfunc_code($adfile){
	return "\n
<!--mfunc adshow_display_ad_file('$adfile') -->
<?php adshow_display_ad_file('$adfile'); ?>
<!--/mfunc-->
";
}

function adinj_ad_code_eval($adcode){
	if (stripos($adcode, '<?php') !== false){
		return adinj_eval_php($adcode);
	}
	return $adcode;
}

function adinj_ad_code_include(){
	$plugin_dir = ADINJ_PATH;
	$options = adinj_options();
	$adcode = "";
	if ($options['ad_insertion_mode'] == 'mfunc'){
		// WP Super Cache's support for mclude assumes that we will be including
		// files from within ABSPATH. To remove this limitation we do the include
		// using mfunc instead.
		$adcode = "\n
<!--mfunc include_once('$plugin_dir/adshow.php') -->
<?php include_once('$plugin_dir/adshow.php'); ?>
<!--/mfunc-->
";
	}
	return adinj_eval_php($adcode);
}

function adinj_add_tags($adcode, $prefix, $func=NULL){
	$options = adinj_options();
	if ($options[$prefix . 'align'] !== ADINJ_RULE_DISABLED ||
		$options[$prefix . 'margin_top'] !== ADINJ_RULE_DISABLED ||
		$options[$prefix . 'margin_bottom'] !== ADINJ_RULE_DISABLED) {
		$top = "";
		$bottom = "";
		if ($options[$prefix . 'margin_top'] !== ADINJ_RULE_DISABLED) $top="margin-top:" . $options[$prefix . 'margin_top'] . "px;";
		if ($options[$prefix . 'margin_bottom'] !== ADINJ_RULE_DISABLED) $bottom="margin-bottom:" . $options[$prefix . 'margin_bottom'] . "px;";
		
		if ($options[$prefix . 'align'] == 'left'){
			$div = "<div style='float:left;" . $top . $bottom . "'>ADCODE</div><br clear='all' />";
		} else if ($options[$prefix . 'align'] == 'center'){
			$div = "<div style='" . $top . $bottom . "'><center>ADCODE</center></div>";
		} else if ($options[$prefix . 'align'] == 'right'){
			$div = "<div style='float:right;" . $top . $bottom . "'>ADCODE</div><br clear='all' />";
		} else if ($options[$prefix . 'align'] == 'float left'){
			$div = "<div style='float:left;" . $top . $bottom . "margin-right:5px;'>ADCODE</div>";
		} else if ($options[$prefix . 'align'] == 'float right'){
			$div = "<div style='float:right;" . $top . $bottom . "margin-left:5px;'>ADCODE</div>";
		} else {
			$div = "<div style='" . $top . $bottom . "'>ADCODE</div>";
		}
		if (empty($func)){
			return str_replace("ADCODE", $adcode, $div);
		} else {
			$ad = str_replace("ADCODE", "\$adcode", $div);
			return "function $func(\$adcode) { return \"$ad\"; }";
		}
	}
	if (!empty($func)){
		return "function $func(\$adcode){return \$adcode;}";
	}
	return $adcode;
}

function adinj_ad_code_random(){
	$options = adinj_options();
	$adcode = "";
	if ($options['ad_insertion_mode'] == 'mfunc'){
		$adcode = adinj_get_mfunc_code(ADINJ_AD_RANDOM_FILE);
	} else {
		$adcode = $options['ad_code_random_1'];
		$adcode = adinj_add_tags($adcode, 'rnd_');
	}
	return adinj_ad_code_eval($adcode);
}

function adinj_ad_code_top(){
	global $adinj_total_all_ads_used;
	++$adinj_total_all_ads_used;
	$options = adinj_options();
	$adcode = "";
	if ($options['ad_insertion_mode'] == 'mfunc'){
		$adcode = adinj_get_mfunc_code(ADINJ_AD_TOP_FILE);
	} else {
		$adcode = $options['ad_code_top_1'];
		$adcode = adinj_add_tags($adcode, 'top_');
	}
	return adinj_ad_code_eval($adcode);
}

function adinj_ad_code_bottom(){
	global $adinj_total_all_ads_used;
	++$adinj_total_all_ads_used;
	$options = adinj_options();
	$adcode = "";
	if ($options['ad_insertion_mode'] == 'mfunc'){
		$adcode = adinj_get_mfunc_code(ADINJ_AD_BOTTOM_FILE);
	} else {
		$adcode = $options['ad_code_bottom_1'];
		$adcode = adinj_add_tags($adcode, 'bottom_');
	}
	return adinj_ad_code_eval($adcode);
}

function read_ad_from_file($ad_path){
	$contents = "";
	if (file_exists($ad_path)){
		$contents = file_get_contents($ad_path);
		if ($contents === false) die("can't read from file: $ad_path");
	}
	return $contents;
}

// Based on: http://www.wprecipes.com/wordpress-hack-how-to-display-ads-on-old-posts-only
// Only use for pages and posts. Not for archives, categories, home page, etc.
function adinj_is_old_post(){
	$options = adinj_options();
	$days = $options['ads_on_page_older_than'];
	if ($days == 0) return true;
	if(is_single() || is_page()) {
		$current_date = time();
		$offset = $days * 60*60*24;
		$post_date = get_the_time('U');
		if(($post_date + $offset) > $current_date){
			return false;
		} else {
			return true;
		}
	}
	return false;
}

function adinj_adverts_disabled_flag(){
	$custom_fields = get_post_custom();
	if (isset($custom_fields['disable_adverts'])){
		$disable_adverts = $custom_fields['disable_adverts'];
		return ($disable_adverts[0] == 1);
	}
	return 0;
}

//////////For runtime ads - i.e. when caching is off and config.php not loaded
function adinj_search_engine_referrers(){
	$list = adinj_quote_list('ad_referrers');
	return preg_split("/[,'\s*]/", $list);
}
function adinj_blocked_ips(){
	$list = adinj_quote_list('blocked_ips');
	return preg_split("/[,'\s*]/", $list);
}
function adinj_fromasearchengine(){
	$referrer = $_SERVER['HTTP_REFERER'];
	$searchengines = adinj_search_engine_referrers();
	foreach ($searchengines as $se) {
		if (stripos($referrer, $se) !== false) {
			return true;
		}
	}
	// Also return true if the visitor has recently come from a search engine
	// and has the adinj cookie set.
	return ($_COOKIE["adinj"]==1);
}
function adinj_blocked_ip(){
	$visitorIP = $_SERVER['REMOTE_ADDR'];
	return in_array($visitorIP, adinj_blocked_ips());
}
function adinj_show_adverts(){
	if (adinj_blocked_ip()){
		return "blockedip";
	}
	if (adinj_ticked('sevisitors_only')){
		if (!adinj_fromasearchengine()){
			return "blockedreferrer";
		}
	}
	return true;
}
// From: Exec-PHP plugin
function adinj_eval_php($code)	{
	if (strlen($code) == 0) return $code;
	ob_start();
	eval("?>$code<?php ");
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
//////////For runtime ads

function adinj($content, $message){
	if (!adinj_ticked('debug_mode')) return $content;
	global $adinj_total_rand_ads_used, $adinj_total_all_ads_used;
	$path = ADINJ_AD_PATH;
	$options = adinj_options();
	$mode = $options['ad_insertion_mode'];
	return $content."
<!--
[ADINJ DEBUG]
$message
content length=".strlen($content)."
\$adinj_total_rand_ads_used=$adinj_total_rand_ads_used
\$adinj_total_all_ads_used=$adinj_total_all_ads_used
injection mode=$mode
ADINJ_AD_PATH=$path
//-->\n";
}

function adinj_ads_completely_disabled_from_page($content=NULL){
	if (!adinj_ticked('ads_enabled')){
		return "NOADS: ads_enabled not ticked";
	}
	
	// check for ads on certain page types being disabled
	if ((is_home() && adinj_ticked('exclude_home')) ||
	(is_page() && adinj_ticked('exclude_page')) ||
	(is_single() && adinj_ticked('exclude_single'))){
		return "NOADS: excluded from this page type";
	}
	
	// if disable_adverts==1
	if (adinj_adverts_disabled_flag()) return "NOADS: adverts_disabled_flag";
	
	// no ads on old posts/pages if rule is enabled
	if((is_page() || is_single()) && !adinj_is_old_post()) return "NOADS: !is_old_post";

	if ($content == NULL){
		return false;
	}
	
	// manual ad disabling tags
	if (strpos($content, "<!--noadsense-->") !== false) return "NOADS: noadsense tag"; // 'Adsense Injection' tag
	if (strpos($content, "<!-no-adsense-->") !== false) return "NOADS: no-adsense tag"; // 'Whydowork Adsense' tag
	if (stripos($content,'<!--NoAds-->') !== false) return "NOADS: NoAds tag"; // 'Quick Adsense' tag
	if (stripos($content,'<!--OffAds-->') !== false) return "NOADS: OffAds tag"; // 'Quick Adsense' tag
	
	return false;
}

function adinj_inject_hook($content){
	if (is_feed()) return $content;
	
	$reason = adinj_ads_completely_disabled_from_page($content);
	if ($reason !== false){
		return adinj($content, $reason);
	}

	$options = adinj_options();
	
	if ($options['ad_insertion_mode'] == 'direct_dynamic'){
		$showads = adinj_show_adverts();
		if ($showads !== true){
			return adinj($content, "NOADS: ad blocked at run time reason=$showads");
		}
	}

	$adcode_include = adinj_ad_code_include();
	
	# Ad sandwich mode
	if(is_page() || is_single()){
		if(stripos($content, "<!--adsandwich-->") !== false) return adinj($adcode_include.adinj_ad_code_top().$content.adinj_ad_code_bottom(), "Ads=adsandwich");
		if(stripos($content, "<!--adfooter-->") !== false) return adinj($content.$adcode_include.adinj_ad_code_bottom(), "Ads=adfooter");
	}
	
	# Insert top and bottom ads if necesary
	$length = strlen($content);
	if(is_page() || is_single()){
		if (adinj_do_rule_if($options['top_ad_if_longer_than'], '<', $length)){
			$content = $adcode_include.adinj_ad_code_top().$content;
			$adcode_include = false;
		}
		if (adinj_do_rule_if($options['bottom_ad_if_longer_than'], '<', $length)){
			$content = $content.adinj_ad_code_bottom();
		}
	}
	
	$num_rand_ads_to_insert = adinj_num_rand_ads_to_insert($length);
	if ($num_rand_ads_to_insert <= 0) return adinj($content, "all ads used up");
	
	if ($adcode_include !== false) $content = $adcode_include.$content;
	
	$debug_on = $options['debug_mode'];
	if (!$debug_on) $debugtags=false;
	
	$content_adfree_header = "";
	$content_adfree_footer = "";
	
	// TODO add docs explaining the significance of leaving blank lines
	// before or after these tags
	# 'Adsense Injection' tag compatibility
	$split = adinj_split_by_tag($content, "<!--adsensestart-->", $debugtags);
	if (count($split) == 2){
		$content_adfree_header = $split[0];
		$content = $split[1];
	}
	
	# Use the same naming convention for the end tag
	$split = adinj_split_by_tag($content, "<!--adsenseend-->", $debugtags);
	if (count($split) == 2){
		$content = $split[0];
		$content_adfree_footer = $split[1];
	}
	
	$split = adinj_split_by_tag($content, "<!--adstart-->", $debugtags);
	if (count($split) == 2){
		$content_adfree_header = $split[0];
		$content = $split[1];
	}
	
	$split = adinj_split_by_tag($content, "<!--adend-->", $debugtags);
	if (count($split) == 2){
		$content = $split[0];
		$content_adfree_footer = $split[1];
	}

	# Move onto random ad insertions
	$paragraphmarker = "</p>";
	if(stripos($content, $paragraphmarker) === false) return adinj($content, "no &lt;/p&gt; tags");
	
	if ($debug_on) $debug = "\nTags=". htmlentities($debugtags);  
	
	// Generate a list of all potential injection points
	if ($debug_on) $debug .= "\nPotential positions: ";
	$potential_inj_positions = array();
	$prevpos = -1;
	while(($prevpos = stripos($content, $paragraphmarker, $prevpos+1)) !== false){
		$potential_inj_positions[] = $prevpos + strlen($paragraphmarker);
		if ($debug_on) $debug .= $prevpos.", ";
	}

	if ($debug_on) $debug .= "\npotential_inj_positions:".sizeof($potential_inj_positions);
	
	if (sizeof($potential_inj_positions) == 0){
		return adinj($content, "Error: no potential inj positions");
	}
	
	$inj_positions = array();
	
	if (adinj_ticked('first_paragraph_ad')){
		$inj_positions[] = array_shift($potential_inj_positions);
		--$num_rand_ads_to_insert;
	}

	// Pick the correct number of random injection points
	if (sizeof($potential_inj_positions) > 0 && $num_rand_ads_to_insert > 0){
		if (!adinj_ticked('multiple_ads_at_same_position')){
			// Each ad is inserted into a unique position
			if (sizeof($potential_inj_positions) < $num_rand_ads_to_insert){
				$debug .= "\nnum_rand_ads_to_insert requested=$num_rand_ads_to_insert. But restricted to ". sizeof($potential_inj_positions) . " due to limited injection points.";
				$num_rand_ads_to_insert = sizeof($potential_inj_positions);
			}
			$rand_positions = array_rand(array_flip($potential_inj_positions), $num_rand_ads_to_insert);
			if ($num_rand_ads_to_insert == 1){
				// Convert it back into an array
				$inj_positions[] = $rand_positions;
			} else {
				$inj_positions = array_merge($inj_positions, $rand_positions);
			}
			foreach($inj_positions as $pos){
				if ($debug_on) $debug = $pos . ", " . $debug;
			}
		} else {
			// Multiple ads may be inserted at the same position
			$injections = 0;
			while($injections++ < $num_rand_ads_to_insert){
				$rnd = array_rand($potential_inj_positions);
				if ($debug_on) $debug = $potential_inj_positions[$rnd] . ", " . $debug;
				$inj_positions[] = $potential_inj_positions[$rnd];
			}
		}
	}
	
	if (sizeof($inj_positions) == 0){
		return adinj($content_adfree_header.$content.$content_adfree_footer, "Error: No ad injection positions: " . $debug);
	}
	
	// Sort positions
	sort($inj_positions);
	
	// Insert ads in reverse order
	$adcode = adinj_ad_code_random();
	global $adinj_total_rand_ads_used, $adinj_total_all_ads_used;
	for ($adnum=sizeof($inj_positions)-1; $adnum>=0; $adnum--){
		$content = substr_replace($content, $adcode, $inj_positions[$adnum], 0);
		++$adinj_total_rand_ads_used;
		++$adinj_total_all_ads_used;
	}

	return adinj($content_adfree_header.$content.$content_adfree_footer, "Ads injected: " . $debug);
}

function adinj_split_by_tag($content, $tag, &$debugtags=false){
	$ret = array();
	if (strpos($content, $tag) !== false){
		if ($debugtags !== false) $debugtags .= "$tag, ";
		$content_split = explode($tag, $content, 2);
		$ret[] = $content_split[0];
		if (count($content_split) == 2){
			$ret[] = $content_split[1];
		}
	}
	return $ret;
}

function adinj_num_rand_ads_to_insert($content_length){
	global $adinj_total_rand_ads_used; // a page can be more than one post
	$options = adinj_options();
	if (is_single() || is_page()){
		$max_num_rand_ads_to_insert = $options['max_num_of_ads'] - $adinj_total_rand_ads_used;
	} else if (is_home()){
		$max_num_rand_ads_to_insert = $options['max_num_of_ads_home_page'] - $adinj_total_rand_ads_used;
	} else {
		return 0;
		//TODO Bug reported: 'Something badly wrong in num_rand_ads_to_insert');
	}
	if ($max_num_rand_ads_to_insert <= 0) {
		return 0;
	}
	if(!is_single() && !is_page()) {
		// If there are multiple posts on page only show one ad per post
		// This rule from 'Adsense Injection'.
		return 1;
	}
	$length = $content_length;
	if (adinj_do_rule_if($options['no_random_ads_if_shorter_than'], '>', $length)){
		return 0;
	}
	if (adinj_do_rule_if($options['one_ad_if_shorter_than'], '>', $length)){
		return 1;
	}
	if (adinj_do_rule_if($options['two_ads_if_shorter_than'], '>', $length)){
		return min(2, $max_num_rand_ads_to_insert);
	}
	if (adinj_do_rule_if($options['three_ads_if_shorter_than'], '>', $length)){
		return min(3, $max_num_rand_ads_to_insert);
	}
	return $max_num_rand_ads_to_insert;
}

function adinj_do_rule_if($rule_value, $condition, $content_length){
	if ($rule_value == ADINJ_ALWAYS_SHOW) return true;
	if ($condition == '>'){
		return ($rule_value != ADINJ_RULE_DISABLED && $rule_value > $content_length);
	} else if ($condition == '<'){
		return ($rule_value != ADINJ_RULE_DISABLED && $rule_value < $content_length);
	} else {
		die("adinj_do_rule_if bad condition: $condition");
	}
}

function adinj_ticked($option){
	$options = adinj_options();
	if (!empty($options[$option])) return 'checked="checked"';
	return false;
}

// activate
register_activation_hook( __FILE__, 'adinj_activate_hook' );
// Content injection
add_action('wp_enqueue_scripts', 'adinj_addsevjs_hook');
add_action('wp_footer', 'adinj_print_referrers_hook');
add_filter('the_content', 'adinj_inject_hook');

?>