<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

if (!is_admin()) return;

function adinj_tab_main(){
	$ops = adinj_options();
	?>
	
	<p><a href="#adsettings">Ad Placement settings</a> | <a href="#adverts">Ads</a> | <a href="#restrictions">Ad insert mode/dynamic restrictions</a> | <a href="#filters">Filters</a> | <a href="#docsquickstart">Quick Start</a> | <a href="#testads">Test ads</a></p>
	
	<?php adinj_global_settings_box($ops); ?>
	
	
	<?php adinj_postbox_start(__("Ad placement settings", 'adinj'), 'adsettings'); ?>
	
	<p></p>
	
	<table border="0" class="adinjtable" width="95%">
	<tr><td></td><td width="20%"><b>Single/Page</b></td><td width="20%"><b>Home</b></td><td width="20%"><b>Archive</b></td></tr>
	<tr><td colspan="4"><h3><a name="topadplacement"></a>Top ad [<a href="#topadcode">code</a>] [<a href="#pagetypefilters">page type filters</a>]</h3></td></tr>
	
	<tr><td>Only show on posts longer than:</td><td>
	<?php
	$unit = adinj_counting_unit_description();
	$ad_if_longer_settings = array('d',100,200,300,500,750,1000,1500,2000,2500,3000,5000,10000,15000,20000);
	adinj_selection_box("top_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</td><td><div class="adinj_home">';
	adinj_selection_box("home_top_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</div></td><td><div class="adinj_archive">';
	adinj_selection_box("archive_top_ad_if_longer_than", $ad_if_longer_settings, $unit);
	?>
	</div></td></tr>
	
	<tr><td><b>|_</b> Max num of ads on whole page:</td><td>1</td><td><div class="adinj_home">
	<?php
	$num_ads_array = array(0,1,2,3,4,5,6,7,8,9,10);
	adinj_selection_box("home_max_num_top_ads_per_page", $num_ads_array);
	echo '</div></td><td><div class="adinj_archive">';
	adinj_selection_box("archive_max_num_top_ads_per_page", $num_ads_array);
	?>
	</div></td></tr>
	
	
	<tr><td colspan="4"><h3><a name="randomadplacement"></a>Random ads [<a href="#randomadcode">code</a>] [<a href="#pagetypefilters">page type filters</a>]</h3></td></tr>
	<tr><td>Max num of ads on whole page:</td><td>
	n/a
	</td><td><div class="adinj_home">
	<?php
	adinj_selection_box("home_max_num_random_ads_per_page", $num_ads_array);
	echo '</div></td><td><div class="adinj_archive">';
	adinj_selection_box("archive_max_num_random_ads_per_page", $num_ads_array);
	?>
	</div></td></tr>
	<tr><td><b>|_ </b>Max num of random ads per post:</td><td>
	<?php
	adinj_selection_box("max_num_of_ads", $num_ads_array);
	echo '</td><td><div class="adinj_home">';
	adinj_selection_box("home_max_num_random_ads_per_post", $num_ads_array);
	echo '</div></td><td><div class="adinj_archive">';
	adinj_selection_box("archive_max_num_random_ads_per_post", $num_ads_array);
	?>
	</div></td></tr>
	<?php
	adinj_random_ad_limit_table();
	?>
	<tr><td colspan="4">
		<table class="adinjtable">
		<tr><td>
		<?php
			_e("Always start the first ad ", 'adinj');
		?>
		</td><td>
			<input type="radio" name="random_ads_after_mode" value="at" <?php if ($ops['random_ads_after_mode']=='at') echo 'checked="checked"'; ?> /> <b>at</b><br />
			<input type="radio" name="random_ads_after_mode" value="after" <?php if ($ops['random_ads_after_mode']=='after') echo 'checked="checked"'; ?> /> <b>at or after</b><br />
		</td><td>
			<input type="radio" name="random_ads_after_unit" value="paragraph" <?php if ($ops['random_ads_after_unit']=='paragraph') echo 'checked="checked"'; ?> /> <b>paragraph:</b><br />
			<input type="radio" name="random_ads_after_unit" value="character" <?php if ($ops['random_ads_after_unit']=='character' || $ops['ads_enabled']=='') echo 'checked="checked"'; ?> /> <b>character:</b><br />
		</td>
		<td>
		<?php
			adinj_selection_box("start_from_paragraph", array('d',1,2,3,4,5,100,200,300,500,750,1000,1500,2000,3000,4000,5000,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20), " ");
		?>
		</td></tr>
		</table>
		
		<script type="text/javascript">
		jQuery(document).ready(function(){
		jQuery('input[name=random_ads_after_unit]:radio').change(function() {
			if (jQuery('input[name=random_ads_after_unit]:checked').val() == "character"){
				jQuery('#random_ads_after_warning').slideDown(300);
			} else {
				jQuery('#random_ads_after_warning').slideUp(300);
			}
			return true;
			});
		});
		if ('<?php echo $ops['random_ads_after_unit'] ?>' == 'paragraph') {
			document.write('<style type="text/css">#random_ads_after_warning { display: none; }</style>');
		}
		</script>
		<div id="random_ads_after_warning"><span style="font-size:10px;color:red;">Note: If starting 'at character x', the start point will be the next paragraph.</span><br /></div>
			
	</td></tr>
	
	<tr><td colspan="4"><h3><a name="bottomadplacement"></a>Bottom ad [<a href="#bottomadcode">code</a>] [<a href="#pagetypefilters">page type filters</a>]</h3></td></tr>
	
	<tr><td>Only show on posts longer than:</td><td>
	<?php
	adinj_selection_box("bottom_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</td><td><div class="adinj_home">';
	adinj_selection_box("home_bottom_ad_if_longer_than", $ad_if_longer_settings, $unit);
	echo '</div></td><td><div class="adinj_archive">';
	adinj_selection_box("archive_bottom_ad_if_longer_than", $ad_if_longer_settings, $unit);
	?>
	</div></td></tr>
	
	<tr><td><b>|_ </b>Max num of ads on whole page:</td><td>1</td><td><div class="adinj_home">
	<?php
	adinj_selection_box("home_max_num_bottom_ads_per_page", $num_ads_array);
	echo '</div></td><td><div class="adinj_archive">';
	adinj_selection_box("archive_max_num_bottom_ads_per_page", $num_ads_array);
	?>
	</div></td></tr>
	
	
	
	</table>
	
	<?php adinj_postbox_end(); ?>
	
	

	
	<?php adinj_postbox_start(__("Adverts", 'adinj'), 'adverts'); ?>
	
	<h3><a name="topadcode"></a>Top ad (below post title - this is not a 'header' ad) [<a href="#topadplacement">placement</a>] <!--[<a href='?page=ad-injection&amp;tab=adrotation#multiple_top'>pool</a>]--></h3>
	<table border="0" class="adinjtable" width="95%">
	<tr><td>
	<textarea name="ad_code_top_1" rows="10" cols="<?php adinj_table_width('ad'); ?>"><?php echo $ops['ad_code_top_1']; ?></textarea>
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
	
	<h3><a name="randomadcode"></a>Random ad (inserted randomly between paragraphs) [<a href="#randomadplacement">placement</a>] <!--[<a href='?page=ad-injection&amp;tab=adrotation#multiple_random'>pool</a>]--></h3>
	<table border="0" class="adinjtable" width="95%">
	<tr><td>
	<textarea name="ad_code_random_1" rows="10" cols="<?php adinj_table_width('ad'); ?>"><?php echo $ops['ad_code_random_1']; ?></textarea>
	<br />
	<p><span style="font-size:10px;"><b>Docs:</b> Try a <a href="#468x60">468x60</a> or <a href="#728x90">728x90</a> banner.</span></p>
	</td><td>
	<?php
	adinj_add_alignment_options('rnd_');  
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_random'>Rotation:<br />".adinj_percentage_split('ad_code_random_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	
	<h3><a name="bottomadcode"></a>Bottom ad (below the post content) [<a href="#bottomadplacement">placement</a>] <!--[<a href='?page=ad-injection&amp;tab=adrotation#multiple_bottom'>pool</a>]--></h3>
	<table border="0" class="adinjtable" width="95%">
	<tr><td>
	<textarea name="ad_code_bottom_1" rows="10" cols="<?php adinj_table_width('ad'); ?>"><?php echo $ops['ad_code_bottom_1']; ?></textarea>
	<br />
	<p><span style="font-size:10px;"><b>Docs:</b> Try a <a href="#336x280">336x280</a> advert.</span></p>
	</td><td>
	<?php 
	adinj_add_alignment_options('bottom_');  
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_bottom'>Rotation:<br />".adinj_percentage_split('ad_code_bottom_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	
	
	<h3><a name="footeradcode"></a>Footer ad (put into 'the_footer' hook - not supported by all themes) [<a href="#footeradplacement">placement</a>] <!--[<a href='?page=ad-injection&amp;tab=adrotation#multiple_footer'>pool</a>]--></h3>
	<table border="0" class="adinjtable" width="95%">
	<tr><td>
	<textarea name="ad_code_footer_1" rows="10" cols="<?php adinj_table_width('ad'); ?>"><?php echo $ops['ad_code_footer_1']; ?></textarea>
	<br />
	Docs: footer ad information and troubleshooting
	<?php adinj_add_show_hide_section('footer_docs_'.uniqid(''), 'ui_footer_docs_show', 'ui_footer_docs_show', $ops); ?>
	<blockquote>
	<p><span style="font-size:10px;">Try a <a href="#468x60">468x60</a> or <a href="#728x90">728x90</a> banner.</span></p>
	<p><span style="font-size:10px;">The footer ad will only work if your theme supports it.</span></p>
	<p><span style="font-size:10px;">Your theme must include 'the_footer' hook in the correct part of the page. If the footer ad is appearing in the wrong place, you could try manually editing your theme to move 'the_footer' hook.</span></p>
	</blockquote>
	</div>
	</td><td>
	<?php 
	adinj_add_alignment_options('footer_');  
	echo "<br /><b><a href='?page=ad-injection&amp;tab=adrotation#multiple_footer'>Rotation:<br />".adinj_percentage_split('ad_code_footer_', 1, $ops)."</a></b>";
	?>
	</td></tr>
	</table>
	
	
	<?php adinj_postbox_end(); ?>
	
	<?php adinj_filters_box($ops); ?>
	
	<?php adinj_insertion_mode_box($ops); ?>
	
	<br clear="all" />
	
	<?php
	
	adinj_docs();
	
	adinj_testads();
	
	global $adinj_warning_msg_chmod;
	if (!empty($adinj_warning_msg_chmod)){
		echo '<a name="warning_msg_chmod"></a>';
		echo '<br clear="all" />';
		echo $adinj_warning_msg_chmod;
	}
}

function adinj_table_width($table){
	global $wp_db_version;
	if ($table == 'ad'){
		if ($wp_db_version < 18226){
			echo '60';
		} else { //WP3.2+
			echo '70';
		}
	} else if ($table == 'dynamic'){
		if ($wp_db_version < 18226){
			echo '70';
		} else { //WP3.2+
			echo '80';
		}
	} else {
		echo '61';
	}
}

function adinj_global_settings_box($ops){
	adinj_postbox_start(__("Global settings (apply to all ads)", 'adinj'), 'global'); ?>
	
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
	
	<p></p>
	<?php
	$count_pages = wp_count_posts('page', 'readable'); 
	$count_posts = wp_count_posts('post', 'readable'); 
	?>
	
	<a name="pagetypefilters"></a>
	<table class="adinjstatustable">
	<tr>
		<td><b><font color="red">Tick to disable ads:</font></b></td>
		<td><b>Single(<?php echo $count_posts->publish; ?>)</b></td>
		<td><b>Page(<?php echo $count_pages->publish; ?>)</b></td>
		<td><b>Home</b></td>
		<td><b>Archive</b></td>
		<td><b><a href="<?php echo get_bloginfo('url'); ?>" target="_new">Front</a></b></td>
		<td><b>404</b></td>
		<td><b>Search</b></td>
	</tr>
	<?php
	adinj_add_exclude_row('All ads');
	adinj_add_exclude_row('|_&nbsp;&nbsp;<a href="#topadplacement">Top</a>', 'top_');
	adinj_add_exclude_row('|_&nbsp;&nbsp;<a href="#randomadplacement">Random</a>', 'random_');
	adinj_add_exclude_row('|_&nbsp;&nbsp;<a href="#bottomadplacement">Bottom</a>', 'bottom_');
	adinj_add_exclude_row('|_&nbsp;&nbsp;<a href="#footeradplacement">Footer</a>', 'footer_');
	adinj_add_exclude_row('|_&nbsp;&nbsp;Widget', 'widget_');
	adinj_add_exclude_row('|_&nbsp;&nbsp;Template', 'template_');
	?>
	<tr><td colspan="8"><span style="font-size:10px;">Go to the <a href="widgets.php">widget control panel</a> to set up any widgets. See the <a href="http://wordpress.org/extend/plugins/ad-injection/faq/" target="_new">FAQ</a> for how to set up template ads. There are some template ad examples in the <a href="#testads">test ads</a> section of this page.</span></td></tr>
	</table>
	
	<p>
	
	</p>
	
	<p><span style="font-size:10px;"><b>Notes:</b> Your home page is the page displaying your latest posts. It may be different to your front page if you have configured your front page to be a static page. If you have <a href='options-reading.php'>set your front page</a> to be a static 'page' rather than your latest posts, the 'page' tick box will also apply to the front page.</span></p>
	<p><span style="font-size:10px;">Archive pages are the categories, tags, authors and date pages.</span></p>
	
	<style type="text/css">
	.adinjtable td { vertical-align: top; }
	</style>
	<p></p>
	<table border="0" class="adinjtable">
	<tr>
	<td><?php _e("Only show normal ads on pages older than ", 'adinj') ?></td>
	<td>
	<select name='ads_on_page_older_than'>
	<?php
	$older_than_days = array(0, 1, 2, 3, 5, 7, 10, 14, 21, 28, 40, 50);
	for ($value=0; $value<sizeof($older_than_days); ++$value){
		echo "<option value=\"$older_than_days[$value]\" ";
		if($ops['ads_on_page_older_than'] == $older_than_days[$value]) echo 'selected="selected"';
		echo ">$older_than_days[$value]</option>";
	}
	?>
	</select><?php _e(" (days)", 'adinj') ?> - only for single posts and pages
	</td></tr>
	<tr>
	<td><?php _e("Only show widget ads on pages older than ", 'adinj') ?></td>
	<td>
	<select name='widgets_on_page_older_than'>
	<?php
	for ($value=0; $value<sizeof($older_than_days); ++$value){
		echo "<option value=\"$older_than_days[$value]\" ";
		if($ops['widgets_on_page_older_than'] == $older_than_days[$value]) echo 'selected="selected"';
		echo ">$older_than_days[$value]</option>";
	}
	?>
	</select><?php _e(" (days)", 'adinj') ?> - only for single posts and pages
	</td></tr>
	</table>
	
	<p></p>
	
	<?php
	adinj_postbox_end();
}

function adinj_filters_box($ops){
	adinj_postbox_start(__("Tag, category, author and post id filters", 'adinj'), 'filters'); ?>
	<h4>Global filters</h4>
	<?php adinj_condition_tables('global_', 'ui_conditions_show'); ?>
	<p>The global filter apply to all ads, and are processed before the ad type specific filters.</p>
	<h4>Top ad filters</h4>
	<?php adinj_condition_tables('top_', 'ui_top_conditions_show'); ?>
	<h4>Random ad filters</h4>
	<?php adinj_condition_tables('random_', 'ui_top_conditions_show'); ?>
	<h4>Bottom ad filters</h4>
	<?php adinj_condition_tables('bottom_', 'ui_top_conditions_show'); ?>
	<h4>Footer ad filters</h4>
	<?php adinj_condition_tables('footer_', 'ui_top_conditions_show'); ?>
	<?php
	adinj_postbox_end();
}

function adinj_insertion_mode_box($ops){
	adinj_postbox_start(__("Ad insertion mode and dynamic ad display restrictions", 'adinj'), 'restrictions'); ?>
	<h4>Ad insertion mode</h4>
	<blockquote>
	<input type="radio" name="ad_insertion_mode" value="mfunc" <?php if (adinj_mfunc_mode()) echo 'checked="checked"'; ?> /> <b>mfunc: Insert ads using cache compatible mfunc tags</b> - Dynamic features will work with WP Super Cache, W3 Total Cache and WP Cache. Only select this mode if you are using one of those caching plugins and want to use dynamic features (IP / referrer restriction, alt content and ad roatation). If you aren't using dynamic features select direct mode.
	<?php if (!is_supported_caching_plugin_active()) {
		echo '<p><b><span style="font-size:10px;color:red;">Note: A supported caching plugin does not appear to be active. If you are not using WP Super Cache / W3 Total Cache / WP Cache you should use one of the direct insertion modes below.</span></b></p>';		
	} ?>
	
	<?php if (!adinj_mfunc_mode()) { ?>
	<script type="text/javascript">
	document.write('<style type="text/css" media="screen">#caching_plugin_msg { display: none; }</style>');
	</script>
	<?php }  ?>
	<br />
	
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
	
	<br />
	
	<input type="radio" name="ad_insertion_mode" value="direct" <?php if (adinj_direct_mode()) echo 'checked="checked"'; ?> /> <b>direct: Direct ad code insertion</b> - Select this if you are not using an mfunc compatible caching plugin OR if you are not using the dynamic features.<br />
	</blockquote>

	<p></p>
	
	<script type="text/javascript">
	jQuery(document).ready(function(){
	jQuery('input[name=ad_insertion_mode]:radio').change(function() {
		if (jQuery('input[name=ad_insertion_mode]:checked').val() == "direct"){
			jQuery('.caching_plugin_msg').slideUp(300);
		} else { // mfunc
			jQuery('.caching_plugin_msg').slideDown(300);
		}
		return true;
		});
	});
	</script>
	
	
	<h4><a name="dynamic"></a>Show ads only to visitors from these search engines or websites (dynamic feature)</h4>
	
	<blockquote>
	<?php adinj_add_checkbox('sevisitors_only') ?><?php _e("Only show ads to visitors from these websites (customise search engine or website referrers below). When a user visits from one of these referrers they will see ads <b>for the next hour</b> (sets a 1 hour cookie).", 'adinj') ?><br />
	<textarea name="ad_referrers" rows="2" cols="<?php adinj_table_width('dynamic'); ?>"><?php echo $ops['ad_referrers']; ?></textarea>
	<p>Comma separated list e.g.: <br /><code>.google., .bing., .yahoo., .ask., search?, search., facebook.com, t.co</code></p>
	</blockquote>

	
	<h4>Blocked referrer keywords (dynamic feature)</h4>
	
	<blockquote>
	<?php adinj_add_checkbox('block_keywords') ?><?php _e("Don't show ads to visitors from external sites if the referrer contains one of these keywords or substrings.", 'adinj') ?> If a blocked string is found block ads for: <?php	adinj_selection_box("block_ads_for_hours", array('1'=>'1 hour', '2'=>'2 hours', '3'=>'3 hours', '6'=>'6 hours', '24'=>'1 day', '48'=>'2 days', '72'=>'3 days', '168'=>'1 week', '720'=>'30 days', '8760'=>'1 year')); ?><br />
	<textarea name="blocked_keywords" rows="2" cols="<?php adinj_table_width('dynamic'); ?>"><?php echo $ops['blocked_keywords']; ?></textarea>
	<p>Comma separated list e.g.: <br /><code>myblogname.com, .ru, .in, james+bond</code></p>
	</blockquote>

	
	<h4>Blocked IP addresses (dynamic feature)</h4>
	
	<blockquote>
	<?php adinj_add_checkbox('block_ips') ?><?php _e("Exclude ads from these IP addresses.", 'adinj') ?><br />
	<textarea name="blocked_ips" rows="4" cols="<?php adinj_table_width('dynamic'); ?>"><?php echo $ops['blocked_ips']; ?></textarea>
	<p>Comma separated list e.g.: <br /><code>0.0.0.1, 0.0.0.2</code></p>
	<p>Or you can list one IP per line with optional comments e.g.</p>
	<code style="padding:0px 0px">192.168.0.1<br />0.0.0.2<br /><?php echo $_SERVER['REMOTE_ADDR'] ?> //my ip<br />0.0.0.3</code>
	
	<p>For reference your current IP address is <code><?php echo $_SERVER['REMOTE_ADDR'] ?></code></p>
	</blockquote>

	
	
	
	<?php adinj_postbox_end();
}

function adinj_add_exclude_row($name, $prefix=''){
	$all = '';
	if ($name == 'All ads') $all='all';
?>
	<tr>
		<td><b><?php echo $name; ?></b></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_single', 'adinj_single', $all) ?></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_page', 'adinj_page', $all) ?></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_home', 'adinj_home', $all) ?></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_archive', 'adinj_archive', $all) ?></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_front', 'adinj_front', $all) ?></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_404', 'adinj_404', $all) ?></td>
		<td><?php adinj_add_checkbox($prefix.'exclude_search', 'adinj_search', $all) ?></td>
	</tr>
<?php
}

