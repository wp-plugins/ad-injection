<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

if (!is_admin()) return;

function adinj_tab_adrotation(){
	$ops = adinj_options();
	
	echo <<<DOCS
	<p><a href="#multiple_random">Random adverts</a> | <a href="#multiple_top">Top adverts</a> | <a href="#multiple_bottom">Bottom adverts</a></p>
	
	<p>Ad rotation / split testing and alternate content are advanced features. If you don't understand these features you probably don't need them and can therefore ignore this tab. In summary:</p>
	
	<ul>
	<li><b>Ad rotation / split testing:</b> You can define multiple adverts for the same ad space which are rotated according to the ratios you define. The percentage of views that each ad will be shows is displated beneath the ratio text box. For example if you define two ads and set both to have a ratio of '50' they will each be shown (roughly) 50% of the time. The numbers don't have to add up to 100 as the ratio is calculated based on the total. e.g. if you have two advert - one is set with a ratio of '1' and the other '3' the ratios will be 25% and 75%. Please remember this isn't strict ad rotation, it is random selection based on ratios so the ratios will be correct over a large sample of ad views, not a small sample.</li>
	<li><b>Alternate content:</b> This is content that is displayed when ads are blocked for the user. Ads can only  be blocked for specific users if you use one of the dynamic modes (mfunc or direct_dynamic). You could use this alternate content to show other content, some kind of layout filler, or even a different type of ad. I've added support for rotation of alternate content as well.</li>
	</ul>

DOCS;
	
	$total_rand_split = adinj_total_split('ad_code_random_', $ops);
	$total_rand_alt_split = adinj_total_split('ad_code_random_alt_', $ops);
	
	$total_top_split = adinj_total_split('ad_code_top_', $ops);
	$total_top_alt_split = adinj_total_split('ad_code_top_alt_', $ops);
	
	$total_bottom_split = adinj_total_split('ad_code_bottom_', $ops);
	$total_bottom_alt_split = adinj_total_split('ad_code_bottom_alt_', $ops);
	?>
	
	<style type="text/css">
	.adinjtable td { vertical-align: top; }
	</style>
	
	<?php
	adinj_postbox_start(__("Random adverts", 'adinj'), 'multiple_random');
	echo '<table border="0" cellspacing="5" class="adinjtable">';
	for ($i=1; $i<=10; ++$i){
		adinj_add_row_with_text_box('ad_code_random_', $i, 'Ad code', $total_rand_split);
	}
	adinj_add_row_with_text_box('ad_code_random_alt_', 1, 'Alt content', $total_rand_alt_split);
	adinj_add_row_with_text_box('ad_code_random_alt_', 2, 'Alt content', $total_rand_alt_split);
	echo '</table>';
	adinj_postbox_end();

	
	adinj_postbox_start(__("Top adverts", 'adinj'), 'multiple_top');
	echo '<table border="0" cellspacing="5" class="adinjtable">';
	for ($i=1; $i<=10; ++$i){
		adinj_add_row_with_text_box('ad_code_top_', $i, 'Ad code', $total_top_split);
	}
	adinj_add_row_with_text_box('ad_code_top_alt_', 1, 'Alt content', $total_top_alt_split);
	adinj_add_row_with_text_box('ad_code_top_alt_', 2, 'Alt content', $total_top_alt_split);
	echo '</table>';
	adinj_postbox_end();

	
	adinj_postbox_start(__("Bottom adverts", 'adinj'), 'multiple_bottom');
	echo '<table border="0" cellspacing="5" class="adinjtable">';
	for ($i=1; $i<=10; ++$i){
		adinj_add_row_with_text_box('ad_code_bottom_', $i, 'Ad code', $total_bottom_split);
	}
	adinj_add_row_with_text_box('ad_code_bottom_alt_', 1, 'Alt content', $total_bottom_alt_split);
	adinj_add_row_with_text_box('ad_code_bottom_alt_', 2, 'Alt content', $total_bottom_alt_split);
	echo '</table>';
	adinj_postbox_end();
	
}

function adinj_add_row_with_text_box($name_stem, $num, $title, $total){
	$ops = adinj_options();
	$name = $name_stem.$num;
	$namesplit = $name.'_split';
	$percent = adinj_percentage_split($name_stem, $num, $ops, $total);
echo <<<EOT
	<tr><td>
	<a name="$name"></a>
	<span style="font-size:10px;"><b>$title $num</b></span><br />
	<textarea name="$name" rows="8" cols="60">$ops[$name]</textarea>
	</td><td>
	<input name="$namesplit" size="7" value="$ops[$namesplit]" />
	<br />
	$percent
	</td></tr>
EOT;
}


?>