<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

if (!is_admin()) return;

function adinj_tab_main(){
	$ops = adinj_options();
	?>
	<script type="text/javascript">
	function adinj_addtext(element, value) {
		if (value.length == 0) return;
		separator = ', ';
		if (element.value.length == 0){
			separator = '';
		}
		element.value += (separator + value);
	}
	</script>
	
	<p><a href="#random">Random ads</a> | <a href="#topad">Top</a> | <a href="#bottomad">Bottom</a> | <a href="#widgets">Widgets</a> | <a href="#restrictions">Ad insert mode/dynamic restrictions</a> | <a href="#debugging">Debug</a> | <a href="#docs">Quick Start</a> | <a href="#testads">Test ads</a></p>
	
	
	
	
	<?php adinj_postbox_start(__("Global settings", 'adinj'), 'global'); ?>
	
	<p>These settings apply to all ads (random, top, bottom, and widget). They will override all other settings.</p>
	
	<input type="radio" name="ads_enabled" value="on" <?php if ($ops['ads_enabled']=='on') echo 'checked="checked"'; ?> /> <b>On: <?php _e('Ads enabled', 'adinj') ?></b><br />
	<input type="radio" name="ads_enabled" value="off" <?php if ($ops['ads_enabled']=='off' || $ops['ads_enabled']=='') echo 'checked="checked"'; ?> /> <b>Off</b><br />
	<input type="radio" name="ads_enabled" value="test" <?php if ($ops['ads_enabled']=='test') echo 'checked="checked"'; ?> /> <b>Test mode</b> - Only show ads to admin.<br />
	<span style="font-size:10px;color:red;">Warning: Turn any caching plugin *off* before using test mode. If you leave them on the test adverts will be cached and shown to your real visitors.</span><br />

	<table border="0">
	<tr><td>
	<p><?php _e("Only show ads on pages older than ", 'adinj') ?></p>
	</td><td>
	<p>
	<select name='ads_on_page_older_than'>
	<?php
	$older_than_days = array(0, 1, 2, 3, 5, 7, 10, 14, 21, 28, 40, 50);
	for ($value=0; $value<sizeof($older_than_days); ++$value){
		echo "<option value=\"$older_than_days[$value]\" ";
		if($ops['ads_on_page_older_than'] == $older_than_days[$value]) echo 'selected="selected"';
		echo ">$older_than_days[$value]</option>";
	}
	?>
	</select><?php _e(" (days)", 'adinj') ?> - only for single posts and pages</p>
	</td></tr>
	<tr><td style="vertical-align:top">
	Don't show ads on these types:
	</td><td>
	<?php adinj_add_checkbox('exclude_front') ?>front - <?php echo get_bloginfo('url'); ?><br />
	<?php adinj_add_checkbox('exclude_home') ?>home - latest posts page (may be same as front)<br />
	<?php
	$count_pages = wp_count_posts('page', 'readable'); 
	$count_posts = wp_count_posts('post', 'readable'); 
	?>
	<?php adinj_add_checkbox('exclude_page') ?>page - <?php echo $count_pages->publish; ?> page(s)<br />
	<?php adinj_add_checkbox('exclude_single') ?>single -<?php echo $count_posts->publish; ?> single blog post(s)<br />
	<?php adinj_add_checkbox('exclude_archive') ?>archive - only <a href="#widgets">widgets</a> currently appear on archives<br />
	</td></tr>
	<tr><td colspan="2"><p><span style="font-size:10px;">If you have <a href='options-reading.php'>set your front page</a> to be a static 'page' rather than your latest posts, the 'page' tick box will also apply to the front page.</span></p></td></tr>
	</table>
	
	<b>Category and tag conditions</b>
	
	<?php adinj_condition_table('global_category', 'category slugs. e.g: cat1, cat2, cat3', 'category'); ?>
	
	<?php adinj_condition_table('global_tag', 'tag slugs. e.g: tag1, tag2, tag3', 'tag'); ?>
	
	<?php adinj_postbox_end(); ?>
	

	<?php adinj_postbox_start(__("Randomly Injected ad code", 'adinj'), 'random'); ?>
	
	<table border="0" cellspacing="5">
	<tr><td style="vertical-align: top">
	<textarea name="ad_code_random_1" rows="10" cols="60"><?php echo $ops['ad_code_random_1']; ?></textarea>
	</td><td style="vertical-align: top">
	<?php
	adinj_add_alignment_options('rnd_'); 
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_random'>Rotation:<br />".adinj_percentage_split('ad_code_random_', 1, $ops)."</a></b>";
	
	?>
	</td></tr>
	<tr><td>
	<?php
		_e("Always put the first ad immediately after paragraph: ", 'adinj');
		adinj_selection_box("start_from_paragraph",
			array(ADINJ_DISABLED, 1, 2, 3, 4, 5), " ");
	?>
	
	</td><td>
	</td></tr>
	</table>

	
	<p><span style="font-size:10px;"><b>Docs:</b> On single posts or pages this advert is inserted between randomly selected paragraphs. On 'your latest posts' page, one ad is inserted into each post. Try a <a href="#468x60">468x60</a> or <a href="#728x90">728x90</a> banner.</span></p>
	<p><span style="font-size:10px;">Be especially careful if you decide to use the 'float' layout options. Make sure that you don't have adverts floated over the top of other page elements, or vice-versa.</span></p>
	</div>
	
	<input type="submit" style="float:right" name="adinj_action" value="<?php _e('Save all settings', 'adinj') ?>" />
	<h3><a name="random_single"></a><?php _e("Single posts and pages: Randomly Injected ad settings", 'adinj') ?></h3>
	<div class="inside" style="margin:10px">
	<p>These random ad injection settings are specific to single posts and pages.</p>
	
	<table border="0">
	
	<tr><td>
	<?php _e("Allow multiple ads to be injected at the same positions.", 'adinj') ?>
	</td><td>
	<?php adinj_add_checkbox('multiple_ads_at_same_position') ?> (default is to inject ads at unique positions)
	</td></tr>

	
	<tr><td>
	<?php _e("Maximum number of randomly injected ads: ", 'adinj') ?></td><td>
	<select name='max_num_of_ads'>
	<?php
	for ($value=0; $value<=10; ++$value){
		echo "<option value=\"$value\" ";
		if($ops['max_num_of_ads'] == $value) echo 'selected="selected"';
		echo ">$value</option>";
	}
	?>
	</select> <?php echo adinj_getdefault('max_num_of_ads'); ?><br />
	</td></tr>
	
	<tr><td>
	<?php
		_e("No random ads if page shorter than: ", 'adinj');
		echo '</td><td>';
		adinj_selection_box("no_random_ads_if_shorter_than",
			array(ADINJ_RULE_DISABLED, 100, 200, 300, 500, 1000, 1500, 2000, 2500, 3000));
			echo adinj_getdefault('no_random_ads_if_shorter_than');
	?>
	</td></tr>
	
	<tr><td>
	<?php
		_e("Limit of 1 ad if page shorter than: ", 'adinj');
		echo '</td><td>';
		adinj_selection_box("one_ad_if_shorter_than",
			array(ADINJ_RULE_DISABLED, 100, 200, 300, 500, 1000, 1500, 2000, 2500, 3000));
			echo adinj_getdefault('one_ad_if_shorter_than');
	?>
	</td></tr>
	
	<tr><td>
	<?php
		_e("Limit of 2 ads if page shorter than: ", 'adinj');
		echo '</td><td>';
		adinj_selection_box("two_ads_if_shorter_than",
			array(ADINJ_RULE_DISABLED, 100, 200, 300, 500, 1000, 1500, 2000, 2500, 3000, 5000, 10000));
			echo adinj_getdefault('two_ads_if_shorter_than');
	?>
	</td></tr>

	<tr><td>
	<?php
		_e("Limit of 3 ads if page shorter than: ", 'adinj');
		echo '</td><td>';
		adinj_selection_box("three_ads_if_shorter_than",
			array(ADINJ_RULE_DISABLED, 100, 200, 300, 500, 1000, 1500, 2000, 2500, 3000, 5000, 10000, 20000));
			echo adinj_getdefault('three_ads_if_shorter_than');
	?>
	</td></tr>
	</table>
	
	<br clear="all" />
	<p><span style="font-size:10px;"><b>Docs:</b> The above directives are processed in order from top to bottom.</span></p>
	

	<p></p>
	
	</div>
	
	<input type="submit" style="float:right" name="adinj_action" value="<?php _e('Save all settings', 'adinj') ?>" />
	<h3><a name="random_home"></a><?php _e("Home page: Randomly Injected ad settings", 'adinj') ?></h3>
	<div class="inside" style="margin:10px">
	<p>These random ad injection settings are specific to your home page. The home page is the page containing your latest posts. It may not be the same as your front page if you have set a static page to be your front page in the <a href='options-reading.php'>reading settings</a>.</p>
	
	<?php _e("Maximum number of injected ads: ", 'adinj') ?>
	<select name='max_num_of_ads_home_page'>
	<?php
	for ($value=0; $value<=10; ++$value){
		echo "<option value=\"$value\" ";
		if($ops['max_num_of_ads_home_page'] == $value) echo 'selected="selected"';
		echo ">$value</option>";
	}
	?>
	</select> <?php echo adinj_getdefault('max_num_of_ads_home_page'); ?><br />
	
	<p><span style="font-size:10px;"><b>Docs:</b> On a 'your latest posts' page, one randomly positioned advert will be inserted into each post, up to the maximum number specified here.</span></p>
	
	<?php adinj_postbox_end(); ?>
	
	
	
	<?php adinj_postbox_start(__("Optional top advert (single posts and pages only)", 'adinj'), 'topad'); ?>
	
	<?php
		_e("Only show top ad on pages longer than: ", 'adinj');
		adinj_selection_box("top_ad_if_longer_than",
			array(ADINJ_DISABLED, ADINJ_ALWAYS_SHOW, 100, 200, 300, 500, 1000, 1500, 2000, 2500, 3000, 5000, 10000, 20000));
			
	?>

	<br clear="all" />
	
	<table border="0">
	<tr><td>
	<textarea name="ad_code_top_1" rows="10" cols="60"><?php echo $ops['ad_code_top_1']; ?></textarea>
	</td><td style="vertical-align: top">
	<?php
	adinj_add_alignment_options('top_');
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_top'>Rotation:<br />".adinj_percentage_split('ad_code_top_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	<span style="font-size:10px;">The top ad is in addition to the quantity of other ads selected.</span>
	
	<p><span style="font-size:10px;"><b>Docs:</b> The top ad will only appear on single posts and pages. It will not appear on multi-post pages. Try a <a href="#468x15">468x15</a> or <a href="#336x280">336x280</a> advert.</span></p>
	
	<?php adinj_postbox_end(); ?>
	
	
	<?php adinj_postbox_start(__("Optional bottom advert (single posts and pages only)", 'adinj'), 'bottomad'); ?>

	<?php
		_e("Only show bottom ad on pages longer than: ", 'adinj');
		adinj_selection_box("bottom_ad_if_longer_than",
			array(ADINJ_DISABLED, ADINJ_ALWAYS_SHOW, 100, 200, 300, 500, 1000, 1500, 2000, 2500, 3000, 5000, 10000, 20000));
	?>
	
	<br clear="all"/>
	
	<table border="0">
	<tr><td>
	<textarea name="ad_code_bottom_1" rows="10" cols="60"><?php echo $ops['ad_code_bottom_1']; ?></textarea>
	</td><td style="vertical-align: top">
	<?php 
	adinj_add_alignment_options('bottom_');
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_bottom'>Rotation:<br />".adinj_percentage_split('ad_code_bottom_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	<span style="font-size:10px;">The top ad is in addition to the quantity of other ads selected.</span>
	
	<p><span style="font-size:10px;"><b>Docs:</b> The bottom ad will only appear on single posts and pages. It will not appear on multi-post pages. Try a <a href="#336x280">336x280</a> advert.</span></p>
	
	
	<?php adinj_postbox_end(); ?>
	
	
	<?php adinj_postbox_start(__("Widget settings (sidebar ads)", 'adinj'), 'widgets'); ?>
	
	<p>You must configure your individual widgets from the <a href="widgets.php">widgets control panel</a>. However these settings are global to all widgets. Also note that the main set of <a href="#global">global settings</a> will override these ones.</p>

	<table border="0">
	
	<tr><td>
	<p>Don't show widget ads on these types:</p>
	</td><td>
	<?php adinj_add_checkbox('widget_exclude_front') ?>front - <?php echo get_bloginfo('url'); ?><br />
	<?php adinj_add_checkbox('widget_exclude_home') ?>home - latest posts page (may be same as front)<br />
	<?php adinj_add_checkbox('widget_exclude_page') ?>page - <?php echo $count_pages->publish; ?> page(s)<br />
	<?php adinj_add_checkbox('widget_exclude_single') ?>single - <?php echo $count_posts->publish; ?> single blog post(s)<br />
	<?php adinj_add_checkbox('widget_exclude_archive') ?>archive - includes category, tag, author, and date pages types<br />
	</td></tr>
	<tr><td colspan="2"><p><span style="font-size:10px;">If you have <a href='options-reading.php'>set your front page</a> to be a static 'page' rather than your latest posts, the 'page' tick box will also apply to the front page.</span></p></td></tr>
	
	</table>
	
	
	<?php adinj_postbox_end(); ?>
	
	
	<?php adinj_postbox_start(__("Ad insertion mode and dynamic ad display restrictions", 'adinj'), 'restrictions'); ?>
	
	<h4>Ad insertion mode</h4>
	
	<blockquote>
	<p><input type="radio" name="ad_insertion_mode" value="mfunc" <?php if ($ops['ad_insertion_mode']=='mfunc') echo 'checked="checked"'; ?> /> <b>mfunc: Use mfunc tags for dynamic features</b> - Dynamic features will work with WP Super Cache, W3 Total Cache and WP Cache. Only select this mode if you are using one of those caching plugins.</p>
	
	<?php if (!is_supported_caching_plugin_active()) {
		echo '<p><b><span style="font-size:10px;color:red;">Note: A supported caching plugin does not appear to be active. If you are not using WP Super Cache / W3 Total Cache / WP Cache you should use one of the direct insertion modes below.</span></b></p>';		
	} ?>
	
	<?php if ($ops['ad_insertion_mode'] != 'mfunc') { ?>
	<script type="text/javascript">
	document.write('<style type="text/css" media="screen">#caching_plugin_msg { display: none; }</style>');
	</script>
	<?php }  ?>

	<div id="caching_plugin_msg" class="caching_plugin_msg">
	<?php
	if (is_plugin_active('wp-super-cache/wp-cache.php')){
		adinj_wp_super_cache_msg();
	} else if (is_plugin_active('w3-total-cache/w3-total-cache.php')){
		adinj_w3_total_cache_msg();
	} else if (is_plugin_active('wp-cache/wp-cache.php')){
		adinj_wp_cache_msg();
	}
	adinj_unknown_cache_msg();
	?>
	
	</div>
	
	<p><input type="radio" name="ad_insertion_mode" value="direct_dynamic" <?php if ($ops['ad_insertion_mode']=='direct_dynamic') echo 'checked="checked"'; ?> /> <b>direct_dynamic: Direct ad insertion with dynamic features</b> - Dynamic features will work if no caching is used. Only select this if you are not using any caching plugin.</p>
	<p><input type="radio" name="ad_insertion_mode" value="direct_static" <?php if ($ops['ad_insertion_mode']=='direct_static') echo 'checked="checked"'; ?> /> <b>direct_static: Direct static ad insertion</b> - No dynamic feature support. Select this if you are are not using dynamic features or are using an incompatible caching plugin.</p>
	</blockquote>
	</div>
	<p></p>
	
	<script type="text/javascript">
	jQuery(document).ready(function(){
	jQuery('input[name=ad_insertion_mode]:radio').change(function() {
		if (jQuery('input[name=ad_insertion_mode]:checked').val() == "direct_static"){
			jQuery('.dynamic_features').slideUp(1000);
			jQuery('.dynamic_features_msg').slideDown(1000);
			jQuery('.caching_plugin_msg').slideUp(1000);
		} else if (jQuery('input[name=ad_insertion_mode]:checked').val() == "direct_dynamic"){
			jQuery('.dynamic_features_msg').slideUp(1000);
			jQuery('.dynamic_features').slideDown(1000);
			jQuery('.caching_plugin_msg').slideUp(1000);
		} else { // mfunc
			jQuery('.dynamic_features_msg').slideUp(1000);
			jQuery('.dynamic_features').slideDown(1000);
			jQuery('.caching_plugin_msg').slideDown(1000);
		}
		return true;
		});
	});
	</script>
	
	<?php if ($ops['ad_insertion_mode'] == 'direct_static') { ?>
	<div class="dynamic_features_msg">
	<?php } else { ?>
	<div class="dynamic_features_msg" style="display:none">
	<?php } ?>
	<div class="inside" style="margin:10px">
	<blockquote><b><span style="font-size:10px;color:red;">Note: Dynamic ad blocking features (restricting ad views by referrer or IP address) are only available in the mfunc, or direct_dynamic modes.</span></b>
	</blockquote>
	</div>
	</div>
	
	<?php if ($ops['ad_insertion_mode'] == 'direct_static') { ?>
	<script type="text/javascript">
	document.write('<style type="text/css" media="screen">#dynamic_features { display: none; }</style>');
	</script>
	<?php } ?>
	<div id="dynamic_features" class="dynamic_features">
	
	<div class="inside" style="margin:10px">

	<h4><a name="dynamic"></a>Show ads only to search engine visitors (dynamic feature)</h4>
	
	<blockquote>
	<?php adinj_add_checkbox('sevisitors_only') ?><?php _e("Only show ads to search engine visitors (customise search engine referrers below if necessary).", 'adinj') ?><br />
	<textarea name="ad_referrers" rows="2" cols="70"><?php echo $ops['ad_referrers']; ?></textarea>
	<p>Comma separated list e.g.: <br /><code>.google., .bing., .yahoo., .ask., search?, search., /search/</code></p>
	</blockquote>
	
	<h4>Blocked IP addresses (dynamic feature)</h4>
	
	<blockquote>
	<!--<?php adinj_add_checkbox('block_ips') ?><?php _e("Exclude ads from these IP addresses.", 'adinj') ?><br />-->
	<textarea name="blocked_ips" rows="4" cols="70"><?php echo $ops['blocked_ips']; ?></textarea>
	<p>Comma separated list e.g.: <br /><code>0.0.0.1, 0.0.0.2</code></p>
	<p>Or you can list one IP per line with optional comments e.g.</p>
	<code style="padding:0px 0px">192.168.0.1<br />0.0.0.2<br /><?php echo $_SERVER['REMOTE_ADDR'] ?> //my ip<br />0.0.0.3</code>
	
	<p>For reference your current IP address is <code><?php echo $_SERVER['REMOTE_ADDR'] ?></code></p>
	</blockquote>
	</div>
	
	</div>
	
	<?php adinj_postbox_end(); ?>
	
	
	<?php adinj_postbox_start(__("Debugging", 'adinj'), 'debugging'); ?>
	
	<?php adinj_add_checkbox('debug_mode') ?>Enable debug mode
	
	<p>If you are not sure why ads aren't appearing, or why they are appearing, enable debug mode and look at the debug information (search for 'ADINJ DEBUG') in the HTML of your content pages.</p>
	
	<?php
	
	if (adinj_problem_with_wpminify_check()){
		echo adinj_get_problem_with_wpminify_message();
	}
	
	adinj_debug_information();
	?>
	
	<p></p>
	
	<p>If you want to restore all settings (excluding the ad contents) to their default values use this button.</p>
	
	<input type="submit" name="adinj_action" value="<?php _e('Reset to Default', 'adinj') ?>" />
	
	<p>You can delete the database settings if you are going to uninstall Ad Injection (they will be automatically deleted if you uninstall via WordPress as well).</p>
	
	<input type="submit" name="adinj_action" value="<?php _e('Delete settings from DB', 'adinj') ?>" />
	
	<p>This button will delete all your Ad Injection widgets.</p>
	
	<input type="submit" name="adinj_action" value="<?php _e('Delete widget settings from DB', 'adinj') ?>" />
	
	<?php adinj_postbox_end(); ?>


	<br clear="all" />
	
	<?php
	
	adinj_docs();
}

