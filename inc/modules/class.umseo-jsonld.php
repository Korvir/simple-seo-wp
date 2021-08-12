<?php

class UmSEO_JsonLD
{

	function __construct()
	{
		add_action('umseo_head', [ $this, 'head_jsonld_output' ]);
	}

	public static function get_settings()
	{
		return [
			[
				'desc' => '<h3>Default image</h3>',
				'type' => 'html',
				'name' => 'umseo_jsonld_def_image_desc',
			],
			[
				'name' => 'umseo_jsonld_default_image',
				'label' => __('Image', 'umseo'),
				'type' => 'file',
			],
			[
				'name' => 'umseo_jsonld_default_image_width',
				'label' => __('Image width', 'umseo'),
				'type' => 'number',
			],
			[
				'name' => 'umseo_jsonld_default_image_height',
				'label' => __('Image height', 'umseo'),
				'type' => 'number',
			],
			[
				'desc' => '<h3>Publisher settings</h3>',
				'type' => 'html',
				'name' => 'umseo_jsonld_publisher_desc',
			],
			[
				'name' => 'umseo_jsonld_publisher_type',
				'label' => __('Type', 'umseo'),
				'type' => 'select',
				'default' => 'Person',
				'options' => [
					'Person' => 'Person',
					'Organization' => 'Organization',
				]
			],
			[
				'name' => 'umseo_jsonld_publisher_name',
				'label' => __('Name', 'umseo'),
				'type' => 'text',
				'default' => get_bloginfo('name'),
			],
			[
				'name' => 'umseo_jsonld_publisher_url',
				'label' => __('URL', 'umseo'),
				'type' => 'text',
				'default' => home_url(),
			],
			[
				'name' => 'umseo_jsonld_publisher_image',
				'label' => __('Image', 'umseo'),
				'type' => 'file',
			],
			[
				'name' => 'umseo_jsonld_publisher_image_width',
				'label' => __('Image width', 'umseo'),
				'type' => 'number',
			],
			[
				'name' => 'umseo_jsonld_publisher_image_height',
				'label' => __('Image height', 'umseo'),
				'type' => 'number',
			],
		];
	}

	public function head_jsonld_output()
	{
		if (is_404())
		{
			return;
		}

		$factory = UmseoSchema_Thing::factory();
		$SchemaMarkupWebSite = $factory->getWebSite();
		$breadcrumbSchema = false;
		$SchemaMarkup = $factory->getResource();

		if (method_exists($factory, 'getBreadcrumb'))
		{
			$breadcrumbSchema = $factory->getBreadcrumb();
		}
		printf('<script type="application/ld+json" data-schema="Website">%s</script>' . "\n", $SchemaMarkupWebSite);
		printf('<script type="application/ld+json" data-schema="Breadcrumb">%s</script>' . "\n", $breadcrumbSchema);

		if (!$factory->schemaType || !$SchemaMarkup) return;

		if (is_array($SchemaMarkup) && isset($SchemaMarkup['a']) && isset($SchemaMarkup['n']))
		{
			printf('<script type="application/ld+json" data-schema="Article">%s</script>' . "\n", $SchemaMarkup['a']);
			printf('<script type="application/ld+json" data-schema="NewsArticle">%s</script>' . "\n", $SchemaMarkup['n']);

		} elseif ( is_array($SchemaMarkup) && isset($SchemaMarkup['p']) )
		{
			printf('<script type="application/ld+json" data-schema="Product">%s</script>' . "\n", $SchemaMarkup['p']);
		}
		else
		{
			printf('<script type="application/ld+json" data-schema="%s">%s</script>' . "\n", $factory->schemaType, $SchemaMarkup);
		}
	}
}