function adinj_random_ad_limit_table(){
	?>
	<tr><td>&nbsp;<b>|_</b> No ads if post shorter than:</td>
	<?php
	$prefixes = array("", "home", "archive");
	$unit = adinj_counting_unit_description();
	$ad_limit_settings = array('d',100,200,300,500,750,1000,1500,2000,2500,3000,5000,10000,15000,20000);
	
	foreach ($prefixes as $prefix){
		echo '<td><div class="adinj_'.$prefix.'">';
		if (!empty($prefix)) $prefix = $prefix.'_';
		adinj_selection_box($prefix."no_random_ads_if_shorter_than", $ad_limit_settings, $unit);
		echo '</div></td>';
	} ?>
	</tr>
	<tr><td>&nbsp;<b>|_</b> Only 1 ad if post shorter than:</td>
	<?php
	foreach ($prefixes as $prefix){
		echo '<td><div class="adinj_'.$prefix.'">';
		if (!empty($prefix)) $prefix = $prefix.'_';
		adinj_selection_box($prefix."one_ad_if_shorter_than", $ad_limit_settings, $unit);
		echo '</div></td>';
	} ?>
	</tr>
	<tr><td>&nbsp;<b>|_</b> Only 2 ads if post shorter than:</td>
	<?php
	foreach ($prefixes as $prefix){
		echo '<td><div class="adinj_'.$prefix.'">';
		if (!empty($prefix)) $prefix = $prefix.'_';
		adinj_selection_box($prefix."two_ads_if_shorter_than", $ad_limit_settings, $unit);
		echo '</div></td>';
	}
	?>
	</tr>
	<tr><td>&nbsp;<b>|_</b> Only 3 ads if post shorter than:</td>
	<?php
	foreach ($prefixes as $prefix){
		echo '<td><div class="adinj_'.$prefix.'">';
		if (!empty($prefix)) $prefix = $prefix.'_';
		adinj_selection_box($prefix."three_ads_if_shorter_than", $ad_limit_settings, $unit);
		echo '</div></td>';
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
			<b>Ads on/off summary</b><br />
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
				<td style="text-align:right"><b><a href="#topadplacement">Top</a></b></td>
				<td><?php echo adinj_count_live_ads('top', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('top_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('top', 'single') ?></td>
				<td><?php adinj_print_ad_dot('top', 'page') ?></td>
				<td><?php adinj_print_ad_dot('top', 'home') ?></td>
				<td><?php adinj_print_ad_dot('top', 'archive') ?></td>
			</tr>
			<tr>
				<td style="text-align:right"><b><a href="#randomadplacement">Random</a></b></td>
				<td><?php echo adinj_count_live_ads('random', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('random_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('random', 'single') ?></td>
				<td><?php adinj_print_ad_dot('random', 'page') ?></td>
				<td><?php adinj_print_ad_dot('random', 'home') ?></td>
				<td><?php adinj_print_ad_dot('random', 'archive') ?></td>
			</tr>
			<tr>
				<td style="text-align:right"><b><a href="#bottomadplacement">Bottom</a></b></td>
				<td><?php echo adinj_count_live_ads('bottom', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('bottom_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'single') ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'page') ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'home') ?></td>
				<td><?php adinj_print_ad_dot('bottom', 'archive') ?></td>
			</tr>
			<tr>
				<td style="text-align:right"><b><a href="#footeradplacement">Footer</a></b></td>
				<td><?php echo adinj_count_live_ads('footer', $ops); ?></td>
				<td><?php echo adinj_count_live_ads('footer_alt', $ops); ?></td>
				<td><?php adinj_print_ad_dot('footer', 'single') ?></td>
				<td><?php adinj_print_ad_dot('footer', 'page') ?></td>
				<td><?php adinj_print_ad_dot('footer', 'home') ?></td>
				<td><?php adinj_print_ad_dot('footer', 'archive') ?></td>
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
			<tr>
				<td style="text-align:right"><b>Template</b></td>
				<td></td>
				<td></td>
				<td><?php adinj_print_ad_dot('template', 'single') ?></td>
				<td><?php adinj_print_ad_dot('template', 'page') ?></td>
				<td><?php adinj_print_ad_dot('template', 'home') ?></td>
				<td><?php adinj_print_ad_dot('template', 'archive') ?></td>
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