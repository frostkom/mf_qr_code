<?php
/*
Plugin Name: MF QR Code
Plugin URI: https://github.com/frostkom/mf_qr_code
Description:
Version: 1.1.1
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_qr_code
Domain Path: /lang
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_qr_code = new mf_qr_code();

	add_action('cron_base', array($obj_qr_code, 'cron_base'), mt_rand(1, 10));

	add_action('init', array($obj_qr_code, 'init'));

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_qr_code');

		add_action('admin_init', array($obj_qr_code, 'admin_init'));

		add_filter('manage_page_posts_columns', array($obj_qr_code, 'column_header'), 5);
		add_action('manage_page_posts_custom_column', array($obj_qr_code, 'column_cell'), 5, 2);

		if(wp_doing_ajax())
		{
			add_action('wp_ajax_api_qr_code_image', array($obj_qr_code, 'api_qr_code_image'));
		}
	}

	function uninstall_qr_code()
	{
		include_once("include/classes.php");

		$obj_qr_code = new mf_qr_code();

		mf_uninstall_plugin(array(
			'uploads' => $obj_qr_code->post_type,
		));
	}
}