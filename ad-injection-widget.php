<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

// 1 = original
// 2 = split testing / alt content
define('ADINJ_WIDGET_DB_VERSION', 2);

class Ad_Injection_Widget extends WP_Widget {
	function Ad_Injection_Widget() {
		$widget_ops = array( 'classname' => 'adinjwidget', 'description' => 'Insert Ad Injection adverts into your sidebars/widget areas.' );
		$control_ops = array( 'width' => 450, 'height' => 300, 'id_base' => 'adinj' );
		$this->WP_Widget( 'adinj', 'Ad Injection', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		if (adinj_ads_completely_disabled_from_page()) return;
		
		if ((is_front_page() && adinj_ticked('widget_exclude_front')) ||
			(is_home() && adinj_ticked('widget_exclude_home')) ||
			(is_page() && adinj_ticked('widget_exclude_page')) ||
			(is_single() && adinj_ticked('widget_exclude_single')) ||
			(is_archive() && adinj_ticked('widget_exclude_archive'))){
			return;
		}

		extract( $args );
		
		$ops = adinj_options();
		
		$include = "";
		if ($ops['ad_insertion_mode'] == 'mfunc'){
			$include = adinj_ad_code_include();
		}
		
		$title = apply_filters('widget_title', $instance['title'] );
		
		// The old 'non upgraded' db will be passed here if the widget hasn't
		// been resaved. We can't upgrade as doing so would mean we'd have to
		// re-save the widget files - which we can't do as we can't re-write 
		// the settings back to the db (at least not without a bit more work)
		$adcode = adinj_get_ad_code('widget_'.$this->get_id(), $instance);

		echo $before_widget;
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}		
			
		if ( !empty($adcode) ){
			echo $include;
			echo $adcode;
		}

		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ){
		$updated = $this->adinj_upgrade_widget_db($new_instance, $old_instance);
		
		// Only strip tags when potential for updated title
		$updated['title'] = strip_tags( $new_instance['title'] );

		write_ad_to_file($updated['advert_1'], $this->get_ad_file_path(1));
		write_ad_to_file($updated['advert_2'], $this->get_ad_file_path(2));
		write_ad_to_file($updated['advert_3'], $this->get_ad_file_path(3));
		write_ad_to_file($updated['advert_alt_1'], $this->get_alt_file_path(1));
		write_ad_to_file($updated['advert_alt_2'], $this->get_alt_file_path(2));
		
		return $updated;
	}
	
	function default_options(){
		/* Set up some default widget settings. */
		return array(
			'title' => '', 
			//adverts
			'advert_1' => '', 
			'advert_1_split' => '100',
			'advert_2' => '', 
			'advert_2_split' => '100',
			'advert_3' => '', 
			'advert_3_split' => '100',
			'advert_alt_1' => '', 
			'advert_alt_1_split' => '100', 
			'advert_alt_2' => '', 
			'advert_alt_2_split' => '100', 
			//settings
			'margin_top' => ADINJ_DISABLED,
			'margin_bottom' => ADINJ_DISABLED,
			'padding_top' => ADINJ_DISABLED,
			'padding_bottom' => ADINJ_DISABLED,
			//ui
			'ui_ad_1_show' => 'true',
			'ui_ad_2_show' => 'false',
			'ui_ad_3_show' => 'false',
			'ui_alt_1_show' => 'false',
			'ui_alt_2_show' => 'false',
			//
			'db_version' => '2'
			);
	}
	
	function adinj_upgrade_widget_db($new_instance, $old_instance){
		// Copy old values across to default
		$updated = $this->default_options();
		foreach ($updated as $key => $value){
			if (array_key_exists($key, $new_instance)){
				$updated[$key] = $new_instance[$key];
			}
		}
		
		// Upgrade any options as necessary
		$old_dbversion = adinj_db_version($old_instance);
		if ($old_dbversion == 1){
			$updated['advert_1'] = $old_instance['advert'];
		}
			
		$updated['db_version'] = ADINJ_WIDGET_DB_VERSION;
	
		return $updated;
	}
	
