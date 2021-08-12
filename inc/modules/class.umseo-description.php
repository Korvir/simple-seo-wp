<?php

class UmSEO_Description {

	function __construct() 
	{
    add_action( 'umseo_head', [$this, 'head_tag_output'] );
  }

  public static function get_settings()
  {
    return [
      [
        'name'              => 'umseo_desc_home_format',
        'label'             => __( 'Blog Homepage Description', 'umseo' ),
        'type'              => 'textarea',
        'default'           => get_bloginfo('description'),
      ],
      [
        'name'              => 'umseo_desc_post_format',
        'label'             => __( 'Post Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => '{excerpt}',
      ],
      [
        'name'              => 'umseo_desc_page_format',
        'label'             => __( 'Page Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => '{excerpt}',
      ],
      [
        'name'              => 'umseo_desc_category_format',
        'label'             => __( 'Category Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => '{description}',
      ],
      [
        'name'              => 'umseo_desc_tag_format',
        'label'             => __( 'Tag Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => '{description}',
      ],
      [
        'name'              => 'umseo_desc_day_format',
        'label'             => __( 'Day Archive Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => 'Archives for {month} {day}, {year}',
      ],
      [
        'name'              => 'umseo_desc_month_format',
        'label'             => __( 'Month Archive Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => 'Archives for {month} {year}',
      ],
      [
        'name'              => 'umseo_desc_year_format',
        'label'             => __( 'Year Archive Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => 'Archives for {year}',
      ],
      [
        'name'              => 'umseo_desc_author_format',
        'label'             => __( 'Author Archive Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => 'Posts by {author}',
      ],
      [
        'name'              => 'umseo_desc_pagination_format',
        'label'             => __( 'Pagination Description Format', 'umseo' ),
        'type'              => 'textarea',
        'default'           => '{meta_description} - Page {num}',
      ],
    ];
  }

  function head_tag_output() {
		
		$desc = self::get_meta_desc();
		
    //Do we have a description? If so, output it.
		if ($desc)
			echo "\t<meta name=\"description\" content=\"" . esc_attr($desc) . "\" />\n";
	}
	
  
  public static function get_meta_desc()
  {
    global $page, $paged;

    $description = '';

    if (is_404()) {
      return;
    }

    if ( is_front_page() ) {
      
      $page_on_front = get_option( 'page_on_front' );
      if( $page_on_front ) {
        $format = get_post_meta( $page_on_front, '_umseo_description', true );
        if( !$format ){
          $format = umseo_get_option('umseo_desc_home_format', 'umseo_meta_description', false);
        }
        $description = $format;
        
      }else{
        
        $format = umseo_get_option('umseo_desc_home_format', 'umseo_meta_description', false);
        $description = $format;
      }

    } elseif ( is_home() ) {

    	$page_for_posts = get_option( 'page_for_posts' );
      if( $page_for_posts ){
        $format = get_post_meta( $page_for_posts, '_umseo_description', true );
        if (!$format) {
          $format = umseo_get_option('umseo_desc_home_format', 'umseo_meta_description', false);
        }
        $description = $format;

      } else {
        $format = umseo_get_option('umseo_desc_home_format', 'umseo_meta_description', false);
        $description = $format;
      }

    } elseif ( is_single() ) {
      $post = get_queried_object();
      $format = get_post_meta( $post->ID, '_umseo_description', true );
      if (!$format) {
        $format = umseo_get_option('umseo_desc_post_format', 'umseo_meta_description', false);
      }
      $description = str_replace('{excerpt}', self::get_the_excerpt(), $format);

    } elseif ( is_page() ) {
      $page_post = get_queried_object();
      $format = get_post_meta( $page_post->ID, '_umseo_description', true );
      if (!$format) {
        $format = umseo_get_option('umseo_desc_page_format', 'umseo_meta_description', false);
      }
      $description = str_replace('{excerpt}', self::get_the_excerpt(), $format);

    } elseif ( is_category() ) {
      $term = get_queried_object();
      $format = umseo_get_term_meta( $term->term_id, '_umseo_description' );

      if (!$format) {
        $format = umseo_get_option('umseo_desc_category_format', 'umseo_meta_description', false);
      }
      $description = str_replace(['{category}', '{description}'], [$term->name, $term->category_description], $format);
      
    } elseif ( is_tag() ) {
      $term = get_queried_object();
      $format = umseo_get_term_meta( $term->term_id, '_umseo_description' );
      
      if (!$format) {
        $format = umseo_get_option('umseo_desc_tag_format', 'umseo_meta_description', false);
        $description = str_replace(['{tag}', '{description}'], [$term->name, $term->description], $format);
      } else {
        $description = str_replace('{tag}', $term->name, $format);
      }

    } elseif ( is_author() && $author = get_queried_object() ) {
      $format = umseo_get_option('umseo_desc_author_format', 'umseo_meta_description', false);
      $description = str_replace('{author}', $author->display_name, $format);

    } elseif ( is_year() ) {
      $format = umseo_get_option('umseo_desc_year_format', 'umseo_meta_description', false);
      $description = str_replace('{year}', get_the_date('Y'), $format);

    } elseif ( is_month() ) {
      $format = umseo_get_option('umseo_desc_month_format', 'umseo_meta_description', false);
      $description = str_replace(['{year}', '{month}'], [get_the_date('Y'), get_the_date('F')], $format);

    } elseif ( is_day() ) {
      
      $format = umseo_get_option('umseo_desc_day_format', 'umseo_meta_description', false);
      $description = str_replace(
        ['{year}', '{month}', '{day}'], 
        [get_the_date('Y'), get_the_date('F'), get_the_date('d')], 
        $format
      );

    } elseif ( is_search() ) {
      
      $description = 'Search Page';

    }



    // Add a page number if necessary.
    if ( ( $paged >= 2 || $page >= 2 ) && !is_404() ) {
      $format = umseo_get_option('umseo_desc_pagination_format', 'umseo_meta_description', false);
      $description = str_replace(['{meta_description}', '{num}'], [$description, max( $paged, $page )], $format);
    }

    return apply_filters( 'umseo_description', $description );
  }

  public static function get_the_excerpt(){
    $excerpt = umseo_excerpt([
      'maxchar'     => 140,
      'autop'       => false,
    ]);

    return $excerpt;
  }
  
}