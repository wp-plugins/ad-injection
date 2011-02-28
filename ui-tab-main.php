<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

if (!is_admin()) return;

function adinj_tab_main(){
	$ops = adinj_options();
	?>
	
	<p><a href="#adsettings">Ad Placement settings</a> | <a href="#adverts">Adverts</a> | <a href="#restrictions">Ad insert mode/dynamic restrictions</a> | <a href="#docsquickstart">Quick Start</a> | <a href="#testads">Test ads</a></p>
	
	<?php adinj_postbox_start(__("Global settings", 'adinj'), 'global'); ?>
	
	<p>These settings apply to all ads (random, top, bottom, and widget). They will override all other settings.</p>
	
	<input type="radio" name="ads_enabled" value="on" <?php if ($ops['ads_enabled']=='on') echo 'checked="checked"'; ?> /> <b>On: <?php _e('Ads enabled', 'adinj') ?></b><br />
	<input type="radio" name="ads_enabled" value="off" <?php if ($ops['ads_enabled']=='off' || $ops['ads_enabled']=='') echo 'checked="checked"'; ?> /> <b>Off</b><br />
	<input type="radio" name="ads_enabled" value="test" <?php if ($ops['ads_enabled']=='test') echo 'checked="checked"'; ?> /> <b>Test mode</b> - Only show ads to admin.<br />
	
	<script type="text/javascript">
	jQuery(document).ready(function(){
	jQuery('input[name=ads_enabled]:radio').change(function() {
		if (jQuery('input[name=ads_enabled]:checked').val() == "test"){
			jQuery('#test_mode_warning').slideDown(500);
		} else {
			jQuery('#test_mode_warning').slideUp(500);
		}
		return true;
		});
	});
	if ('<?php echo $ops['ads_enabled'] ?>' != 'test') {
		document.write('<style type="text/css">#test_mode_warning { display: none; }</style>');
	}
	</script>
	<div id="test_mode_warning"><span style="font-size:10px;color:red;">Warning: Turn any caching plugin *off* before using test mode. If you leave the caching plugin on, the test adverts will be cached and shown to your real visitors.</span><br /></div>
	
	<style type="text/css">
	.adinjtable td { vertical-align: top; }
	</style>
	
	<table border="0" class="adinjtable">
	<tr>
	<td><p><?php _e("Only show ads on pages older than ", 'adinj') ?></p></td>
	<td>
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
	</table>
	<?php
	adinj_condition_tables('global_', 'ui_conditions_show');
	adinj_postbox_end();
	?>
	

	
	<?php adinj_postbox_start(__("Ad placement settings", 'adinj'), 'adsettings'); ?>
	<p><b>Exclude ads from page types</b></p>
	<?php
	$count_pages = wp_count_posts('page', 'readable'); 
	$count_posts = wp_count_posts('post', 'readable'); 
	?>
	<table><tr><td><b>all ads</b></td><td></td><td><b>widget ads</b></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_front') ?></td><td>front - <?php echo get_bloginfo('url'); ?></td><td><?php adinj_add_checkbox('widget_exclude_front') ?></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_home') ?></td><td>home page (latest posts page - may or may not be same as front)</td><td><?php adinj_add_checkbox('widget_exclude_home') ?></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_page') ?></td><td>page - <?php echo $count_pages->publish; ?> single page(s)</td><td><?php adinj_add_checkbox('widget_exclude_page') ?></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_single') ?></td><td>single - <?php echo $count_posts->publish; ?> single post(s)</td><td><?php adinj_add_checkbox('widget_exclude_single') ?></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_archive') ?></td><td>archive - categories, tags, authors, dates</td><td><?php adinj_add_checkbox('widget_exclude_archive') ?></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_search') ?></td><td>search (widgets only for now)</td><td><?php adinj_add_checkbox('widget_exclude_search') ?></td></tr>
	<tr><td><?php adinj_add_checkbox('exclude_404') ?></td><td>404 (widgets only for now)</td><td><?php adinj_add_checkbox('widget_exclude_404') ?></td></tr>
	</table>
	<p><span style="font-size:10px;"><b>Notes:</b> Your home page is the page displaying your latest posts. It may be different to your front page if you have configured your front page to be a static page.</span></p>
	<p><span style="font-size:10px;">If you have <a href='options-reading.php'>set your front page</a> to be a static 'page' rather than your latest posts, the 'page' tick box will also apply to the front page.</span></p>
	<p><span style="font-size:10px;">You must configure your individual widgets from the <a href="widgets.php">widgets control panel</a>. Ticking the 'all ads' box will also exclude the widget ads.</span></p>
	<p></p>
	
	<table border="0" class="adinjtable">
	<tr><td></td><td><b>Single/Page</b></td><td><b>Home</b></td><td><b>Archive</b></td></tr>
	<tr><td colspan="4"><h3>Top ad</h3></td></tr>
	
	<tr><td>Only show top ad on posts longer than:</td><td>
	<?php
	$unit = adinj_counting_unit_description();
	$ad_if_longer_settings = array('d','a',100,200,300,500,1000,1500,2000,2500,3000,5000,10000,20000);
	adinj_selection_box("top_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</td><td>';
	adinj_selection_box("home_top_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</td><td>';
	adinj_selection_box("archive_top_ad_if_longer_than", $ad_if_longer_settings, $unit);
	?>
	</td></tr>
	
	<tr><td>Max num of top ads on whole page:</td><td>1</td><td>
	<?php
	$num_ads_array = array(0,1,2,3,4,5,6,7,8,9,10);
	adinj_selection_box("home_max_num_top_ads_per_page", $num_ads_array);
	echo '</td><td>';
	adinj_selection_box("archive_max_num_top_ads_per_page", $num_ads_array);
	?>
	</td></tr>
	
	
	<tr><td colspan="4">
	<?php adinj_condition_tables('top_', 'ui_top_conditions_show'); ?>
	<p></p>
	</td></tr>
	
	<tr><td colspan="4"><h3>Random ads</h3></td></tr>
	<tr><td>Max num of random ads on whole page:</td><td>
	n/a
	</td><td>
	<?php
	adinj_selection_box("home_max_num_random_ads_per_page", $num_ads_array);
	echo '</td><td>';
	adinj_selection_box("archive_max_num_random_ads_per_page", $num_ads_array);
	?>
	</td></tr>
	<tr><td>Max num of random ads per post:</td><td>
	<?php
	adinj_selection_box("max_num_of_ads", $num_ads_array);
	echo '</td><td>';
	adinj_selection_box("home_max_num_random_ads_per_post", $num_ads_array);
	echo '</td><td>';
	adinj_selection_box("archive_max_num_random_ads_per_post", $num_ads_array);
	?>
	</td></tr>
	<?php
	adinj_random_ad_limit_table();
	?>
	<tr><td colspan="4">
	<?php
		_e("Always put the first ad immediately after paragraph: ", 'adinj');
		adinj_selection_box("start_from_paragraph",	array(ADINJ_RULE_DISABLED,1,2,3,4,5), " ");
	?>
	</td></tr>
	<tr><td colspan="4">
	<?php adinj_condition_tables('random_', 'ui_random_conditions_show'); ?>
	<p></p>
	</td></tr>
	
	<tr><td colspan="4"><h3>Bottom ad</h3></td></tr>
	
	<tr><td>Only show bottom ad on posts longer than:</td><td>
	<?php
	adinj_selection_box("bottom_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</td><td>';
	adinj_selection_box("home_bottom_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</td><td>';
	adinj_selection_box("archive_bottom_ad_if_longer_than", $ad_if_longer_settings, $unit);
	?>
	</td></tr>
	
	<tr><td>Max num of bottom ads on whole page:</td><td>1</td><td>
	<?php
	adinj_selection_box("home_max_num_bottom_ads_per_page", $num_ads_array);
	echo '</td><td>';
	adinj_selection_box("archive_max_num_bottom_ads_per_page", $num_ads_array);
	?>
	</td></tr>
	
	<tr><td colspan="4">
	<?php adinj_condition_tables('bottom_', 'ui_bottom_conditions_show'); ?>
	<p></p>
	</td></tr>
	</table>
	
	<?php adinj_postbox_end(); ?>
	
	

	
	<?php adinj_postbox_start(__("Adverts", 'adinj'), 'adverts'); ?>
	
	<h3>Top ad (above the post content - this is not a 'header' ad)</h3>
	<table border="0" class="adinjtable">
	<tr><td>
	<textarea name="ad_code_top_1" rows="10" cols="60"><?php echo $ops['ad_code_top_1']; ?></textarea>
	<br />
	<p><span style="font-size:10px;"><b>Docs:</b> Try a <a href="#468x15">468x15</a> or <a href="#336x280">336x280</a> advert.</span></p>
	</td><td>
	<?php
	adinj_add_alignment_options('top_');  
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_top'>Rotation:<br />".adinj_percentage_split('ad_code_top_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	<p><span style="font-size:10px;">Be especially careful if you decide to use the 'float' layout options. Make sure that you don't have adverts floated over the top of other page elements, or vice-versa.</span></p>
	
	<h3>Random ad (inserted randomly between paragraphs)</h3>
	<table border="0" class="adinjtable">
	<tr><td>
	<textarea name="ad_code_random_1" rows="10" cols="60"><?php echo $ops['ad_code_random_1']; ?></textarea>
	<br />
	<p><span style="font-size:10px;"><b>Docs:</b> Try a <a href="#468x60">468x60</a> or <a href="#728x90">728x90</a> banner.</span></p>
	</td><td>
	<?php
	adinj_add_alignment_options('rnd_');  
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_random'>Rotation:<br />".adinj_percentage_split('ad_code_random_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	
	<h3>Bottom ad (below the post content - this is not a 'footer' ad)</h3>
	<table border="0" class="adinjtable">
	<tr><td>
	<textarea name="ad_code_bottom_1" rows="10" cols="60"><?php echo $ops['ad_code_bottom_1']; ?></textarea>
	<br />
	<p><span style="font-size:10px;"><b>Docs:</b> Try a <a href="#336x280">336x280</a> advert.</span></p>
	</td><td>
	<?php 
	adinj_add_alignment_options('bottom_');  
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_bottom'>Rotation:<br />".adinj_percentage_split('ad_code_bottom_', 1, $ops)."</a></b>";
	?>
	</td></tr>
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
	<p><input type="radio" name="ad_insertion_mode" value="direct_static" <?php if ($ops['ad_insertion_mode']=='direct_static') echo 'checked="checked"'; ?> /> <b><strike>direct_static: Direct static ad insertion</strike></b> - <font color="red">Warning: This mode will be removed very soon (March 2011). There will only be an 'mfunc' and a 'direct' mode to simplify things. I recommend you switch to 'direct_dynamic' if you are using this mode. If you aren't using dynamic features make sure the tick boxes below are unticked.</font> No dynamic feature support. Select this if you are are not using dynamic features or are using an incompatible caching plugin.</p>
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
	
	<?php if ($ops['ad_insertion_mode'] != 'direct_static') { ?>
	<script type="text/javascript">
	document.write('<style type="text/css" media="screen">#dynamic_features_msg { display: none; }</style>');
	</script>
	<?php } ?>
	<div id="dynamic_features_msg" class="dynamic_features_msg">
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
	<?php adinj_add_checkbox('block_ips') ?><?php _e("Exclude ads from these IP addresses.", 'adinj') ?><br />
	<textarea name="blocked_ips" rows="4" cols="70"><?php echo $ops['blocked_ips']; ?></textarea>
	<p>Comma separated list e.g.: <br /><code>0.0.0.1, 0.0.0.2</code></p>
	<p>Or you can list one IP per line with optional comments e.g.</p>
	<code style="padding:0px 0px">192.168.0.1<br />0.0.0.2<br /><?php echo $_SERVER['REMOTE_ADDR'] ?> //my ip<br />0.0.0.3</code>
	
	<p>For reference your current IP address is <code><?php echo $_SERVER['REMOTE_ADDR'] ?></code></p>
	</blockquote>
	</div>
	
	</div>
	
	<?php adinj_postbox_end(); ?>
	
	<br clear="all" />
	
	<?php
	
	adinj_docs();
	
	adinj_testads();
}

function adinj_random_ad_limit_table(){
	?>
	<tr><td>No ads if shorter than:</td>
	<?php
	$prefixes = array("", "home_", "archive_");
	$unit = adinj_counting_unit_description();
	$ad_limit_settings = array(ADINJ_RULE_DISABLED,100,200,300,500,1000,1500,2000,2500,3000);
	
	foreach ($prefixes as $prefix){
		echo '<td>';
		adinj_selection_box($prefix."no_random_ads_if_shorter_than", $ad_limit_settings, $unit);
		echo '</td>';
	} ?>
	</tr>
	<tr><td>One ad if shorter than:</td>
	<?php
	foreach ($prefixes as $prefix){
		echo '<td>';
		adinj_selection_box($prefix."one_ad_if_shorter_than", $ad_limit_settings, $unit);
		echo '</td>';
	} ?>
	</tr>
	<tr><td>Two ads if shorter than:</td>
	<?php
	foreach ($prefixes as $prefix){
		echo '<td>';
		adinj_selection_box($prefix."two_ads_if_shorter_than", $ad_limit_settings, $unit);
		echo '</td>';
	}
	?>
	</tr>
	<tr><td>Three ads if shorter than:</td>
	<?php
	foreach ($prefixes as $prefix){
		echo '<td>';
		adinj_selection_box($prefix."three_ads_if_shorter_than", $ad_limit_settings, $unit);
		echo '</td>';
	} ?>
	</tr>
	<?php
}

function adinj_counting_unit_description(){
	$ops = adinj_options();
	$unit = $ops['content_length_unit'];
	if ($unit == 'all'){
		return '(all chars)';
	} else if ($unit == 'viewable'){
		return '(chars)';
	} else {
		return '(words)';
	}
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
	$ops = adinj_options();
	?>
	<div class="postbox-container" style="width:258px;">
		<div class="metabox-holder">	
		<div class="meta-box-sortables" style="min-height:50px;">
		<div class="postbox">
		<h3 class="hndle"><span><?php echo adinj_get_logo(); ?> Status</span></h3>
		<div class="inside" style="margin:5px;">
			
			<style type="text/css">
			.adinjstatustable td { vertical-align: top; }
			.adinjstatustable td { padding: 2px; }
			</style>
		
			<table border="0" cellpadding="2" class="adinjstatustable">

			
			<tr><td style="text-align:right">
			<b><a href="#global">Ads enabled</a></b>
			</td><td>
			<?php 
			$info = adinj_get_status('global'); echo adinj_dot($info[0]).' '.$info[1];
			if ($info[0] != 'red') {	?>
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
			<b><a href='?page=ad-injection&amp;tab=debug#debugging'>Debug mode</a></b>
			</td><td>
			<?php $info = adinj_get_status('debugging'); echo adinj_dot($info[0]).' '.$info[1]; ?>
			</td></tr>
			
			<tr><td colspan="2">
			<p></p>
			<b>Ad summary</a></b><br />
			<table class="adinjstatustable">
			<tr>
				<td></td>
				<td><b>Ads</b></td>
				<td><b>Alt</b></td>
				<td><b>S</b></td>
				<td><b>P</b></td>
				<td><b>H</b></td>
				<td><b>A</b></td>
			</tr>
			<tr>
				<td style="text-align:right"><b>Top</b></td>
				<td><?php echo adinj_count_live_ads('top', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('top_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('top', 'single') ?></td>
				<td><?php adinj_print_ad_dot('top', 'page') ?></td>
				<td><?php adinj_print_ad_dot('top', 'home') ?></td>
				<td><?php adinj_print_ad_dot('top', 'archive') ?></td>
			</tr>
			<tr>
				<td style="text-align:right"><b>Random</b></td>
				<td><?php echo adinj_count_live_ads('random', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('random_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('random', 'single') ?></td>
				<td><?php adinj_print_ad_dot('random', 'page') ?></td>
				<td><?php adinj_print_ad_dot('random', 'home') ?></td>
				<td><?php adinj_print_ad_dot('random', 'archive') ?></td>
			</tr>
			<tr>
				<td style="text-align:right"><b>Bottom</b></td>
				<td><?php echo adinj_count_live_ads('bottom', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('bottom_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'single') ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'page') ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'home') ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'archive') ?></td>
			</tr>
			<tr>
				<td style="text-align:right"><b>Widget</b></td>
				<td></td>
				<td></td>
				<td><?php adinj_print_ad_dot('widget', 'single') ?></td>
				<td><?php adinj_print_ad_dot('widget', 'page') ?></td>
				<td><?php adinj_print_ad_dot('widget', 'home') ?></td>
				<td><?php adinj_print_ad_dot('widget', 'archive') ?></td>
			</tr>
			</table>
			<p>S=single post<br />
			P=single page<br />
			H=home<br />
			A=archive<br />
			<!--E=excerpt--></p>
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



function adinj_docs(){
?>
<hr />

<?php adinj_postbox_start(__("Quick Start", 'adinj'), "docsquickstart", '95%'); ?>

<p>1. Copy and paste your ad code into the ad code boxes.</p>

<p>2. Choose how many ads of each type you want displayed.</p>

<p>3. Configure any ad positioning (optional).</p>

<p>4. Check the ad insertion mode (in the Insertion mode and ad display restriction section).</p>

<p>5. If you are using a compatible ad insertion mode you may configure dynamic ad display restrictions. i.e. showing ads only to certain people (e.g. search engine visitors), or blocking ads from specific IPs.</p>

<p>6. Enable your ads (tick box at the very top).</p>

<?php adinj_postbox_end();

}

?>