function adinj_wp_super_cache_msg(){
	?>
	<p>With WP Super Cache version 0.9.9.8+ you can use the fastest 'mod rewrite rules' caching mode. With older versions of WP Super Cache you'll have to use the slower 'legacy mode'.</p>
	<p>Go to the 
	<?php if (is_plugin_active('wp-super-cache/wp-cache.php')) { ?>
	<a href='options-general.php?page=wpsupercache&amp;tab=settings'>WP Super Cache advanced options page</a>
	<?php } else { ?>
	WP Super Cache advanced options page
	<?php }  ?>
	to configure the caching mode.</p>
	<?php
}

function adinj_w3_total_cache_msg(){
	?>
	<p>W3 Total Cache will cache the the page on-disk if you use its Page Cache: 'Disk (basic)' mode. However if you use its Page Cache: Disk (enhanced) mode it won't cache the page. If you aren't using Ad Injection's dynamic features then you can use W3 Total Cache with Page Cache: Disk (enhanced) mode.</p>
	<?php
}

function adinj_wp_cache_msg(){
	?>
	<p>With WP Cache just turn the caching on and all pages will be cached. You may however want to consider upgrading to WP Super Cache as it has more efficient caching options such as serving static files via mod rewrite.</p>
	<?php
}

function adinj_unknown_cache_msg(){
	?>
	<blockquote>
	<p><b>Recommended settings:</b></p>
	<blockquote>
	<ul>
	<li><b>WP Super Cache</b> - 0.9.9.8+ using mod_rewrite mode.</li>
	<li><b>W3 Total Cache</b> - Page Cache: 'Disk (basic)' mode.</li>
	<li><b>WP Cache</b> - Just turn the caching on.</li>
	</ul>
	</blockquote>
	</blockquote>
	<?php
}

