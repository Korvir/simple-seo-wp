<?php

defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of Category
 *
 * @author mark
 */
class UmseoSchema_Category extends UmseoSchema_Thing
{

	public string $schemaType = "CollectionPage";

	public function getResource( $pretty = false )
	{
		$hasPart = array();

		$this->schema = array(
			'@context' => 'http://schema.org/',
			'@type' => $this->schemaType,
			'@id' => get_category_link(get_query_var('cat')) . '#' . $this->schemaType,
			'headline' => single_cat_title('', false),
			'description' => category_description(),
			'url' => get_category_link(get_query_var('cat')),
			// 'hasPart' => $hasPart
		);

		return $this->toJson($this->schema, $pretty);
	}
}
