<?php defined('ABSPATH') or die('This script cannot be accessed directly.');

/**
 * Description of BlogPosting
 *
 * @author mark
 */
class UmseoSchema_Post extends UmseoSchema_Page
{
	/**
	 * @param false $pretty
	 * @return array
	 */
	public function getResource( $pretty = false ): array
	{
		global $post;
		parent::getResource($pretty);

		$post_type = $post->post_type;

		switch ($post_type)
		{
			case 'product': // woocommerce product

				$this->schemaType = 'Product';
				$this->schema['@type'] = $this->schemaType;
				$this->schema['@id'] = UmSEO_Canonical::get_canonical_url() . '#product';
				$this->schema['inLanguage'] = get_bloginfo("language");

				// Get the Categories
				$categories = get_the_category();
				if (count($categories) > 0)
				{
					$categoryNames = array_map(function ( $category )
					{
						return $category->name;
					}, $categories);

					$this->schema['articleSection'] = $categoryNames[0];
				}

				// Check is woocommerce install and activated
				if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
				{
					$wc_product = wc_get_product( $post->ID );
					$stock = $wc_product->get_stock_status();
					$brand = $wc_product->get_attribute('pa_brand');


					$this->schema['sku'] = $wc_product->get_sku();
					$this->schema['brand'] = [
						'@type' => 'Brand',
						'name'  => $brand
					];
					$this->schema['offers'] = [
						'@type'         => 'Offer',
						'url'           => UmSEO_Canonical::get_canonical_url(),
						'priceCurrency' => get_woocommerce_currency(),
						'price'         => $wc_product->get_price(),
						'itemCondition' => 'https://schema.org/NewCondition',
						'availability'  => 'https://schema.org/' . $this->getSchemaOrgStockStatus( $stock ),
					];

				}

				return array(
					'p' => $this->toJson($this->schema, $pretty),
				);
				break;



			default: // simple post

				$this->schemaType = 'Article';
				$this->schema['@type'] = $this->schemaType;
				$this->schema['@id'] = UmSEO_Canonical::get_canonical_url() . '#article';

				// Get the Categories
				$categories = get_the_category();
				if (count($categories) > 0)
				{
					$categoryNames = array_map(function ( $category )
					{
						return $category->name;
					}, $categories);

					$this->schema['articleSection'] = $categoryNames[0];
				}

				$this->schema['wordCount'] = str_word_count($post->post_content);
				$this->schema['keywords'] = $this->getTags();

				$_news_article = $this->schema;
				$_news_article['@type'] = 'NewsArticle';
				$_news_article['@id'] = UmSEO_Canonical::get_canonical_url() . '#newsarticle';

				unset($_news_article['wordCount']);
				unset($_news_article['keywords']);

				$_news_article['keywords'] = $this->getTags();
				$_news_article['wordCount'] = str_word_count($post->post_content);

				return array(
					'a' => $this->toJson($this->schema, $pretty),
					'n' => $this->toJson($_news_article, $pretty),
				);
				break;

		}

	}


	/**
	 * @param false $Pretty
	 * @return false|string
	 */
	public function getBreadcrumb( $Pretty = false )
	{
		return false;

		$BreadcrumbPosition = 1;

		$this->SchemaBreadcrumb['@context'] = 'http://schema.org/';
		$this->SchemaBreadcrumb['@type'] = 'BreadcrumbList';

		$this->SchemaBreadcrumb['itemListElement'][] = array
		(
			'@type' => 'ListItem',
			'position' => $BreadcrumbPosition++,
			'item' => array
			(
				'@id' => home_url('/#breadcrumbitem'),
				'name' => get_bloginfo('name'),
			),
		);

		$this->SchemaBreadcrumb['itemListElement'][] = array
		(
			'@type' => 'ListItem',
			'position' => $BreadcrumbPosition++,
			'item' => array
			(
				'@id' => get_permalink() . "#breadcrumbitem",
				'name' => get_the_title(),
			),
		);

		return $this->toJson($this->SchemaBreadcrumb, $Pretty);
	}


	/**
	 * Schema.org Enumeration Type for "ItemAvailability" ( woocommerce stock status )
	 * @param $stock
	 * @return string
	 */
	public function getSchemaOrgStockStatus( $stock ): string
	{

		switch ($stock)
		{
			case 'onbackorder':
				return 'BackOrder';
				break;

			case 'outofstock':
				return 'OutOfStock';
				break;

			default :
				return 'InStock';
				break;
		}
	}

}
