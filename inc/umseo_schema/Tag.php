<?php

defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of Category
 *
 * @author mark
 */
class UmseoSchema_Tag extends UmseoSchema_Thing
{
	public string $schemaType = "CollectionPage";

	public function getResource( $pretty = false )
	{
		$hasPart = array();

		$this->schema = array(
			'@context' => 'http://schema.org/',
			'@type' => $this->schemaType,
			'@id' => get_tag_link(get_query_var('tag_id')) . '#' . $this->schemaType,
			'headline' => single_tag_title('', false),
			'description' => strip_tags(tag_description()),
			'url' => get_tag_link(get_query_var('tag_id')),
			// 'hasPart' => $hasPart
		);

		return $this->toJson($this->schema, $pretty);
	}

}