function adinj_side_status_box(){
	?>
	<div class="postbox-container" style="width:258px;">
		<div class="metabox-holder">	
		<div class="meta-box-sortables" style="min-height:50px;">
		<div class="postbox">
		<h3 class="hndle"><span><?php echo adinj_get_logo(); ?> Status</span></h3>
		<div class="inside" style="margin:5px;">
			
			<style type="text/css">
			.adinjstatustable td { vertical-align: top; }
			</style>
		
			<table border="0" cellpadding="2" class="adinjstatustable">
			
			<tr><td style="text-align:right">
			<b><a href="#global">Ads enabled</a></b>
			</td><td>
			<?php 
			$info = adinj_get_status('global'); echo adinj_dot($info[0]).' '.$info[1];
			if ($info[0] != 'red') {	?>
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('exclude_front'); ?> front
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('exclude_home'); ?> home
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('exclude_page'); ?> page
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('exclude_single'); ?> single
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('exclude_archive'); ?> archive
			</td></tr>
		
			<tr><td style="text-align:right">
			<b><a href="#random">Random ads</a></b>
			</td><td>
			<?php 
			$info = adinj_get_status('random'); echo adinj_dot($info[0]).' '.$info[1].'<br />'; 
			$info = adinj_get_status('random_home'); echo adinj_dot($info[0]).' '.$info[1].'<br />';
			$info = adinj_get_status('random_pool'); echo adinj_dot($info[0]).' '.$info[1].'<br />';
			$info = adinj_get_status('random_alt_pool'); echo adinj_dot($info[0]).' '.$info[1].'<br />'; 
			?>
			</td></tr>
			
			<tr><td style="text-align:right">
			<b><a href="#topad">Top ad</a></b>
			</td><td>
			<?php 
			$info = adinj_get_status('topad'); echo adinj_dot($info[0]).' '.$info[1].'<br />';
			$info = adinj_get_status('top_pool'); echo adinj_dot($info[0]).' '.$info[1].'<br />'; 
			$info = adinj_get_status('top_alt_pool'); echo adinj_dot($info[0]).' '.$info[1].'<br />'; 
			?>
			</td></tr>
			
			<tr><td style="text-align:right">
			<b><a href="#bottomad">Bottom ad</a></b>
			</td><td>
			<?php 
			$info = adinj_get_status('bottomad'); echo adinj_dot($info[0]).' '.$info[1].'<br />';
			$info = adinj_get_status('bottom_pool'); echo adinj_dot($info[0]).' '.$info[1].'<br />'; 
			$info = adinj_get_status('bottom_alt_pool'); echo adinj_dot($info[0]).' '.$info[1].'<br />'; 
			?>
			</td></tr>
			
			<tr><td style="text-align:right; vertical-align:top">
			<b><a href="#widgets">Widgets</a></b>
			</td><td>
			<?php $info = adinj_get_status('widgets'); echo adinj_dot($info[0]).' '.$info[1]; 
			if ($info[0] != 'red') {	?>
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('widget_exclude_front'); ?> front
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('widget_exclude_home'); ?> home
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('widget_exclude_page'); ?> page
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('widget_exclude_single'); ?> single
			<br />&nbsp;&nbsp;<?php echo adinj_green_or_red_dot('widget_exclude_archive'); ?> archive
			<?php } ?>
			</td></tr>
			
			<tr><td style="text-align:right">
			<b><a href="#restrictions">Mode</a></b>
			</td><td>
			<?php $info = adinj_get_status('mode'); echo adinj_dot($info[0]).' '.$info[1]; ?>
			</td></tr>
			
			<tr><td style="text-align:right">
			<b><a href="#dynamic">Restrictions</a></b>
			</td><td>
			<?php $info = adinj_get_status('restrictions'); echo adinj_dot($info[0]).' '.$info[1]; ?>
			</td></tr>
			
			<tr><td style="text-align:right">
			<b><a href="#debugging">Debug mode</a></b>
			</td><td>
			<?php $info = adinj_get_status('debugging'); echo adinj_dot($info[0]).' '.$info[1]; ?>
			</td></tr>
			<tr><td>
			</td><td>
			</td></tr>
			<tr><td>
			</td><td>
			<?php } ?>
			</td></tr>
			</table>
		</div>
		</div>	
		</div>
		</div>
	</div> 	
	
	<br />
	<?php
}

