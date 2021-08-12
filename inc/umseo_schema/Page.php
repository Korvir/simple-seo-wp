<?php

defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of Article
 *
 * @author mark
 */
class UmseoSchema_Page extends UmseoSchema_Thing
{
	/**
	 * Get Default Schema.org for Resource
	 *
	 * @param bool $pretty
	 * @return array|false|string string
	 */
	public function getResource( $pretty = false )
	{
		global $post;

		$Permalink = UmSEO_Canonical::get_canonical_url();

		$MarkupTypeDefault = 'Article';
		$MarkupType = get_post_meta($post->ID, '_HunchSchemaType', true);
		$this->schemaType = $MarkupType ? $MarkupType : $MarkupTypeDefault;

		$this->schema = [
			'@context' => 'http://schema.org/',
			'@type' => $this->schemaType,
			'@id' => $Permalink . '#' . $this->schemaType,
			'mainEntityOfPage' => [
				'@type' => 'WebPage',
				'@id' => $Permalink,
			],
			'headline' => apply_filters('umseo_headline', get_the_title(), 'page'),
			'alternativeHeadline' => apply_filters('umseo_headline', get_the_title(), 'page'),
			'name' => get_the_title(),
			'articleBody' => umseo_excerpt([ 'maxchar' => 999999, 'autop' => false ]),
			'inLanguage' => 'en-US',
			'description' => $this->getExcerpt(),
			'datePublished' => get_the_date('Y-m-d H:m'),
			'dateModified' => get_the_modified_date('Y-m-d H:m'),
			'author' => $this->getAuthor(),
			'publisher' => $this->getPublisher(),
			'image' => $this->getImage(),
			// 'video' => $this->getVideos(),
			'url' => $Permalink,
		];

		if (get_comments_number())
		{
			$this->schema['commentCount'] = get_comments_number();
			$this->schema['comment'] = $this->getComments();
		}

		if ($this->schemaType == 'Article' && is_front_page())
		{
			$this->schema = '';
			return false;
		}

		return $this->toJson($this->schema, $pretty);
	}


	/**
	 * @param false $Pretty
	 * @return bool|string
	 */
	public function getBreadcrumb( $Pretty = false )
	{
		global $post;

		$BreadcrumbPosition = 1;
		$Permalink = UmSEO_Canonical::get_canonical_url();
		$this->SchemaBreadcrumb['@context'] = 'http://schema.org';
		$this->SchemaBreadcrumb['@type'] = 'BreadcrumbList';

		$this->SchemaBreadcrumb['itemListElement'][] = array
		(
			'@type' => 'ListItem',
			'position' => $BreadcrumbPosition++,
			'item' => array
			(
				'@id' => home_url('/#breadcrumbitem'),
				'name' => 'Home',
				// 'name' => get_bloginfo( 'name' ),
			),
		);

		if ($post->post_parent)
		{
			$Ancestors = array_reverse(get_post_ancestors($post->ID));

			foreach ($Ancestors as $PostId)
			{
				$this->SchemaBreadcrumb['itemListElement'][] = array
				(
					'@type' => 'ListItem',
					'position' => $BreadcrumbPosition++,
					'item' => array
					(
						'@id' => get_permalink($PostId) . "#breadcrumbitem",
						'name' => get_the_title($PostId),
					),
				);
			}
		}

		if (!is_front_page())
		{
			$this->SchemaBreadcrumb['itemListElement'][] = array
			(
				'@type' => 'ListItem',
				'position' => $BreadcrumbPosition++,
				'item' => array
				(
					'@id' => $Permalink . "#breadcrumbitem",
					'name' => get_the_title(),
				),
			);
		}

		return $this->toJson($this->SchemaBreadcrumb, $Pretty);
	}
}
