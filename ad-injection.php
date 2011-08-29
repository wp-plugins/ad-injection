<?php
/*
Plugin Name: Ad Injection
Plugin URI: http://www.reviewmylife.co.uk/blog/2010/12/06/ad-injection-plugin-wordpress/
Description: Injects any advert (e.g. AdSense) into your WordPress posts or widget area. Restrict who sees the ads by post length, age, referrer or IP. Cache compatible.
Version: 1.1.0.3
Author: reviewmylife
Author URI: http://www.reviewmylife.co.uk/
License: GPLv2
*/

/* License header moved to ad-injection-admin.php */

//error_reporting(E_ALL ^ E_STRICT);

//
define('ADINJ_NO_CONFIG_FILE', 1);

// DB Version
// _ = before split testing
// 2 = split testing support
// 3 = added front options
// 4 = new character counting option
// 5 = search/404 exclusion, increase db ad rotations to 10 + author conditions
// 6 = archive/home options
// 7 = cat/tag/author restriction for top/random/bottom ads
// 8 = ui options for new layout
// 9 = replace the two direct modes with 'direct'
// 10 = exclusion tick boxes for top, random, bottom, and new footer ad
// 11 = options to disable rnd ad at bottom, and to get new ad for each rnd slot
// 13 = post/page id restrictions
// 14 = template ads
// 15 = remove duplicate 'Disabled' option from top/bottom ad section
// 16 = after paragraph options, older option for widget
define('ADINJ_DB_VERSION', 16);

// Files
// TODO will these paths work on windows?
define('ADINJ_PATH', WP_PLUGIN_DIR.'/ad-injection');
define('ADINJ_CONFIG_FILE', WP_CONTENT_DIR . '/ad-injection-config.php');
define('ADINJ_AD_PATH', WP_PLUGIN_DIR.'/ad-injection-data');

// Constants
define('ADINJ_DISABLED', 'Disabled'); // todo deprecated?
define('ADINJ_RULE_DISABLED', 'Rule Disabled'); // todo depreacated?
define('ADINJ_ALWAYS_SHOW', 'Always show'); // todo deprecated?
//
define('ADINJ_ONLY_SHOW_IN', 'Only show in');
define('ADINJ_NEVER_SHOW_IN', 'Never show in');
define('ADINJ_NA', 'n/a');

// Global variables
$adinj_total_top_ads_used = 0;
$adinj_total_random_ads_used = 0;
$adinj_total_bottom_ads_used = 0;
$adinj_total_all_ads_used = 0;
$adinj_data = array();

require_once(ADINJ_PATH . '/adshow.php');
if (is_admin()){
	require_once(ADINJ_PATH . '/ad-injection-admin.php');
}

