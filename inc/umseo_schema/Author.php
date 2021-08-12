<?php

defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of Author Page
 *
 * @author mark
 */
class UmseoSchema_Author extends UmseoSchema_Thing
{

	public string $schemaType = "ProfilePage";

	public function __construct()
	{

	}

	public function getResource( $pretty = false )
	{
		global $post;

		$this->schema = array
		(
			'@context' => 'http://schema.org/',
			'@type' => $this->schemaType,
			'@id' => esc_url(get_author_posts_url(get_the_author_meta('ID', $post->post_author))) . '#' . $this->schemaType,
			'headline' => sprintf('About %s', get_the_author()),
			'datePublished' => get_the_date('Y-m-d'),
			'dateModified' => get_the_modified_date('Y-m-d'),
			'about' => $this->getAuthor(),
		);

		return $this->toJson($this->schema, $pretty);
	}

}
