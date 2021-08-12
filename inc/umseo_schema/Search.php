<?php defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of Search
 *
 * @author mark
 */
class UmseoSchema_Search extends UmseoSchema_Thing
{

	public string $schemaType = "SearchResultsPage";

	public function getResource( $pretty = false )
	{
		$this->schema = array(
			'@context' => 'http://schema.org/',
			'@type' => $this->schemaType,
			'@id' => get_search_link() . '#' . $this->schemaType,
		);

		return $this->toJson($this->schema, $pretty);
	}
}