function adinj_admin_menu_hook(){
	$options_page = add_options_page('Ad Injection', 'Ad Injection', 'manage_options', basename(__FILE__), 'adinj_options_page');
	add_action("admin_print_scripts-".$options_page, "adinj_admin_print_scripts_main");
	add_action("admin_print_scripts-widgets.php", "adinj_admin_print_scripts_widgets");
	
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
	$formatting = NULL;
	if (adinj_mfunc_mode()){
		adinj_live_ads_array($adtype, $ads_db, $ads_live, $ads_split, 'string');
		if (adinj_db_version($ads_db) >= 2){
			adinj_live_ads_array($adtype.'_alt', $ads_db, $alt_live, $alt_split, 'string');
		}
		$formatting = adinj_formatting_options($adtype, $ads_db, 'string');
	} else {
		$ads_live = array();
		$ads_split = array();
		$alt_live = array();
		$alt_split = array();
		adinj_live_ads_array($adtype, $ads_db, $ads_live, $ads_split, 'array');
		if (adinj_db_version($ads_db) >= 2){
			adinj_live_ads_array($adtype.'_alt', $ads_db, $alt_live, $alt_split, 'array');
		}
		$formatting = adinj_formatting_options($adtype, $ads_db, 'array');
	}
	if (empty($ads_live) && empty($alt_live)){
		return "";
	}
	if (adinj_mfunc_mode()){
		return adinj_ad_code_eval("\n
<!--mfunc adshow_display_ad_file_v2(array($ads_live), array($ads_split), array($formatting), array($alt_live), array($alt_split)) -->
<?php adshow_display_ad_file_v2(array($ads_live), array($ads_split), array($formatting), array($alt_live), array($alt_split)); ?>
<!--/mfunc-->
");
	}
	
	// else dynamic ad
	if (adshow_show_adverts() !== true){
		$adname = adshow_pick_value($alt_live, $alt_split);
	} else {
		$adname = adshow_pick_value($ads_live, $ads_split);
	}
	$ad = $ads_db[$adname];
	
	if (empty($ad)){
		return "";
	}
	
	$ad = adshow_add_formatting($ad, $formatting);
	return "<!--Ad Injection:$adtype-->".adinj_ad_code_eval($ad);
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

function adinj_ad_code_footer(){
	$ad = adinj_get_ad_code('footer', adinj_options());
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

	if ($type == 'random' || $type == 'top' || $type == 'bottom' || $type == 'footer' ||
		$type == 'random_alt' || $type == 'top_alt' || $type == 'bottom_alt' || $type == 'footer_alt'){
		$op_stem = 'ad_code_'.$type.'_';
		$file_stem = 'ad_'.$type.'_';
	} else if (preg_match("/widget_[\d+]/i", $type)){
		if (strpos($type, '_alt') === false){
			$op_stem = 'advert_';
		} else {
			$op_stem = 'advert_alt_';
		}
		$file_stem = 'ad_'.$type.'_';
	}
	
	if (adinj_db_version($ads_option) == 1){
		// old DB support (no ad rotation support) - TODO  delete later
		if ($type == 'random'|| $type == 'top' || $type == 'bottom'){
			if ($output_type == "string"){
				$ads = "'ad_".$type."_1.txt'";
			} else {
				$ads[] = 'ad_code_'.$type.'_1';
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

function adinj_formatting_options($adtype, $ops, $output_type="string"){
	$align = "";
	$clear = "";
	$margin_top = "";
	$margin_bottom = "";
	$padding_top = "";
	$padding_bottom = "";

	$prefix = '';
	if ($adtype == 'random') $prefix = 'rnd_';
	if ($adtype == 'top') $prefix = 'top_';
	if ($adtype == 'bottom') $prefix = 'bottom_';
	if ($adtype == 'footer') $prefix = 'footer_';
	//widgets have no prefix

	$align = $ops[$prefix.'align'];
	$clear = $ops[$prefix.'clear'];
	$margin_top = $ops[$prefix.'margin_top'];
	$margin_bottom = $ops[$prefix.'margin_bottom'];
	$padding_top = $ops[$prefix.'padding_top'];
	$padding_bottom = $ops[$prefix.'padding_bottom'];
	
	if (adinj_not_set($align)) $align = "";
	if (adinj_not_set($clear)) $clear = "";
	if (adinj_not_set($margin_top)) $margin_top = "";
	if (adinj_not_set($margin_bottom)) $margin_bottom = "";
	if (adinj_not_set($padding_top)) $padding_top = "";
	if (adinj_not_set($padding_bottom)) $padding_bottom = "";
	
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
	if (adinj_mfunc_mode()){
		// WP Super Cache's support for mclude assumes that we will be including
		// files from within ABSPATH. To remove this limitation we do the include
		// using mfunc instead.
		return adinj_ad_code_eval("\n
<!--mfunc include_once('$plugin_dir/adshow.php') -->
<?php include_once('$plugin_dir/adshow.php'); ?>
<!--/mfunc-->
");
	}
	return "";
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
function adinj_is_old_post($adtype){
	$ops = adinj_options();
	if ($adtype == 'widget'){
		$days = $ops['widgets_on_page_older_than'];
	} else {
		$days = $ops['ads_on_page_older_than'];
	}
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
	global $adinj_total_top_ads_used, $adinj_total_random_ads_used, $adinj_total_bottom_ads_used, $adinj_total_all_ads_used;
	$ops = adinj_options();
	$para = adinj_paragraph_to_start_ads();
	$random_mode = $ops['random_ads_after_mode'];
	if ($random_mode == 'after') $random_mode = 'at or after';
	$random_unit = $ops['random_ads_after_unit'];
	$mode = $ops['ad_insertion_mode'];
	
	$posttype = get_post_type() . ' (';
	if (is_archive()) $posttype .= ' archive';
	if (is_front_page()) $posttype .= ' front';
	if (is_home()) $posttype .= ' home';
	if (is_page()) $posttype .= ' page';
	if (is_search()) $posttype .= ' search';
	if (is_single()) $posttype .= ' single';
	if (is_404()) $posttype .= ' 404';
	$posttype .= ')';
	
	if(is_single() || is_page()) {
		$currentdate = time();
		$postdate = get_the_time('U');
		$currentday = $currentdate / 24 / 60 / 60;
		$postday = $postdate / 24 / 60 / 60;
	}
	return $content."
<!--
[ADINJ DEBUG]
$message
\$adinj_total_top_ads_used=$adinj_total_top_ads_used
\$adinj_total_random_ads_used=$adinj_total_random_ads_used
\$adinj_total_bottom_ads_used=$adinj_total_bottom_ads_used
\$adinj_total_all_ads_used=$adinj_total_all_ads_used
paragraphtostartads=$para (fyi: -1 is disabled)
random ads start $random_mode $random_unit $para (fyi: -1 is disabled)
posttype=$posttype
currentdate=$currentdate ($currentday)
postdate=$postdate ($postday)
injection mode=$mode
//-->\n";
}

function adinj_excluded_by_tick_box($prefix){
	if (is_front_page() && adinj_ticked($prefix.'exclude_front') ||
		is_home() && adinj_ticked($prefix.'exclude_home') ||
		is_search() && adinj_ticked($prefix.'exclude_search') ||
		is_404() && adinj_ticked($prefix.'exclude_404') ||
		is_page() && adinj_ticked($prefix.'exclude_page') ||
		is_single() && adinj_ticked($prefix.'exclude_single') ||
		is_archive() && adinj_ticked($prefix.'exclude_archive')){
		return true;
	}
	return false;
}

function adinj_ads_completely_disabled_from_page($adtype, $content=NULL){
	$ops = adinj_options();
	if ($ops['ads_enabled'] == 'off' ||
		$ops['ads_enabled'] == ''){
		return "NOADS: Ads are not switched on";
	}
	if ($ops['ads_enabled'] == 'test' && !current_user_can('activate_plugins')){
		return "NOADS: test mode enabled - ads only shown admins";
	}
	
	// check for ads on certain page types being disabled
	if (is_front_page() && adinj_ticked('exclude_front')){ return "NOADS: excluded from front page"; }
	if (is_home() && adinj_ticked('exclude_home')){ return "NOADS: excluded from home page"; }
	if (is_search() && adinj_ticked('exclude_search')){ return "NOADS: excluded from search pages"; }
	if (is_404() && adinj_ticked('exclude_404')){ return "NOADS: excluded from 404 pages"; }
	if ((is_page() && adinj_ticked('exclude_page')) ||
		(is_single() && adinj_ticked('exclude_single')) ||
		(is_archive() && adinj_ticked('exclude_archive'))){
		return "NOADS: excluded from this page type: ".get_post_type();
	}
	
	// if disable_adverts==1
	if (adinj_adverts_disabled_flag()) return "NOADS: adverts_disabled_flag";
	
	// no ads on old posts/pages if rule is enabled
	if((is_page() || is_single()) && !adinj_is_old_post($adtype)) return "NOADS: !is_old_post";
	
	if (!adinj_allowed_in_category('global', $ops)) return "NOADS: globally blocked from category";
	if (!adinj_allowed_in_tag('global', $ops)) return "NOADS: globally blocked from tag";	
	if (!adinj_allowed_in_author('global', $ops)) return "NOADS: globally blocked from author";
	if (!adinj_allowed_in_id('global', $ops)) return "NOADS: globally blocked from post/page id";
	
	// manual ad disabling tags
	if ($content == NULL) return false;
	if (stripos($content, "<!--noadsense-->") !== false) return "NOADS: noadsense tag"; // 'Adsense Injection' tag
	if (stripos($content, "<!-no-adsense-->") !== false) return "NOADS: no-adsense tag"; // 'Whydowork Adsense' tag
	if (stripos($content,'<!--NoAds-->') !== false) return "NOADS: NoAds tag"; // 'Quick Adsense' tag
	if (stripos($content,'<!--OffAds-->') !== false) return "NOADS: OffAds tag"; // 'Quick Adsense' tag
	
	return false;
}

function adinj_allowed_in_list($all_entries, $config_entries, $mode, $func){
	if (is_array($all_entries)){
		foreach($all_entries as $entry){
			$string = $func($entry);
			$decoded = rawurldecode($string); //allow UTF-8 encoded strings
			if (in_array($string, $config_entries) || in_array($decoded, $config_entries)){
				if (adinj_mode_only_show_in($mode)){
					return true;
				} else if (adinj_mode_never_show_in($mode)){
					return false;
				}
			}
		}
	}
	if (adinj_mode_only_show_in($mode)){
		return false;
	} else if (adinj_mode_never_show_in($mode)){
		return true;
	}
	echo ("<!--ADINJ DEBUG: error in adinj_allowed_in_list($func)-->");
	return true;
}

function adinj_mode_only_show_in($mode){
	return ($mode == ADINJ_ONLY_SHOW_IN || $mode == 'o');
}

function adinj_mode_never_show_in($mode){
	return ($mode == ADINJ_NEVER_SHOW_IN || $mode == 'n');
}

function adinj_allowed_in_category($scope, $ops){
	$conditions = adinj_split_comma_list($ops[$scope.'_category_condition_entries']);
	if (empty($conditions)) return true;
	
	$mode = $ops[$scope.'_category_condition_mode'];
	
	if (!in_the_loop() && adinj_mode_only_show_in($mode) && !(is_single() || is_category())){
		return false;
	}
	
	if (in_the_loop() && adinj_mode_only_show_in($mode) && !(is_single() || is_home() || is_category())){
		return false;
	}
	
	$categories = array();
	global $post;
	if (in_the_loop() && (is_single() || is_home())){
		$categories = get_the_category($post->ID);
	} else if (!in_the_loop() && is_single()){
		$cat_ids = wp_get_object_terms($post->ID, 'category', 'fields=all');
		foreach($cat_ids as $id){
			$categories[] = get_category($id);
		}
	} else if (is_category()){
		$categories[] = get_category(get_query_var('cat'));
	} // else cat array is empty
	return adinj_allowed_in_list($categories, $conditions, $mode, 'adinj_category_nicename');
}

function adinj_allowed_in_tag($scope, $ops){
	$conditions = adinj_split_comma_list($ops[$scope.'_tag_condition_entries']);
	if (empty($conditions)) return true;
	
	$mode = $ops[$scope.'_tag_condition_mode'];

	if (!in_the_loop() && adinj_mode_only_show_in($mode) && !(is_single() || is_tag())){
		return false;
	}

	if (in_the_loop() && adinj_mode_only_show_in($mode) && !(is_single() || is_home() || is_tag())){
		return false;
	}
	
	$tags = array();
	global $post;
	if (in_the_loop() && (is_single() || is_home())){
		$tags = get_the_tags($post->ID);
	} else if (!in_the_loop() && is_single()){
		$tag_ids = wp_get_object_terms($post->ID, 'post_tag', 'fields=all');
		foreach($tag_ids as $id){
			$tags[] = get_tag($id);
		}
	} else if (is_tag()){
		$tags[] = get_tag(get_query_var('tag_id'));
	} // else tag array is empty
	return adinj_allowed_in_list($tags, $conditions, $mode, 'adinj_tag_slug');
}

function adinj_allowed_in_author($scope, $ops){
	$conditions = adinj_split_comma_list($ops[$scope.'_author_condition_entries']);
	if (empty($conditions)) return true;
	
	$mode = $ops[$scope.'_author_condition_mode'];
	
	if (!in_the_loop() && adinj_mode_only_show_in($mode) && !(is_single() || is_page() || is_author())){
		return false;
	}
	
	if (in_the_loop()&& adinj_mode_only_show_in($mode) && !(is_single() || is_page() || is_home() || is_author())){
		return false;
	}
	
	$user = array();
	if (is_single() || is_page() || is_home()){
		$data = get_the_author_meta('user_login'); // works in and out of the loop
		$user[] = $data; //need to make it into array
	} else if (is_author()){
		$curauth = get_userdata(get_query_var('author'));
		$user[] = $curauth->user_login;
	} // else author array is empty
	
	return adinj_allowed_in_list($user, $conditions, $mode, 'adinj_author_data');
}

function adinj_allowed_in_id($scope, $ops){
	$conditions = adinj_split_comma_list($ops[$scope.'_id_condition_entries']);
	if (empty($conditions)) return true;
	
	if (!is_single() && !is_page()){
		return true;
	}
	
	$postid = -1;
	if (in_the_loop()){
		global $post;
		$postid = $post->ID;
	} else {
		global $wp_query;
		$postid = $wp_query->post->ID;
	}
	
	$mode = $ops[$scope.'_id_condition_mode'];
	return adinj_allowed_in_list(array($postid), $conditions, $mode, 'adinj_post_id');
}

//function parameters
function adinj_category_nicename($category){ return $category->category_nicename; }
function adinj_tag_slug($tag){ return $tag->slug; }
function adinj_author_data($data){ return $data; }
function adinj_post_id($data){ return $data; }

function adinj_split_comma_list($list){
	return preg_split("/[\s,]+/", $list, -1, PREG_SPLIT_NO_EMPTY);
}

function adinj_footer_hook(){
	if (is_feed()) return; // TODO feed specific ads
	if (adinj_num_footer_ads_to_insert() <= 0) return;
	echo adinj_ad_code_footer();
}

function adinj_content_hook($content){
	if (is_feed()) return $content; // TODO feed specific ads
	if (!in_the_loop()) return $content; // Don't insert ads into meta description tags TODOTODO
	$ops = adinj_options();
	if(empty($ops)){
		return $content;
	}
	$debug_on = $ops['debug_mode'];
	if ($debug_on) echo "<!--adinj-->"; //TODO remove?
	
	adinj_upgrade_db_if_necessary();
	
	global $adinj_total_all_ads_used, $adinj_total_random_ads_used, $adinj_total_top_ads_used, $adinj_total_bottom_ads_used;
	if(!is_archive() && (is_page() || is_single())){
		// On single page the_content may be called more than once - e.g. for
		// description meta tag and for content.
		$adinj_total_all_ads_used = 0;
		$adinj_total_top_ads_used = 0;
		$adinj_total_random_ads_used = 0;
		$adinj_total_bottom_ads_used = 0;
	}

	$reason = adinj_ads_completely_disabled_from_page('in-content', $content);
	if ($reason !== false){
		return adinj($content, $reason);
	}
	
	$debug_on = $ops['debug_mode'];
	$debug = "";
	
	if ($debug_on && adinj_direct_mode()){
		$showads = adshow_show_adverts();
		if ($showads !== true){
			$debug .= "\nNOADS: ad blocked at run time reason=$showads";
		}
	}

	$ad_include = "";
	if (adinj_mfunc_mode()){
		$ad_include = adinj_ad_code_include();
	}
	
	# Ad sandwich mode
	if(is_page() || is_single()){
		if(stripos($content, "<!--adsandwich-->") !== false) return adinj($ad_include.adinj_ad_code_top().$content.adinj_ad_code_bottom(), "Ads=adsandwich");
		if(stripos($content, "<!--adfooter-->") !== false) return adinj($content.$ad_include.adinj_ad_code_bottom(), "Ads=adfooter");
	}
	
	$length = 0;
	if ($ops['content_length_unit'] == 'all'){
		$length = strlen($content);
		if ($debug_on) $debug .= "\nnum chars: $length (including HTML chars)";
	} else if ($ops['content_length_unit'] == 'viewable'){
		$length = strlen(strip_tags($content));
		if ($debug_on) $debug .= "\nnum chars: $length (viewable chars only)";
	} else {
		$length = str_word_count_utf8(strip_tags($content));
		if ($debug_on) $debug .= "\nnum words: $length";
	}
	# Insert top and bottom ads if necesary
	if(stripos($content, "<!--topad-->") == false){
		if (adinj_num_top_ads_to_insert($length) > 0){
			$content = $ad_include.adinj_ad_code_top().$content;
			$ad_include = "";
			++$adinj_total_top_ads_used;
		}
	} else {
		if ($debug_on) $debug .= "\ntop ad position(s) fixed by 'topad' tag";
		$content = str_replace('<!--topad-->', adinj_ad_code_top(), $content);
		$content = $ad_include.$content;
		$ad_include = "";
		++$adinj_total_top_ads_used;
	}
	
	if(stripos($content, "<!--bottomad-->") == false){
		if (adinj_num_bottom_ads_to_insert($length) > 0){
			$content = $content.adinj_ad_code_bottom();
			++$adinj_total_bottom_ads_used;
		} 
	} else {
		if ($debug_on) $debug .= "\nbottom ad position(s) fixed by 'bottomad' tag";
		$content = str_replace('<!--bottomad-->', adinj_ad_code_bottom(), $content);
		++$adinj_total_bottom_ads_used;
	}

	if ($ad_include !== "") $content = $ad_include.$content;
	
	$num_rand_ads_to_insert = adinj_num_rand_ads_to_insert($length);
	if ($num_rand_ads_to_insert <= 0) return adinj($content, "no random ads on this post");
	$ad = adinj_ad_code_random();
	if (empty($ad)) return adinj($content, "no random ad defined");
	
	if(stripos($content, "<!--randomad-->") !== false){
		if ($debug_on) $debug .= "\nrandom ad position(s) fixed by 'randomad' tag";
		$content = str_replace('<!--randomad-->', $ad, $content);
		return adinj($content, "Fixed random ads" . $debug);
	}
	
	if (!$debug_on) $debugtags=false;
	
	$adstart_override = false;
	$content_adfree_header = "";
	$content_adfree_footer = "";
	
	// TODO add docs explaining the significance of leaving blank lines
	// before or after these tags
	# 'Adsense Injection' tag compatibility
	$split = adinj_split_by_tag($content, "<!--adsensestart-->", $debugtags);
	if (count($split) == 2){
		$content_adfree_header = $split[0];
		$content = $split[1];
		$adstart_override = true;
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
		$adstart_override = true;
	}
	
	$split = adinj_split_by_tag($content, "<!--adend-->", $debugtags);
	if (count($split) == 2){
		$content = $split[0];
		$content_adfree_footer = $split[1];
	}

	if ($debug_on) $debug .= "\nContent length=". strlen($content);
	
	$startposition = adinj_paragraph_to_start_ads();
	
	if (!$adstart_override){
		if ($ops['random_ads_after_unit'] == 'character' && $startposition > 0){
			$split = adinj_split_by_index($content, $startposition);
			if (count($split) == 2){
				$content_adfree_header = $split[0];
				$content = $split[1];
				if ($ops['random_ads_after_mode'] == 'at'){
					// Start ads at next paragraph
					$startposition = 1;
				} else {
					$startposition = -1;
				}
			} else {
				return adinj($content, "No random ads: content too short for 'start first ad at/after character $startposition' setting");
			}
		}
	}
	
	// TODO add note explaining that start tags are processed before the 'first
	// paragraph ad
	
	// Move onto random ad insertions
	$paragraphmarker = "</p>";
	if(stripos($content, $paragraphmarker) === false) return adinj($content, "no &lt;/p&gt; tags");
	
	if ($debug_on) $debug .= "\nTags=". htmlentities($debugtags);
	
	// Generate a list of all potential injection points
	if ($debug_on) $debug .= "\nInitial potential positions: ";
	$potential_inj_positions = array();
	$prevpos = -1;
	while(($prevpos = stripos($content, $paragraphmarker, $prevpos+1)) !== false){
		$potentialposition = $prevpos + strlen($paragraphmarker);
		$potential_inj_positions[] = $potentialposition;
		if ($debug_on) $debug .= "$potentialposition, ";
	}

	if ($debug_on) $debug .= "\npotential_inj_positions:".sizeof($potential_inj_positions);
	
	if (sizeof($potential_inj_positions) == 0){
		return adinj($content, "No random ads: no potential inj positions found in content");
	}
	
	if (!adinj_ticked('rnd_allow_ads_on_last_paragraph')){
		array_pop($potential_inj_positions);
		if (sizeof($potential_inj_positions) == 0){
			return adinj($content, "No random ads: no potential inj positions after removing last paragraph position");
		}
	}
	
	$inj_positions = array();
	
	if ($startposition > 0){
		$pos = NULL;
		if ($ops['random_ads_after_mode'] == 'at'){
			for ($i=0; $i<$startposition; ++$i){
				// discard positions until we get to the starting paragraph
				$pos = array_shift($potential_inj_positions);
			}
			if ($pos != NULL && $ops['random_ads_after_mode'] == 'at'){
				$inj_positions[] = $pos;
				--$num_rand_ads_to_insert;
			}
		} else { // mode == at or after
			for ($i=0; $i<$startposition-1; ++$i){
				// discard positions before the starting paragraph
				array_shift($potential_inj_positions);
			}
		}
		if (sizeof($potential_inj_positions) == 0){
			return adinj($content, "No random ads: no potential inj positions left after applying 'start ads at/after paragraph' $startposition setting");
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
		return adinj($content_adfree_header.$content.$content_adfree_footer, "Error: No random ad injection positions: " . $debug);
	}
	foreach($inj_positions as $pos){
		if ($debug_on) $debug = "$pos, $debug";
	}
	
	// Sort positions
	sort($inj_positions);
	
	// Insert ads in reverse order
	global $adinj_total_random_ads_used, $adinj_total_all_ads_used;
	for ($adnum=sizeof($inj_positions)-1; $adnum>=0; $adnum--){
		$content = substr_replace($content, $ad, $inj_positions[$adnum], 0);
		++$adinj_total_random_ads_used;
		++$adinj_total_all_ads_used;

		if (adinj_ticked('rnd_reselect_ad_per_position_in_post')){
			$ad = adinj_ad_code_random();
		}
	}

	return adinj($content_adfree_header.$content.$content_adfree_footer, "Ads injected: " . $debug);
}

//http://php.net/manual/en/function.str-word-count.php
define("WORD_COUNT_MASK", "/\p{L}[\p{L}\p{Mn}\p{Pd}'\x{2019}]*/u");
function str_word_count_utf8($str){
	if (@preg_match('/\pL/u', 'a') == 1) { // check if utf8 support is compile in
		return preg_match_all(WORD_COUNT_MASK, $str, $matches);
	} else {
		return str_word_count($str);
	}
}

function adinj_paragraph_to_start_ads(){
	$ops = adinj_options();
	if (adinj_db_version($ops) == 1){
		if (adinj_ticked('first_paragraph_ad')) return 1;
		return -1;
	}
	if (adinj_rule_disabled('start_from_paragraph')){
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

function adinj_split_by_index($content, $index){
	$ret = array();
	if (strlen($content) > $index+4){ // +4 as there is no point splitting unless there is room for a </p> at end
		$ret[0] = substr($content, 0, $index);
		$ret[1] = substr($content, $index+1);
	}
	return $ret;
}

function adinj_get_current_page_type_prefix(){
	if (is_home()) return 'home_';
	if (is_archive()) return 'archive_';
	return '';
}

function adinj_num_top_ads_to_insert($content_length){
	if (adinj_excluded_by_tick_box('top_')) return 0;
	$ops = adinj_options();
	$prefix = adinj_get_current_page_type_prefix();
	$max_num_ads_to_insert = 0;
	if (is_single() || is_page()){
		$max_num_ads_to_insert = 1;
	} else {
		global $adinj_total_top_ads_used;
		$max_num_ads_to_insert = $ops[$prefix.'max_num_top_ads_per_page'] - $adinj_total_top_ads_used;
	}
	if ($max_num_ads_to_insert <= 0) return 0;

	if (!adinj_allowed_in_category('top', $ops)) return 0;
	if (!adinj_allowed_in_tag('top', $ops)) return 0;
	if (!adinj_allowed_in_author('top', $ops)) return 0;
	if (!adinj_allowed_in_id('top', $ops)) return 0;
	
	$val = $ops[$prefix.'top_ad_if_longer_than'];
	if (adinj_not_set($val) || adinj_true_if($content_length, '>', $val)){
		return 1;
	}
	return 0;
}

function adinj_num_bottom_ads_to_insert($content_length){
	if (adinj_excluded_by_tick_box('bottom_')) return 0;
	$ops = adinj_options();
	$prefix = adinj_get_current_page_type_prefix();
	$max_num_ads_to_insert = 0;
	if (is_single() || is_page()){
		$max_num_ads_to_insert = 1;
	} else {
		global $adinj_total_bottom_ads_used;
		$max_num_ads_to_insert = $ops[$prefix.'max_num_bottom_ads_per_page'] - $adinj_total_bottom_ads_used;
	}
	if ($max_num_ads_to_insert <= 0) return 0;
	
	if (!adinj_allowed_in_category('bottom', $ops)) return 0;
	if (!adinj_allowed_in_tag('bottom', $ops)) return 0;
	if (!adinj_allowed_in_author('bottom', $ops)) return 0;
	if (!adinj_allowed_in_id('bottom', $ops)) return 0;
	
	$val = $ops[$prefix.'bottom_ad_if_longer_than'];
	if (adinj_not_set($val) || adinj_true_if($content_length, '>', $val)){
		return 1;
	}
	return 0;
}

function adinj_num_rand_ads_to_insert($content_length){
	if (adinj_excluded_by_tick_box('random_')) return 0;
	global $adinj_total_random_ads_used; // a page can be more than one post
	$ops = adinj_options();
	$max_ads_in_post = 0;
	$prefix = adinj_get_current_page_type_prefix();
	if (is_single() || is_page()){
		$max_num_rand_ads_to_insert = $ops['max_num_of_ads'] - $adinj_total_random_ads_used;
		$max_ads_in_post = $max_num_rand_ads_to_insert;
	} else if (is_home() || is_archive()){
		$max_num_rand_ads_to_insert = $ops[$prefix.'max_num_random_ads_per_page'] - $adinj_total_random_ads_used;
		$max_ads_in_post = $ops[$prefix.'max_num_random_ads_per_post'];
	} else {
		return 0;
	}
	
	$max_num_rand_ads_to_insert = min($max_num_rand_ads_to_insert, $max_ads_in_post);

	if ($max_num_rand_ads_to_insert <= 0) {
		return 0;
	}
	
	if (!adinj_allowed_in_category('random', $ops)) return 0;
	if (!adinj_allowed_in_tag('random', $ops)) return 0;
	if (!adinj_allowed_in_author('random', $ops)) return 0;
	if (!adinj_allowed_in_id('random', $ops)) return 0;
	$length = $content_length;
	if (adinj_true_if($length, '<', $ops[$prefix.'no_random_ads_if_shorter_than'])){
		return 0;
	}
	if (adinj_true_if($length, '<', $ops[$prefix.'one_ad_if_shorter_than'])){
		return 1;
	}
	if (adinj_true_if($length, '<', $ops[$prefix.'two_ads_if_shorter_than'])){
		return min(2, $max_num_rand_ads_to_insert);
	}
	if (adinj_true_if($length, '<', $ops[$prefix.'three_ads_if_shorter_than'])){
		return min(3, $max_num_rand_ads_to_insert);
	}
	return $max_num_rand_ads_to_insert;
}

function adinj_num_footer_ads_to_insert(){
	if (adinj_excluded_by_tick_box('footer_')) return 0;
	$reason = adinj_ads_completely_disabled_from_page('footer', $content);
	if ($reason !== false){
		return 0;
	}
	$ops = adinj_options();
	if (!adinj_allowed_in_category('footer', $ops)) return 0;
	if (!adinj_allowed_in_tag('footer', $ops)) return 0;
	if (!adinj_allowed_in_author('footer', $ops)) return 0;
	if (!adinj_allowed_in_id('footer', $ops)) return 0;
	return 1;
}

function adinj_true_if($rule_value, $condition, $content_length){
	if (adinj_alwaysshow($rule_value)) return true; // todo deprecated
	if ($condition == '>'){
		return ($rule_value >= $content_length);
	} else if ($condition == '<'){
		return ($rule_value <= $content_length);
	} else {
		die("adinj_true_if bad condition: $condition");
	}
}

function adinj_direct_mode(){
	$ops = adinj_options();
	return ($ops['ad_insertion_mode'] != 'mfunc');
}

function adinj_mfunc_mode(){
	$ops = adinj_options();
	return ($ops['ad_insertion_mode'] == 'mfunc');
}

function adinj_alwaysshow($value){ // todo deprecated?
	return "$value" == ADINJ_ALWAYS_SHOW || "$value" == 'a';
}

function adinj_rule_disabled($value){
	return "$value" == ADINJ_RULE_DISABLED || "$value" == ADINJ_DISABLED || "$value" == 'd' || "$value" == '';
}

function adinj_not_set($value){
	return adinj_rule_disabled($value);
}

function adinj_ticked($option, $ops=array()){
	if (empty($ops)) $ops = adinj_options();
	if (!empty($ops[$option]) && $ops[$option] != 'off') return 'checked="checked"';
	return false;
}

function adinj_upgrade_db_if_necessary(){
	$cached_options = adinj_options();
	if(empty($cached_options)){
		// 1st Install.
		require_once(ADINJ_PATH . '/ad-injection-admin.php');
		adinj_install_db();
		return;
	}

	$cached_dbversion = adinj_db_version($cached_options);
	
	if (ADINJ_DB_VERSION != $cached_dbversion){
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

// template ads
function adinj_print_ad($adname=''){
	$reason = adinj_ads_completely_disabled_from_page('template', "");
	if ($reason !== false){
		return;
	}
	if (adinj_excluded_by_tick_box('template_')) return;
	if ($adname == 'random'){
		echo adinj_ad_code_random();
	} else if ($adname == 'top'){
		echo adinj_ad_code_top();
	} else if ($adname == 'bottom'){
		echo adinj_ad_code_bottom();
	} else if ($adname == 'footer'){
		echo adinj_ad_code_footer();
	} else if (preg_match("/.+\.txt/i", $adname)){
		adshow_display_ad_file_v2($adname);
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
add_filter('the_content', 'adinj_content_hook'); // TODO allow priority to be changed? e.g. TheTravelTheme is setting its formatting at priority 99
add_action('wp_footer', 'adinj_footer_hook');
add_action('wp_footer', 'adinj_print_referrers_hook');

?>