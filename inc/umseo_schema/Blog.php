<?php

defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of Blog
 *
 * @author mark
 */
class UmseoSchema_Blog extends UmseoSchema_Thing
{
	public string $schemaType = "Blog";

	public function getResource( $pretty = false )
	{
		if (is_front_page() && is_home() || is_front_page())
		{
			$Headline = get_bloginfo('name');
			$Permalink = home_url();
		} else
		{
			$Headline = get_the_title(get_option('page_for_posts'));
			$Permalink = get_permalink(get_option('page_for_posts'));
		}

		$this->schema = array(
			'@context' => 'http://schema.org/',
			'@type' => $this->schemaType,
			'@id' => $Permalink . '#' . $this->schemaType,
			'headline' => $Headline,
			'description' => get_bloginfo('description'),
			'url' => $Permalink,
		);

		return $this->toJson($this->schema, $pretty);
	}
}
