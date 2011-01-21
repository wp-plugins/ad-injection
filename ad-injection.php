<?php
/*
Plugin Name: Ad Injection
Plugin URI: http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/
Description: Injects any advert (e.g. AdSense) into your WordPress posts or widget area. Restrict who sees the ads by post length, age, referrer or IP. Cache compatible.
Version: 0.9.6.1
Author: reviewmylife
Author URI: http://www.reviewmylife.co.uk/
License: GPLv2
*/

/* License header moved to ad-injection-admin.php */

//error_reporting(E_ALL ^ E_STRICT);

//
define('ADINJ_NO_CONFIG_FILE', 1);

//
define('ADINJ_DB_VERSION', 2);

// Files
define('ADINJ_PATH', WP_PLUGIN_DIR.'/ad-injection');
define('ADINJ_CONFIG_FILE', WP_CONTENT_DIR . '/ad-injection-config.php'); // same directory as WP Super Cache config file
define('ADINJ_AD_PATH', WP_PLUGIN_DIR.'/ad-injection-data'); // ad store from 0.9.2
define('ADINJ_AD_RANDOM_FILE', 'ad_random_1.txt');
define('ADINJ_AD_TOP_FILE', 'ad_top_1.txt');
define('ADINJ_AD_BOTTOM_FILE', 'ad_bottom_1.txt');

// Constants
define('ADINJ_DISABLED', 'Disabled');
define('ADINJ_RULE_DISABLED', 'Rule Disabled');
define('ADINJ_ALWAYS_SHOW', 'Always show');
//
define('ADINJ_ONLY_SHOW_IN', 'Only show in');
define('ADINJ_NEVER_SHOW_IN', 'Never show in');

// Global variables
$adinj_total_rand_ads_used = 0;
$adinj_total_all_ads_used = 0;
$adinj_data = array();

require_once(ADINJ_PATH . '/adshow.php');
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
	$ops = adinj_options();
	return $ops[$option];
}

// TODO make the cookie domain from wp-config.php accessible to script
//$cookie_domain = COOKIE_DOMAIN; // TODO test
//var adinj_cookie_domain = "$cookie_domain"; //JS line. TODO test
function adinj_print_referrers_hook(){
	// TODO can re-enable this check once the widget ads are factored in.
	//if (adinj_ads_completely_disabled_from_page()) return;
	if (adinj_ticked('sevisitors_only')){
		$referrer_list = adinj_quote_list('ad_referrers');
	echo <<<SCRIPT
<script type="text/javascript">
// Ad Injection plugin
var adinj_referrers = new Array($referrer_list);
adinj_searchenginevisitor();
</script>

SCRIPT;
	}
}

function adinj_quote_list($option){
	$ops = adinj_options();
	$list = $ops[$option];
	
	// I'm sure this whole thing could be done with a much simpler single
	// line of PHP - but right now my brain isn't up to thinking about it!
	$lines = explode("\n", $list);
	foreach ($lines as $line){
		$stripped_lines[] = preg_replace("/\/\/.*/", "", $line);
	}
	$list = implode(" ", $stripped_lines);
	
	$list = preg_replace("/'/", "", $list);
	$referrers = preg_split("/[\s,]+/", $list, -1, PREG_SPLIT_NO_EMPTY);
	if (empty($referrers)) return '';
	foreach ($referrers as $referrer){
		$newlist[] = "'" . $referrer . "'";
	}
	return implode(", ", $newlist);
}

function adinj_addsevjs_hook(){
	// TODO can re-enable this check once the widget ads are factored in.
	//if (adinj_ads_completely_disabled_from_page()) return;
	if (!adinj_ticked('sevisitors_only')) return;
	// Put the search engine detection / cookie setting script in the footer
	wp_enqueue_script('adinj_sev', WP_PLUGIN_URL.'/ad-injection/adinj-sev.js', NULL, NULL, true);
}