	function form( $instance ) {
		$instance = $this->adinj_upgrade_widget_db($instance, $instance);
		
		$total_ad_split = adinj_total_split('advert_', $instance);
		$total_alt_split = adinj_total_split('advert_alt_', $instance);
		
		?>
		
		<input type='hidden' <?php $this->add_id_and_name('db_version'); ?> value='<?php echo $defaults['db_version']; ?>' />
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		<br />
			<span style="font-size:10px;">Make sure any label complies with your ad provider's TOS. More info for <a href="http://adsense.blogspot.com/2007/04/encouraging-clicks.html" target="_new">AdSense</a> users.</span>
		</p>

		<style type="text/css">
		.adinjtable td { vertical-align: top; }
		</style>
		
		<table border="0" cellspacing="5" width="100% "class="adinjtable">
		<?php
		$this->add_row('advert_', 1, 'Ad code 1', 'ui_ad_1_show', $total_ad_split, $instance);
		?>
		<tr><td>
			<?php adinj_add_margin_top_bottom_options('widget_', $instance, $this->get_field_name('margin_top'), $this->get_field_name('margin_bottom') ); ?>
		</td><td>
			<?php adinj_add_padding_top_bottom_options('widget_', $instance, $this->get_field_name('padding_top'), $this->get_field_name('padding_bottom') ); ?>
		</td></tr>
		
		<?php
		$this->add_row('advert_', 2, 'Ad code 2', 'ui_ad_2_show', $total_ad_split, $instance);
		$this->add_row('advert_', 3, 'Ad code 3', 'ui_ad_3_show', $total_ad_split, $instance);
		$this->add_row('advert_alt_', 1, 'Alt content 1', 'ui_alt_1_show', $total_alt_split, $instance);
		$this->add_row('advert_alt_', 2, 'Alt content 2', 'ui_alt_2_show', $total_alt_split, $instance);
		?>
		
		</table>
		
		<p>Other options to define who sees these adverts (by page age, IP, referrer) are on the main <a href='options-general.php?page=ad-injection.php'>Ad Injection settings page</a>. The title will however always be displayed. If you want the title to be dynamic as well you should embed it in the ad code text box.</p>
		
		<p>You can also set which <a href='options-general.php?page=ad-injection.php#widgets'>page types</a> the widgets appear on.</p>
		
		<?php
	}
	
	function add_row($op_stem, $num, $label, $show_op, $total_split, $ops){
		$op = $op_stem.$num;
		$op_split = $op.'_split';
		$anchorid = $op_stem.uniqid().'_'.$num;
		$anchorclick = $anchorid.'_click';
		$show = $ops[$show_op];
		$hiddenfieldid = $this->get_field_id($show_op);
		$percentage_split = adinj_percentage_split($op_stem, $num, $ops, $total_split);
		?>
		<tr><td colspan='2'>
		<textarea style="float:right" <?php $this->add_id_and_name($op_split); ?> rows="1" cols="4"><?php echo $ops[$op_split]; ?></textarea>
		
		<?php
		echo <<<HTML
		<a href='#' onclick='javascript:$anchorclick();return false;' style='float:left;display:none' id='toggle-$anchorid' class='button'>+/-</a>
		
		<label><b> $label</b></label> <label style='float:right'>(Rotation: $percentage_split)</label>
		
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('a#toggle-$anchorid').show();
			if ('$show' == 'false') jQuery('.$anchorid-box').hide();
		});
		function $anchorclick(){
			jQuery('#$hiddenfieldid').val(jQuery('.$anchorid-box').is(":hidden"));
			jQuery('.$anchorid-box').slideToggle(1000);
		}
		</script>
		
		</td></tr>
		<tr><td colspan='2'>
		
		<div id="$anchorid-box" class="$anchorid-box">
		
HTML;
		
		if ($op_stem == 'advert_' && $num == 2){
			echo '<span style="font-size:10px;">These boxes are for defining rotated adverts which replace the original advert according to the percentages defined. If you want multiple sidebar/widget ads you need to drag another widget into the sidebar.</span><br />';
		}
		if ($op_stem == 'advert_alt_' && $num == 1){
			echo '<span style="font-size:10px;">Here you can define content which is shown if ads are blocked for the viewer (only for mfunc and dynamic ad insertion modes).</span><br />';
		}
		
		?>
		<input type='hidden' <?php $this->add_id_and_name($show_op); ?> value='<?php echo $ops[$show_op]; ?>' />
		<textarea class="widefat" rows="8" cols="50" width="100%" <?php $this->add_id_and_name($op); ?>><?php echo $ops[$op];	?></textarea>
		</div>
		</td></tr>
		<?php
	}
	
	function add_id_and_name($op){
		echo 'id="'.$this->get_field_id($op).'" name="'.$this->get_field_name($op).'"';
	}
	
	function get_ad_file_path($num){
		return ADINJ_AD_PATH.'/'.$this->get_ad_file_name($num);
	}
	
	function get_alt_file_path($num){
		return ADINJ_AD_PATH.'/'.$this->get_alt_file_name($num);
	}
	
	function get_ad_file_name($num){
		return 'ad_widget_'.$this->get_id().'_'.$num.'.txt';
	}
	
	function get_alt_file_name($num){
		return 'ad_widget_'.$this->get_id().'_alt_'.$num.'.txt';
	}
	
	function get_id(){
		//return $widget_id;
		$field = $this->get_field_id('advert_1');
		preg_match('/-(\d+)-/', $field, $matches);
		return $matches[1];
	}
}

?>