function adinj_debug_information(){
	$stored_options = adinj_options();
	$default_options = adinj_default_options();
	?>
	<h4>Settings dump from database (all in 'adinj_options' option)</h4>
	<table border="1" style="width:610px; table-layout:fixed; word-wrap:break-word;">
	<tr><td><b>Name</b></td><td><b>Stored</b></td><td><b>Default</b></td></tr>
	<?php
	if ($stored_options !== false){
		$count = 0;
		foreach ($stored_options as $key => $value){
			if ($count % 2 == 0){
				echo '<tr style="background-color:#cccccc"><td>';
			} else {
				echo '<tr><td>';
			}
			echo "$key";
			echo "</td><td>";
			$value = htmlentities($value);
			echo "$value";
			echo "</td><td>";
			echo $default_options[$key];
			echo "</td></tr>";
			$count++;
		}
	} else {
		echo "<br />No options in database!";
	}
	echo '</table>';
	
	?><h4>Widget settings dump from database (all in 'widget_adinj' option)</h4>
	<table border="1" style="width:610px; word-wrap:break-word;">
	<tr><td><b>Widget</b></td><td><b>Setting:Value</b></td></tr>
	<?php
	$widgetops = get_option('widget_adinj');
	$count = 0;
	if (is_array($widgetops)){
		foreach($widgetops as $key=>$val){
			if ($count % 2 == 0){
				echo '<tr style="background-color:#cccccc"><td style="vertical-align:top">';
			} else {
				echo '<tr><td style="vertical-align:top">';
			}
			echo $key;
			echo "</td>";
			echo '<td style="vertical-align:top">';
			if (is_array($val)){
				foreach($val as $subkey=>$subval){
					echo $subkey.':'.htmlentities($subval).'<br />';
				}
			} else {
				echo htmlentities($val);
			}
			echo '</td></tr>';
			++$count;
		}
	} else {
		echo '<tr><td></td><td><b>No widget settings found</b></td></tr>';
	}
	echo '</table>';
	
	echo '<h4>Other settings</h4><blockquote>';
	
	echo 'ADINJ_PATH='.ADINJ_PATH.'<br />';
	echo 'ADINJ_CONFIG_FILE='.ADINJ_CONFIG_FILE.'<br />';
	echo 'ADINJ_AD_PATH='.ADINJ_AD_PATH.'<br />';
	
	echo 'Plugin version='.adinj_get_version();
	echo '</blockquote>';
	
	global $adinj_warning_msg_filewrite;
	if (!empty($adinj_warning_msg_filewrite)){
		echo "<h4>Errors on 'Save all settings'</h4><blockquote>$adinj_warning_msg_filewrite</blockquote";
	}
	
	global $adinj_warning_msg_chmod;
	if (!empty($adinj_warning_msg_chmod)){
		echo "<h4>Warnings on 'Save all settings'</h4><blockquote>$adinj_warning_msg_chmod</blockquote";
	}
	
}

