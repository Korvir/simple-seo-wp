<?php
/*
Plugin Name: Simple SEO WP
Description: Speed optimized SEO plugin for Wordpress
Version: 0.1.2
Author: korvir
Text Domain: umseo
*/

if (!defined('WPINC'))
{
	die;
}

// Let's Initialize Everything
if (file_exists(plugin_dir_path(__FILE__) . 'core-init.php'))
{
	require_once( plugin_dir_path(__FILE__) . 'core-init.php' );
}
