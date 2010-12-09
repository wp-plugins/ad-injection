<?php
/*
Part of the Ad Injection plugin for WordPress
http://www.reviewmylife.co.uk/
*/

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();

delete_option('adinj_options');

$adinj_dir = dirname(__FILE__);
if (file_exists($adinj_dir.'/../../ad-injection-config.php')) {
	unlink($adinj_dir.'/../../ad-injection-config.php');
}

?>