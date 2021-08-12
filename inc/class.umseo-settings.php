<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

class UmSEO_Settings
{

	private $settings_api;

	function __construct()
	{
		$this->settings_api = new UmTale_Settings_API;
		add_action('admin_init', array( $this, 'admin_init' ));
		add_action('admin_menu', array( $this, 'admin_menu' ));
		add_action('carbon_fields_register_fields', array( $this, 'term_metabox' ));
		add_action('carbon_fields_register_fields', array( $this, 'post_metabox' ));
	}

	function admin_init()
	{

		//set the settings
		$this->settings_api->set_sections($this->get_settings_sections());
		$this->settings_api->set_fields($this->get_settings_fields());

		//initialize settings
		$this->settings_api->admin_init();
	}

	function admin_menu()
	{
		add_options_page('UmtaleSEO', 'UmtaleSEO', 'delete_posts', 'umseo-settings', [ $this, 'plugin_page' ]);
	}

	function get_settings_sections()
	{
		$sections = [
			[
				'id' => 'umseo_permalinks',
				'title' => __('Permalinks', 'umseo')
			],
			[
				'id' => 'umseo_canonicalizer',
				'title' => __('Canonicalizer', 'umseo')
			],
			[
				'id' => 'umseo_title_tag_rewriter',
				'title' => __('Title Tag Rewriter', 'umseo')
			],
			[
				'id' => 'umseo_meta_description',
				'title' => __('Meta Description', 'umseo')
			],
			[
				'id' => 'umseo_jsonld',
				'title' => __('JSON-LD', 'umseo')
			],
			[
				'id' => 'umseo_opengraph',
				'title' => __('Open Graph', 'umseo')
			],
		];

		return $sections;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields()
	{
		$settings_fields = [
			'umseo_permalinks' => [
				[
					'name' => 'nobase_category',
					'label' => __('Remove the URL bases of Categories', 'umseo'),
					'type' => 'checkbox',
				],
				[
					'name' => 'nobase_post_tag',
					'label' => __('Remove the URL bases of Tags', 'umseo'),
					'type' => 'checkbox',
				],
			],
			'umseo_canonicalizer' => [
				[
					'name' => 'generate_canonical',
					'label' => __('Canonical URL Generation', 'umseo'),
					'desc' => 'Generate <code>&lt;link&nbsp;rel="canonical"&nbsp;/&gt;</code> meta tags',
					'type' => 'checkbox',
				],
			],
			'umseo_title_tag_rewriter' => UmSEO_Title::get_settings(),
			'umseo_meta_description' => UmSEO_Description::get_settings(),
			'umseo_opengraph' => UmSEO_OpenGraph::get_settings(),
			'umseo_jsonld' => UmSEO_JsonLD::get_settings(),
		];

		return $settings_fields;
	}

	function plugin_page()
	{
		echo '<div class="wrap">';

		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}

	public function term_metabox()
	{
		Container::make('term_meta', __('SEO'))
			->where('term_taxonomy', '=', 'category')
			->add_fields([
				Field::make('text', 'umseo_title', __('SEO Title'))
					->set_attribute('placeholder', umseo_get_option('umseo_title_category_format', 'umseo_title_tag_rewriter', '')),
				Field::make('textarea', 'umseo_description', __('SEO Description'))
					->set_attribute('placeholder', umseo_get_option('umseo_desc_category_format', 'umseo_meta_description', ''))
			]);

		Container::make('term_meta', __('SEO'))
			->where('term_taxonomy', '=', 'post_tag')
			->add_fields([
				Field::make('text', 'umseo_title', __('SEO Title'))
					->set_attribute('placeholder', umseo_get_option('umseo_title_tag_format', 'umseo_title_tag_rewriter', '')),
				Field::make('textarea', 'umseo_description', __('SEO Description'))
					->set_attribute('placeholder', umseo_get_option('umseo_desc_tag_format', 'umseo_meta_description', ''))
			]);
	}

	public function post_metabox()
	{
		Container::make('post_meta', __('SEO'))
			->add_fields([
				Field::make('text', 'umseo_title', __('SEO Title'))
					->set_attribute('placeholder', umseo_get_option('umseo_title_post_format', 'umseo_title_tag_rewriter', '')),
				Field::make('textarea', 'umseo_description', __('SEO Description'))
					->set_attribute('placeholder', umseo_get_option('umseo_desc_post_format', 'umseo_meta_description', ''))
			]);
	}
}
