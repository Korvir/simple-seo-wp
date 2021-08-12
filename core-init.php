<?php
// If this file is called directly, abort. //
if (!defined('WPINC'))
{
	die;
} // end if

/*
*	***** UmTale SEO *****
*	This file initializes all UMSEO Core components
*/
define('UMSEO_CORE_INC', dirname(__FILE__) . '/inc/');
define('UMSEO_CORE_IMG', plugins_url('assets/img/', __FILE__));
define('UMSEO_CORE_CSS', plugins_url('assets/css/', __FILE__));
define('UMSEO_CORE_JS', plugins_url('assets/js/', __FILE__));

/*
*  Register CSS
*/
// function umseo_register_core_css()
// {
// 	wp_enqueue_style('umseo-core', UMSEO_CORE_CSS . 'umseo-core.css',null,time('s'),'all');
// }

// add_action( 'wp_enqueue_scripts', 'umseo_register_core_css' );

/*
*
*  Register JS/Jquery Ready
*
*/
// function umseo_register_core_js()
// {
// 	// Register Core Plugin JS
// 	wp_enqueue_script('umseo-core', UMSEO_CORE_JS . 'umseo-core.js','jquery',time(),true);
// };

// add_action( 'wp_enqueue_scripts', 'umseo_register_core_js' );

/*
*  Includes
*/
include_once UMSEO_CORE_INC . 'umseo-core-functions.php';
include_once UMSEO_CORE_INC . 'modules/class.umseo-permalinks.php';
include_once UMSEO_CORE_INC . 'modules/class.umseo-canonical.php';
include_once UMSEO_CORE_INC . 'modules/class.umseo-title.php';
include_once UMSEO_CORE_INC . 'modules/class.umseo-description.php';
include_once UMSEO_CORE_INC . 'modules/class.umseo-opengraph.php';
include_once UMSEO_CORE_INC . 'modules/class.umseo-jsonld.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Thing.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Blog.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Tag.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Author.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Category.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Search.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Page.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Post.php';
include_once UMSEO_CORE_INC . 'umseo_schema/Product.php';

new UmSEO_Permalinks;
new UmSEO_Canonical;
new UmSEO_Title;
new UmSEO_Description;
new UmSEO_OpenGraph;
new UmSEO_JsonLD;

add_action('after_setup_theme', 'umseo_init', 99);

function umseo_init()
{
	if (!class_exists('Carbon_Fields\\Carbon_Fields'))
	{
		require_once( 'vendor/autoload.php' );
		\Carbon_Fields\Carbon_Fields::boot();
	}

	if (is_admin())
	{
		include_once UMSEO_CORE_INC . 'class.settings-api.php';
		include_once UMSEO_CORE_INC . 'class.umseo-settings.php';
		new UmSEO_Settings;
	}
}

add_action('wp_head', 'umseo_head', 1);
add_action('amp_post_template_head', 'umseo_head', 1);

function umseo_head()
{
	do_action('umseo_head');
}
