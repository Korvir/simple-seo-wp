<?php

class UmSEO_Title {

	function __construct() 
	{
    // $generate_canonical = umseo_get_option('generate_canonical', 'umseo_canonicalizer', false);
    remove_action( 'wp_head', '_wp_render_title_tag', 1 );
    add_filter('wp_title', array($this, 'get_title'), 99);
  }

  public static function get_settings()
  {
    return [
      [
        'desc'              => '<ol>
                                <li>Go to <a href="'.admin_url('theme-editor.php').'">Appearance ⇒ Editor</a> (if you get a permissions error, you may be on a WordPress multi-site environment and may not be able to use the filtering rewrite method)</li>
                                <li>Click “Header (header.php)”</li>
                                <li>Look for the <code>&lt;title&gt;</code> start tag and the <code>&lt;/title&gt;</code> end tag</li>
                                <li>Edit the text in between those tags so that it looks like this: <code>&lt;title&gt;&lt;?php wp_title(\'\'); ?&gt;&lt;/title&gt;</code></li>
                                <li>Click “Update File”</li>
                              </ol>',
        'type'              => 'html',
        'name'              => 'umseo_title_tag_rewriter_instruction',
      ],
      [
        'desc'              => '<h3>Formats</h3>',
        'type'              => 'html',
        'name'              => 'umseo_title_tag_rewriter_formats',
      ],
      [
        'name'              => 'umseo_title_home_format',
        'label'             => __( 'Blog Homepage Title', 'umseo' ),
        'type'              => 'text',
        'default'           => get_bloginfo('description'),
      ],
      [
        'name'              => 'umseo_title_post_format',
        'label'             => __( 'Post Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => '{post}',
      ],
      [
        'name'              => 'umseo_title_page_format',
        'label'             => __( 'Page Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => '{page}',
      ],
      [
        'name'              => 'umseo_title_category_format',
        'label'             => __( 'Category Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => '{category}',
      ],
      [
        'name'              => 'umseo_title_tag_format',
        'label'             => __( 'Tag Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => '{tag}',
      ],
      [
        'name'              => 'umseo_title_day_format',
        'label'             => __( 'Day Archive Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => 'Archives for {month} {day}, {year}',
      ],
      [
        'name'              => 'umseo_title_month_format',
        'label'             => __( 'Month Archive Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => 'Archives for {month} {year}',
      ],
      [
        'name'              => 'umseo_title_year_format',
        'label'             => __( 'Year Archive Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => 'Archives for {year}',
      ],
      [
        'name'              => 'umseo_title_author_format',
        'label'             => __( 'Author Archive Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => 'Posts by {author}',
      ],
      [
        'name'              => 'umseo_title_search_format',
        'label'             => __( 'Search Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => 'Search Results for {query}',
      ],
      [
        'name'              => 'umseo_title_404_format',
        'label'             => __( '404 Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => '404 Not Found',
      ],
      [
        'name'              => 'umseo_title_pagination_format',
        'label'             => __( 'Pagination Title Format', 'umseo' ),
        'type'              => 'text',
        'default'           => '{title} - Page {num}',
      ],
    ];
  }
  
  public static function get_title($title)
  {
    global $page, $paged;

    // If it's a 404 page, use a "Page not found" title.
    if ( is_404() ) {
      $format = umseo_get_option('umseo_title_404_format', 'umseo_title_tag_rewriter', false);
      if ($format) {
        $title = $format;
      }
      // If it's a search, use a dynamic search results title.
    } elseif ( is_search() ) {
      /* translators: %s: search phrase */
      $format = umseo_get_option('umseo_title_search_format', 'umseo_title_tag_rewriter', false);
      if ($format) {
        $title = str_replace('{query}', get_search_query(), $format);
      }

      // If on the front page, use the site title.
    } elseif ( is_front_page() ) {
      
      $page_on_front = get_option( 'page_on_front' );
      if( $page_on_front ) {
        $format = get_post_meta( $page_on_front, '_umseo_title', true );
        if( !$format ){
          $format = umseo_get_option('umseo_title_home_format', 'umseo_title_tag_rewriter', false);
        }
        $title = str_replace('{page}', single_post_title( '', false ), $format);
        
      }else{
        $format = umseo_get_option('umseo_title_home_format', 'umseo_title_tag_rewriter', false);
        $title = $format;
        
      }

      // If on the front page, use the site title.
    } elseif ( is_home() && !is_front_page() ) {

    	$page_for_posts = get_option( 'page_for_posts' );
      if( $page_for_posts ){
        $format = get_post_meta( $page_for_posts, '_umseo_title', true );
        if (!$format) {
          $format = umseo_get_option('umseo_title_page_format', 'umseo_title_tag_rewriter', false);
        }
        $title = str_replace('{page}', single_post_title( '', false ), $format);
      } else {
        $format = umseo_get_option('umseo_title_home_format', 'umseo_title_tag_rewriter', false);
        $title = $format;

      }

    } elseif ( is_single() ) {
      $post = get_queried_object();
      $format = get_post_meta( $post->ID, '_umseo_title', true );
      if (!$format) {
        $format = umseo_get_option('umseo_title_post_format', 'umseo_title_tag_rewriter', false);
      }
      $title = str_replace('{post}', single_post_title( '', false ), $format);

    } elseif ( is_page() ) {
      $page_post = get_queried_object();
      $format = get_post_meta( $page_post->ID, '_umseo_title', true );
      if (!$format) {
        $format = umseo_get_option('umseo_title_page_format', 'umseo_title_tag_rewriter', false);
      }
      $title = str_replace('{page}', single_post_title( '', false ), $format);

    } elseif ( is_category() ) {
      $term = get_queried_object();
      $format = umseo_get_term_meta( $term->term_id, '_umseo_title' );
      if (!$format) {
        $format = umseo_get_option('umseo_title_category_format', 'umseo_title_tag_rewriter', false);
      }
      $title = str_replace('{category}', $term->name, $format);
      
    } elseif ( is_tag() ) {
      $term = get_queried_object();
      $format = umseo_get_term_meta( $term->term_id, '_umseo_title' );
      if (!$format) {
        $format = umseo_get_option('umseo_title_tag_format', 'umseo_title_tag_rewriter', false);
      }
      $title = str_replace('{tag}', $term->name, $format);

    } elseif ( is_author() && $author = get_queried_object() ) {
      $format = umseo_get_option('umseo_title_author_format', 'umseo_title_tag_rewriter', false);
      $title = str_replace('{author}', $author->display_name, $format);

    } elseif ( is_year() ) {
      $format = umseo_get_option('umseo_title_year_format', 'umseo_title_tag_rewriter', false);
      $title = str_replace('{year}', get_the_date('Y'), $format);

    } elseif ( is_month() ) {
      $format = umseo_get_option('umseo_title_month_format', 'umseo_title_tag_rewriter', false);
      $title = str_replace(['{year}', '{month}'], [get_the_date('Y'), get_the_date('F')], $format);

    } elseif ( is_day() ) {
      $format = umseo_get_option('umseo_title_day_format', 'umseo_title_tag_rewriter', false);
      $title = str_replace(
        ['{year}', '{month}', '{day}'], 
        [get_the_date('Y'), get_the_date('F'), get_the_date('d')], 
        $format
      );
    }
    // Add a page number if necessary.
    if ( ( $paged >= 2 || $page >= 2 ) && !is_404() ) {
      $format = umseo_get_option('umseo_title_pagination_format', 'umseo_title_tag_rewriter', false);
      $title = str_replace(['{title}', '{num}'], [$title, max( $paged, $page )], $format);
    }

    
    return apply_filters( 'umseo_title', $title );
  }
}