function adinj_docs(){
?>
<hr />

<?php adinj_postbox_start(__("Quick Start", 'adinj'), "docs", '95%'); ?>

<p>1. Copy and paste your ad code into the ad code boxes.</p>

<p>2. Choose how many ads of each type you want displayed.</p>

<p>3. Configure any ad positioning (optional).</p>

<p>4. Check the ad insertion mode (in the Insertion mode and ad display restriction section).</p>

<p>5. If you are using a compatible ad insertion mode you may configure dynamic ad display restrictions. i.e. showing ads only to certain people (e.g. search engine visitors), or blocking ads from specific IPs.</p>

<p>6. Enable your ads (tick box at the very top).</p>
</div>

<h3><?php echo adinj_get_logo(); ?> Supported in-page tags</h3>
<div class="inside" style="margin:10px">

<p>These tags can be inserted into the page source to override the configured behaviour on single posts and pages. Because sometimes specific pages need to be treated differently.</p>

<ul>
<li><code>&lt;!--noadsense--&gt;</code> OR <code>&lt;!-no-adsense--&gt;</code> OR <code>&lt;!--NoAds--&gt;</code> OR <code>&lt;!--OffAds--&gt;</code> - disables all ads on this page. These tags are here to make this plugin compatible with the tags from Adsense Injection, Whydowork Adsense and Quick Adsense.</li>
</ul>

<p></p>

<ul>
<li><code>&lt;!--adsandwich--&gt;</code> - Inserts the top and bottom ad but no random ads. Disables all other ads.</li>
<li><code>&lt;!--adfooter--&gt;</code> - Insert a single ad at the very bottom. Disables all other ads.</li>
</ul>

<p></p>

<ol>
<li><code>&lt;!--adsensestart--&gt;</code> - Random ads will start from this point*. For compatibility with Adsense Injection.</li>
<li><code>&lt;!--adsenseend--&gt;</code> - Random ads will not be inserted after this point*. New tag but I've kept the Adsense Injection naming convention to make it fit with the above tag.</li>
<li><code>&lt;!--adstart--&gt;</code> - Random ads will start from this point*.</li>
<li><code>&lt;!--adend--&gt;</code> - Random ads will not be inserted after this point*.</li>
</ol>

<p>These four tags will not affect the top and bottom ad.</p>

<h4>Custom field for disabling adverts</h4>

<p>To disable all adverts on the page you can also set the custom <code>disable_adverts</code> field to '1' from the WordPress post editor.</p>


<?php adinj_postbox_end(); ?>


<?php adinj_postbox_start(__("Test Adverts", 'adinj'), "testads", '95%'); ?>

<p>You can copy and paste these adverts into the boxes above to test your ad setup before switching to your real ads.</p>

<h4><a name="468x60"></a>468x60 banner</h4>

<p><textarea onclick="javascript:this.focus();this.select();" style="min-height:50px;" cols="80" rows="4">&lt;div style=&quot;background-color:#ff9999; width:468px; height:60px;&quot;&gt;
&lt;h5&gt;TEST ADVERT 468x60 - &lt;a href=&quot;http://www.reviewmylife.co.uk/&quot;&gt;www.reviewmylife.co.uk&lt;/a&gt;&lt;/h5&gt;
&lt;/div&gt;</textarea></p>

<div style="background-color:#99ffff; width:468px; height:60px;">
<h5>TEST ADVERT 468x60 - <a href="http://www.reviewmylife.co.uk/">www.reviewmylife.co.uk</a></h5>
</div><p></p>

<h4><a name="728x90"></a>728x90 banner</h4>

<p><textarea onclick="javascript:this.focus();this.select();" style="min-height:50px;" cols="80" rows="4">&lt;div style=&quot;background-color:#ff9999; width:728px; height:90px;&quot;&gt;
&lt;h5&gt;TEST ADVERT 728x90&lt;/h5&gt;
&lt;a href=&quot;http://www.reviewmylife.co.uk/&quot;&gt;www.reviewmylife.co.uk&lt;/a&gt;&lt;br /&gt;
&lt;a href=&quot;http://www.advancedhtml.co.uk/&quot;&gt;www.advancedhtml.co.uk&lt;/a&gt;
&lt;/div&gt;</textarea></p>

<div style="background-color:#ff9999; width:728px; height:90px;">
<h5>TEST ADVERT 728x90</h5>
<a href="http://www.reviewmylife.co.uk/">www.reviewmylife.co.uk</a><br />
<a href="http://www.advancedhtml.co.uk/">www.advancedhtml.co.uk</a>
</div><p></p>

<h4>160x90 link unit</h4>

<p><textarea onclick="javascript:this.focus();this.select();" style="min-height:50px;" cols="80" rows="4">&lt;div style=&quot;background-color:#ccff99; width:160px; height:90px;&quot;&gt;
&lt;h5&gt;TEST ADVERT 160x90&lt;/h5&gt;
&lt;a href=&quot;http://www.reviewmylife.co.uk/&quot;&gt;reviewmylife.co.uk&lt;/a&gt;&lt;br /&gt;
&lt;a href=&quot;http://www.advancedhtml.co.uk/&quot;&gt;advancedhtml.co.uk&lt;/a&gt;
&lt;/div&gt;</textarea></p>

<div style="background-color:#ccff99; width:160px; height:90px;">
<h5>TEST ADVERT 160x90</h5>
<a href="http://www.reviewmylife.co.uk/">reviewmylife.co.uk</a>
<a href="http://www.advancedhtml.co.uk/">advancedhtml.co.uk</a><br />
</div><p></p>

<h4><a name="468x15"></a>468x15 link unit</h4>

<p><textarea onclick="javascript:this.focus();this.select();" style="min-height:50px;" cols="80" rows="4">&lt;div style=&quot;background-color:#cccc99; width:468px; height:15px;&quot;&gt;
&lt;font size=&quot;-2&quot;&gt;&lt;b&gt;TEST ADVERT 160x90&lt;/b&gt; &lt;a href=&quot;http://www.reviewmylife.co.uk/&quot;&gt;reviewmylife.co.uk&lt;/a&gt;&lt;/font&gt;
&lt;/div&gt;</textarea></p>

<div style="background-color:#cccc99; width:468px; height:15px;">
<font size="-2"><b>TEST ADVERT 160x90</b> <a href="http://www.reviewmylife.co.uk/">reviewmylife.co.uk</a></font>
</div><p></p>

<h4><a name="336x280"></a>336x280 large rectangle</h4>

<p><textarea onclick="javascript:this.focus();this.select();" style="min-height:50px;" cols="80" rows="4">&lt;div style=&quot;background-color:#ccccff; width:336px; height:280px;&quot;&gt;
&lt;h5&gt;TEST ADVERT 336x280 - &lt;a href=&quot;http://www.reviewmylife.co.uk/&quot;&gt;www.reviewmylife.co.uk&lt;/a&gt;&lt;/h5&gt;
&lt;/div&gt;</textarea></p>

<div style="background-color:#ccccff; width:336px; height:280px;">
<h5>TEST ADVERT 336x280 - <a href="http://www.reviewmylife.co.uk/">www.reviewmylife.co.uk</a></h5>
</div><p></p>

<h4>468x60 banner with dynamic PHP</h4>

<p>The PHP will execute if you use a mfunc compatible caching plugin which is correctly configured, or if you don't use any caching plugin at all.</p>

<p><textarea onclick="javascript:this.focus();this.select();" style="min-height:50px;" cols="80" rows="5">&lt;div style=&quot;background-color:#ffff99; width:468px; height:60px;&quot;&gt;
&lt;b&gt;TEST ADVERT 468x60 with date() and rand()&lt;/b&gt;&lt;br /&gt;
&lt;?php echo &quot;date=&quot;.date(&quot;Y-m-d H:i:s&quot;) .&quot; rand=&quot;.rand(); ?&gt;&lt;br /&gt;
&lt;a href=&quot;http://www.advancedhtml.co.uk/&quot;&gt;www.advancedhtml.co.uk&lt;/a&gt;
&lt;/div&gt;</textarea></p>

<div style="background-color:#ffff99; width:468px; height:60px;">
<b>TEST ADVERT 468x60 with date() and rand()</b><br />
<?php echo "date=".date("Y-m-d H:i:s") ." rand=".rand(); ?><br />
<a href="http://www.advancedhtml.co.uk/">www.advancedhtml.co.uk</a>
</div><p></p>

<?php adinj_postbox_end();

}

?>