function adinj_get_ad_code($adtype, $ads_db){
	$ops = adinj_options();
	$ads_live = NULL;
	$ads_split = NULL;
	$alt_live = NULL;
	$alt_split = NULL;
	if ($ops['ad_insertion_mode'] == 'mfunc'){
		adinj_live_ads_array($adtype, $ads_db, $ads_live, $ads_split, 'string');
		if (adinj_db_version($ads_db) >= 2){
			adinj_live_ads_array($adtype.'_alt', $ads_db, $alt_live, $alt_split, 'string');
		}
		$formatting = adinj_formatting_options($adtype, 'string');
	} else {
		$ads_live = array();
		$ads_split = array();
		$alt_live = array();
		$alt_split = array();
		adinj_live_ads_array($adtype, $ads_db, $ads_live, $ads_split, 'array');
		if (adinj_db_version($ads_db) >= 2){
			adinj_live_ads_array($adtype.'_alt', $ads_db, $alt_live, $alt_split, 'array');
		}
		$formatting = adinj_formatting_options($adtype, 'array');
	}
	if (empty($ads_live) && empty($alt_live)) return "<!--ADINJ DEBUG: ads_live and alt_live are empty-->";

	if ($ops['ad_insertion_mode'] == 'mfunc'){
		return adinj_ad_code_eval("\n
<!--mfunc adshow_display_ad_file_v2(array($ads_live), array($ads_split), array($formatting), array($alt_live), array($alt_split)) -->
<?php adshow_display_ad_file_v2(array($ads_live), array($ads_split), array($formatting), array($alt_live), array($alt_split)); ?>
<!--/mfunc-->
");
	}
	
	// else dynamic ad
	if ($ops['ad_insertion_mode'] == 'direct_dynamic' && adshow_show_adverts() !== true){
		$adname = adshow_pick_value($alt_live, $alt_split);
	} else {
		$adname = adshow_pick_value($ads_live, $ads_split);
	}
	
	$ad = adshow_add_formatting($ads_db[$adname], $formatting);
	return adinj_ad_code_eval($ad);
}

function adinj_ad_code_random(){
	return adinj_get_ad_code('random', adinj_options());
}

function adinj_ad_code_top(){
	$ad = adinj_get_ad_code('top', adinj_options());
	global $adinj_total_all_ads_used;
	++$adinj_total_all_ads_used;
	return $ad;
}

function adinj_ad_code_bottom(){
	$ad = adinj_get_ad_code('bottom', adinj_options());
	global $adinj_total_all_ads_used;
	++$adinj_total_all_ads_used;
	return $ad;
}

/**
Old:
ad_code_random_1 <-> ad_random_1.txt

New:
ad_code_random_1:ad_code_random_1_split <-> ad_random_1.txt
ad_code_random_alt_1:ad_code_random_alt_1_split <-> ad_random-alt_1.txt
*/
function adinj_live_ads_array($type, $ads_option, &$ads, &$split, $output_type="string"){
	$op_stem = "";
	$file_stem = "";

	if ($type == 'random' || $type == 'top' || $type == 'bottom' || 
		$type == 'random_alt' || $type == 'top_alt' || $type == 'bottom_alt'){
		$op_stem = 'ad_code_'.$type.'_';
		$file_stem = 'ad_'.$type.'_';
	} else if (preg_match("/widget_[\d+]/i", $type)){
		if (strpos($type, '_alt') === false){
			$op_stem = 'advert_';
			$file_stem = 'ad_'.$type.'_';
		} else {
			$op_stem = 'advert_alt_';
			$file_stem = 'ad_'.$type.'_';
		}
	}
	
	if (adinj_db_version($ads_option) == 1){
		// old DB support (no ad rotation support) - TODO  delete later
		if ($type == 'random'){
			if ($output_type == "string"){
				$ads = "'ad_random_1.txt'";
			} else {
				$ads[] = 'ad_code_random_1';
			}
		} else if ($type == 'top'){
			if ($output_type == "string"){
				$ads = "'ad_top_1.txt'";
			} else {
				$ads[] = 'ad_code_top_1';
			}
		} else if ($type == 'bottom'){
			if ($output_type == "string"){
				$ads = "'ad_bottom_1.txt'";
			} else {
				$ads[] = 'ad_code_bottom_1';
			}
		} else if (preg_match("/widget_[\d+]/i", $type)){
			if ($output_type == "string"){
				$ads = "'ad_".$type.".txt'";
			} else {
				$ads[] = 'advert';
			}
		}
		return;
	}
	
	// DB with support for ad rotation
	for ($i=1; $i<=10; ++$i){
		$ad_name = $op_stem.$i;
		if (!array_key_exists($ad_name.'_split', $ads_option)) return;
		
		$split_val = $ads_option[$ad_name.'_split'];
		
		if (!empty($ads_option[$ad_name]) && is_numeric($split_val) && $split_val > 0){
			//echo "<br />$ad_name $i size:".sizeof($ads_option[$ad_name]);
			if ($output_type == "string"){
				if (!empty($ads)) $ads .= ",";
				$ads .= "'".$file_stem."$i.txt'";
				if (!empty($split)) $split .= ",";
				$split .= $split_val;
			} else {
				$ads[] = $ad_name;
				$split[] = $split_val;
			}
		}
	}
}

function adinj_formatting_options($adtype, $output_type="string", $options = array()){
	$ops = adinj_options();
	$align = "";
	$clear = "";
	$margin_top = "";
	$margin_bottom = "";
	$padding_top = "";
	$padding_bottom = "";

	if ($adtype == 'random'){
		$align = $ops['rnd_align'];
		$clear = $ops['rnd_clear'];
		$margin_top = $ops['rnd_margin_top'];
		$margin_bottom = $ops['rnd_margin_bottom'];
		$padding_top = $options['rnd_padding_top'];
		$padding_bottom = $options['rnd_padding_bottom'];
	} else if ($adtype == 'top' || $adtype == 'bottom'){
		$align = $ops[$adtype.'_align'];
		$clear = $ops[$adtype.'_clear'];
		$margin_top = $ops[$adtype.'_margin_top'];
		$margin_bottom = $ops[$adtype.'_margin_bottom'];
		$padding_top = $options[$adtype.'_padding_top'];
		$padding_bottom = $options[$adtype.'_padding_bottom'];
	} else if ($adtype == 'widget'){
		$align = $options['align'];
		$clear = $options['clear'];
		$margin_top = $options['margin_top'];
		$margin_bottom = $options['margin_bottom'];
		$padding_top = $options['padding_top'];
		$padding_bottom = $options['padding_bottom'];
	}
	
	if (adinj_disabled($align)) $align = "";
	if (adinj_disabled($clear)) $clear = "";
	if (adinj_disabled($margin_top)) $margin_top = "";
	if (adinj_disabled($margin_bottom)) $margin_bottom = "";
	if (adinj_disabled($padding_top)) $padding_top = "";
	if (adinj_disabled($padding_bottom)) $padding_bottom = "";
	
	if ($output_type == "string"){
		return "'align' => '$align', 'clear' => '$clear', 'margin_top' => '$margin_top', 'margin_bottom' => '$margin_bottom', 'padding_top' => '$padding_top', 'padding_bottom' => '$padding_bottom'";
	} else {
		return array('align' => $align,
			'clear' => $clear,
			'margin_top' => $margin_top,
			'margin_bottom' => $margin_bottom,
			'padding_top' => $padding_top,
			'padding_bottom' => $padding_bottom);
	}
}

function adinj_ad_code_eval($ad){
	if (strlen($ad) == 0) return $ad;
	if (stripos($ad, '<?php') !== false){
		return adshow_eval_php($ad);
	}
	return $ad;
}

function adinj_ad_code_include(){
	$plugin_dir = ADINJ_PATH;
	$ops = adinj_options();
	$ad = "";
	if ($ops['ad_insertion_mode'] == 'mfunc'){
		// WP Super Cache's support for mclude assumes that we will be including
		// files from within ABSPATH. To remove this limitation we do the include
		// using mfunc instead.
		$ad = "\n
<!--mfunc include_once('$plugin_dir/adshow.php') -->
<?php include_once('$plugin_dir/adshow.php'); ?>
<!--/mfunc-->
";
	}
	return adinj_ad_code_eval($ad);
}

//TODO delete this
function adinj_add_tags($ad, $prefix, $func=NULL){
	$ops = adinj_options();
	if (!adinj_disabled($ops[$prefix . 'align']) ||
		!adinj_disabled($ops[$prefix . 'clear']) ||
		!adinj_disabled($ops[$prefix . 'margin_top']) ||
		!adinj_disabled($ops[$prefix . 'margin_bottom']) ||
		!adinj_disabled($ops[$prefix . 'padding_top']) ||
		!adinj_disabled($ops[$prefix . 'padding_bottom'])) {
		$clear = "";
		$top = "";
		$bottom = "";
		$ptop = "";
		$pbottom = "";
		if (!adinj_disabled($ops[$prefix . 'clear'])) $clear="clear:" . $ops[$prefix . 'clear'] . ";";
		if (!adinj_disabled($ops[$prefix . 'margin_top'])) $top="margin-top:" . $ops[$prefix . 'margin_top'] . "px;";
		if (!adinj_disabled($ops[$prefix . 'margin_bottom'])) $bottom="margin-bottom:" . $ops[$prefix . 'margin_bottom'] . "px;";
		if (!adinj_disabled($ops[$prefix . 'padding_top'])) $ptop="padding-top:" . $ops[$prefix . 'padding_top'] . "px;";
		if (!adinj_disabled($ops[$prefix . 'padding_bottom'])) $pbottom="padding-bottom:" . $ops[$prefix . 'padding_bottom'] . "px;";
		$cssrules = $clear . $top . $bottom . $ptop . $pbottom;
		
		if ($ops[$prefix . 'align'] == 'left'){
			$div = "<div style='float:left;" . $cssrules . "'>ADCODE</div><br clear='all' />";
		} else if ($ops[$prefix . 'align'] == 'center'){
			$div = "<div style='" . $cssrules . "'><center>ADCODE</center></div>";
		} else if ($ops[$prefix . 'align'] == 'right'){
			$div = "<div style='float:right;" . $cssrules . "'>ADCODE</div><br clear='all' />";
		} else if ($ops[$prefix . 'align'] == 'float left'){
			$div = "<div style='float:left;" . $cssrules . "margin-right:5px;'>ADCODE</div>";
		} else if ($ops[$prefix . 'align'] == 'float right'){
			$div = "<div style='float:right;" . $cssrules . "margin-left:5px;'>ADCODE</div>";
		} else {
			$div = "<div style='" . $cssrules . "'>ADCODE</div>";
		}
		if (empty($func)){
			return str_replace("ADCODE", $ad, $div);
		} else {
			$ad = str_replace("ADCODE", "\$ad", $div);
			return "function $func(\$ad) { return \"$ad\"; }";
		}
	}
	if (!empty($func)){
		return "function $func(\$ad){return \$ad;}";
	}
	return $ad;
}

function read_ad_from_file($ad_path){
	$contents = "";
	if (file_exists($ad_path)){
		$contents = file_get_contents($ad_path);
		if ($contents === false) return "Error: can't read from file: $ad_path";
	}
	return $contents;
}

// Based on: http://www.wprecipes.com/wordpress-hack-how-to-display-ads-on-old-posts-only
// Only use for pages and posts. Not for archives, categories, home page, etc.
function adinj_is_old_post(){
	$ops = adinj_options();
	$days = $ops['ads_on_page_older_than'];
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

function adinj($content, $message){
	if (!adinj_ticked('debug_mode')) return $content;
	global $adinj_total_rand_ads_used, $adinj_total_all_ads_used;
	$path = ADINJ_AD_PATH;
	$ops = adinj_options();
	$para = adinj_paragraph_to_start_ads();
	$mode = $ops['ad_insertion_mode'];
	return $content."
<!--
[ADINJ DEBUG]
$message
content length=".strlen($content)."
\$adinj_total_rand_ads_used=$adinj_total_rand_ads_used
\$adinj_total_all_ads_used=$adinj_total_all_ads_used
paragraphtostartads=$para (fyi: -1 is disabled)
injection mode=$mode
ADINJ_AD_PATH=$path
//-->\n";
}

function adinj_ads_completely_disabled_from_page($content=NULL){
	$ops = adinj_options();
	if ($ops['ads_enabled'] == 'off' || 
		$ops['ads_enabled'] == ''){
		return "NOADS: Ads are not switched on";
	}
	if ($ops['ads_enabled'] == 'test' && !current_user_can('activate_plugins')){
		return "NOADS: test mode enabled - ads only shown admins";
	}
	
	// check for ads on certain page types being disabled
	if ((is_home() && adinj_ticked('exclude_home')) ||
	(is_page() && adinj_ticked('exclude_page')) ||
	(is_single() && adinj_ticked('exclude_single')) ||
	(is_archive() && adinj_ticked('exclude_archive')) || 
		is_search() || is_404()){
		return "NOADS: excluded from this post type-".get_post_type();
	}
	
	// if disable_adverts==1
	if (adinj_adverts_disabled_flag()) return "NOADS: adverts_disabled_flag";
	
	// no ads on old posts/pages if rule is enabled
	if((is_page() || is_single()) && !adinj_is_old_post()) return "NOADS: !is_old_post";

	$category_ok = adinj_allowed_in_category('global');	
	if (!$category_ok) return "NOADS: blocked from category";
	
	$tag_ok = adinj_allowed_in_tag('global');	
	if (!$tag_ok) return "NOADS: blocked from tag";
	
	// manual ad disabling tags
	if ($content == NULL) return false;
	if (strpos($content, "<!--noadsense-->") !== false) return "NOADS: noadsense tag"; // 'Adsense Injection' tag
	if (strpos($content, "<!-no-adsense-->") !== false) return "NOADS: no-adsense tag"; // 'Whydowork Adsense' tag
	if (stripos($content,'<!--NoAds-->') !== false) return "NOADS: NoAds tag"; // 'Quick Adsense' tag
	if (stripos($content,'<!--OffAds-->') !== false) return "NOADS: OffAds tag"; // 'Quick Adsense' tag
	
	return false;
}

function adinj_allowed_in_category($scope){
	$ops = adinj_options();
	$cat_list = $ops[$scope.'_category_condition_entries'];
	$cat_array = preg_split("/[\s,]+/", $cat_list, -1, PREG_SPLIT_NO_EMPTY);
	if (empty($cat_array)) return true;
	
	$cat_mode = $ops[$scope.'_category_condition_mode'];
	global $post;
	$postcategories = get_the_category($post->ID);
	if (is_array($postcategories)){
		foreach($postcategories as $cat) {
			$postcat = $cat->category_nicename;
			if (in_array($postcat, $cat_array)){
				if ($cat_mode == ADINJ_ONLY_SHOW_IN){
					return true;
				} else if ($cat_mode == ADINJ_NEVER_SHOW_IN){
					return false;
				}
			}
		}
	}
	if ($cat_mode == ADINJ_ONLY_SHOW_IN){
		return false;
	} else if ($cat_mode == ADINJ_NEVER_SHOW_IN){
		return true;
	}
	echo ("<!--ADINJ DEBUG: error in adinj_allowed_in_category-->");
	return true;
}

function adinj_allowed_in_tag($scope){
	$ops = adinj_options();
	$tag_list = $ops[$scope.'_tag_condition_entries'];
	$tag_array = preg_split("/[\s,]+/", $tag_list, -1, PREG_SPLIT_NO_EMPTY);
	if (empty($tag_array)) return true;
	
	$tag_mode = $ops[$scope.'_tag_condition_mode'];
	global $post;
	$posttags = get_the_tags($post->ID);
	if (is_array($posttags)){
		foreach($posttags as $tag) {
			$posttag = $tag->slug;
			if (in_array($posttag, $tag_array)){
				if ($tag_mode == ADINJ_ONLY_SHOW_IN){
					return true;
				} else if ($tag_mode == ADINJ_NEVER_SHOW_IN){
					return false;
				}
			}
		}
	}
	if ($tag_mode == ADINJ_ONLY_SHOW_IN){
		return false;
	} else if ($tag_mode == ADINJ_NEVER_SHOW_IN){
		return true;
	}
	echo ("<!--ADINJ DEBUG: error in adinj_allowed_in_tag-->");
	return true;
}

function adinj_inject_hook($content){
	global $adinj_total_rand_ads_used;
	if (is_feed()) return $content; // TODO feed specific ads
	
	adinj_upgrade_db_if_necessary();
	
	if(is_page() || is_single()){
		// TODO hack for no random ads bug
		$adinj_total_rand_ads_used = 0;
	}

	$reason = adinj_ads_completely_disabled_from_page($content);
	if ($reason !== false){
		return adinj($content, $reason);
	}

	$ops = adinj_options();
	
	if ($ops['ad_insertion_mode'] == 'direct_dynamic'){
		$showads = adshow_show_adverts();
		if ($showads !== true){
			return adinj($content, "NOADS: ad blocked at run time reason=$showads");
			// TODO alt content
		}
	}

	$ad_include = adinj_ad_code_include();
	
	# Ad sandwich mode
	if(is_page() || is_single()){
		if(stripos($content, "<!--adsandwich-->") !== false) return adinj($ad_include.adinj_ad_code_top().$content.adinj_ad_code_bottom(), "Ads=adsandwich");
		if(stripos($content, "<!--adfooter-->") !== false) return adinj($content.$ad_include.adinj_ad_code_bottom(), "Ads=adfooter");
	}
	
	# Insert top and bottom ads if necesary
	$length = strlen($content);
	if(is_page() || is_single()){
		if (adinj_do_rule_if($ops['top_ad_if_longer_than'], '<', $length)){
			$content = $ad_include.adinj_ad_code_top().$content;
			$ad_include = false;
		}
		if (adinj_do_rule_if($ops['bottom_ad_if_longer_than'], '<', $length)){
			$content = $content.adinj_ad_code_bottom();
		}
	}
	
	$num_rand_ads_to_insert = adinj_num_rand_ads_to_insert($length);
	if ($num_rand_ads_to_insert <= 0) return adinj($content, "all ads used up");
	$ad = adinj_ad_code_random();
	if (empty($ad)) return adinj($content, "no random ad defined");
	
	if ($ad_include !== false) $content = $ad_include.$content;
	
	$debug_on = $ops['debug_mode'];
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

	// TODO add note explaining that start tags are processed before the 'first
	// paragraph ad
	
	// Move onto random ad insertions
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
	
	$startparagraph = adinj_paragraph_to_start_ads();
	if ($startparagraph > 0){
		$pos = NULL;
		for ($i=0; $i<$startparagraph; ++$i){
			// discard positions until we get to the starting paragraph
			$pos = array_shift($potential_inj_positions);
		}
		if ($pos != NULL){
			$inj_positions[] = $pos;
			--$num_rand_ads_to_insert;
		}
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
	global $adinj_total_rand_ads_used, $adinj_total_all_ads_used;
	for ($adnum=sizeof($inj_positions)-1; $adnum>=0; $adnum--){
		$content = substr_replace($content, $ad, $inj_positions[$adnum], 0);
		++$adinj_total_rand_ads_used;
		++$adinj_total_all_ads_used;
	}

	return adinj($content_adfree_header.$content.$content_adfree_footer, "Ads injected: " . $debug);
}

function adinj_paragraph_to_start_ads(){
	$ops = adinj_options();
	if (adinj_db_version($ops) == 1){
		if (adinj_ticked('first_paragraph_ad')) return 1;
		return -1;
	}
	if (adinj_disabled('start_from_paragraph')){
		return -1;
	} else {
		return $ops['start_from_paragraph'];
	}
}

function adinj_split_by_tag($content, $tag, &$debugtags){
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
	$ops = adinj_options();
	if (is_single() || is_page()){
		$max_num_rand_ads_to_insert = $ops['max_num_of_ads'] - $adinj_total_rand_ads_used;
	} else if (is_home()){
		$max_num_rand_ads_to_insert = $ops['max_num_of_ads_home_page'] - $adinj_total_rand_ads_used;
	} else {
		return 0;
		//TODO Allow ads in other page types later
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
	if (adinj_do_rule_if($ops['no_random_ads_if_shorter_than'], '>', $length)){
		return 0;
	}
	if (adinj_do_rule_if($ops['one_ad_if_shorter_than'], '>', $length)){
		return 1;
	}
	if (adinj_do_rule_if($ops['two_ads_if_shorter_than'], '>', $length)){
		return min(2, $max_num_rand_ads_to_insert);
	}
	if (adinj_do_rule_if($ops['three_ads_if_shorter_than'], '>', $length)){
		return min(3, $max_num_rand_ads_to_insert);
	}
	return $max_num_rand_ads_to_insert;
}

function adinj_do_rule_if($rule_value, $condition, $content_length){
	if ($rule_value == ADINJ_ALWAYS_SHOW) return true;
	if (adinj_disabled($rule_value)) return false;
	if ($condition == '>'){
		return ($rule_value > $content_length);
	} else if ($condition == '<'){
		return ($rule_value < $content_length);
	} else {
		die("adinj_do_rule_if bad condition: $condition");
	}
}

function adinj_disabled($value){
	return $value == ADINJ_RULE_DISABLED || $value == ADINJ_DISABLED || $value == '';
}

function adinj_ticked($option){
	$ops = adinj_options();
	if (!empty($ops[$option]) && $ops[$option] != 'off') return 'checked="checked"';
	return false;
}

function adinj_upgrade_db_if_necessary(){
	$stored_options = adinj_options();
	if(empty($stored_options)){
		// 1st Install.
		adinj_install_db();
		return;
	}

	$stored_dbversion = adinj_db_version($stored_options);
	
	if (ADINJ_DB_VERSION != $stored_dbversion){
		require_once(ADINJ_PATH . '/ad-injection-admin.php');
		adinj_upgrade_db();
	}
}

// Main options table and widgets could have different db version at the same
// time depending on when the settings were last saved
function adinj_db_version($ops){
	if (!array_key_exists('db_version', $ops)){
		return 1;
	} else {
		return $ops['db_version'];
	}
}

// Widget support
require_once('ad-injection-widget.php');
add_action('widgets_init', 'adinj_widgets_init');
function adinj_widgets_init() {
	register_widget('Ad_Injection_Widget');
}

// activate
register_activation_hook(__FILE__, 'adinj_activate_hook');
// Content injection
add_action('wp_enqueue_scripts', 'adinj_addsevjs_hook');
add_filter('the_content', 'adinj_inject_hook');
add_action('wp_footer', 'adinj_print_referrers_hook');

?>