<?php

// if (is_single()) {

//   global $post;
//   setup_postdata( $post ); 

//   $headline = get_the_title();

//   if (mb_strlen($headline) > 109) {	
//   $headline = mb_substr($headline, 0, 105) . '...';
//   }

//   $ld_json = array(
//       '@context'		=> 'http://schema.org',
//       '@type'			=> 'NewsArticle',
//       'headline'		=> $headline,
//       'articleSection'	=> get_the_category()[0]->name,
//       'publisher'		=> array(
//           '@type'		=> 'Organization',
//           'name'		=> 'Happy Gamer',
//           'sameAs'    => array(
//               'https://www.facebook.com/HappyGamerNews/',
//               'https://twitter.com/HappyGamerWeb',
//               'https://www.pinterest.com/HappyGamerNews/',
//               'https://happygamernews.tumblr.com/',
//               'https://www.instagram.com/happygamernews/'
//           ),
//           'url'	=> 'http://happygamer.com',
//           'logo'	=> array(
//           '@type'  => 'ImageObject',
//               'url'	 => $fake_acf['logo'],
//               'height' => 50,
//               'width'	 => 240
//           )
//       ),
//       'alternativeHeadline'	=> get_the_title(),
//       'description'		    => wp_trim_words( get_the_content(), 35, '...' ),
//       'name'		            => get_the_title(),
//       'inLanguage'		    => 'en-US',
//       'datePublished'		    => get_the_date('c'),
//       'dateModified'		    => get_the_modified_date('c'),
//       'author'	            => array(
//           '@type'	=> 'Person',
//           'name'	=> get_the_author_meta('display_name', get_the_author_meta('ID')),
//           'sameAs'	=> array(
//               get_author_posts_url(get_the_author_meta('ID'))
//           )
//       )
//   );

//   if ($tags = wp_get_post_tags( get_the_ID() )) {

//       $featured_tags = get_option("featured_tags");
//       $tag = false;

//       if ($featured_tags && is_array($featured_tags)) {
//           foreach ($tags as $check_tag) {
//           if (in_array($check_tag->term_id, $featured_tags)) {
//               $tag = $check_tag;
//               break;
//           }
//           }
//       }

//       if ($tag !== false) {

//           $ld_json['about'] = array(
//               '@type'	=> 'Person',
//               'name'	=> $tag->name,
//               'sameAs'	=> get_tag_link($tag->term_id)
//           );
//       }
//   }

//   $ld_json['articleBody'] = get_the_content();

//   if ($post_thumbnail_id = get_post_thumbnail_id( get_the_ID() )) {

//       $image = wp_get_attachment_image_src($post_thumbnail_id, 'full');
//       if( is_array( $image ) && !empty($image) ){
//           $ld_json['image'] = array(
//               '@type'	=> 'ImageObject',
//               'url'	=> $image[0],
//               'height'	=> $image[2],
//               'width'	=> $image[1]
//           );
//       }else{
//           $curr_post_img = _img('no_image.png');
//           $ld_json['image'] = array(
//               '@type'	 => 'ImageObject',
//               'url'	 => $curr_post_img,
//               'height' => 405,
//               'width'	 => 760
//           );
//       }

//   }

//   $ld_json['mainEntityOfPage'] = array(
//       "@type"     => "WebPage",
//       "@id"       => get_the_permalink()
//   );
// }

class UmSEO_Permalinks {

  protected $category_query = [
    'index.php?category_name=$matches[1]&feed=$matches[2]',
    'index.php?category_name=$matches[1]&feed=$matches[2]',
    'index.php?category_name=$matches[1]&embed=true',
    'index.php?category_name=$matches[1]&paged=$matches[2]',
    'index.php?category_name=$matches[1]',
  ];

  protected $post_tag_query = [
    'index.php?tag=$matches[1]&feed=$matches[2]',
    'index.php?tag=$matches[1]&feed=$matches[2]',
    'index.php?tag=$matches[1]&embed=true',
    'index.php?tag=$matches[1]&paged=$matches[2]',
    'index.php?tag=$matches[1]',
  ];

  protected $post_query = [
    'index.php?name=$matches[1]&p=$matches[2]&feed=$matches[3]',
    'index.php?name=$matches[1]&p=$matches[2]&feed=$matches[3]',
    'index.php?name=$matches[1]&p=$matches[2]&paged=$matches[3]',
    'index.php?name=$matches[1]&p=$matches[2]&page=$matches[3]',
  ];

  protected $page_query = [
    'index.php?pagename=$matches[1]&feed=$matches[2]',
    'index.php?pagename=$matches[1]&feed=$matches[2]',
    'index.php?pagename=$matches[1]&paged=$matches[2]',
    'index.php?pagename=$matches[1]&page=$matches[2]',
  ];

	function __construct() 
	{
    $nobase_category = umseo_get_option('nobase_category', 'umseo_permalinks', false);
    $nobase_post_tag = umseo_get_option('nobase_post_tag', 'umseo_permalinks', false);

    if (is_admin()) {
      $flush_rules = get_option('umseo_require_flush_rules', false);
  
      if ($flush_rules !== false && method_exists($this, $flush_rules)) {
        delete_option('umseo_require_flush_rules');
        add_action('init', [$this, $flush_rules]);
      }
    }

    $enabled_nobase = [];

    if ($nobase_category === 'on') {
      $enabled_nobase[] = 'category';
    }

    if ($nobase_post_tag === 'on') {
      $enabled_nobase[] = 'post_tag';
    }

    add_action('umseo_settings_nobase_category_sanitize', [$this, 'umseo_settings_nobase_category_sanitize']);
    add_action('umseo_settings_nobase_post_tag_sanitize', [$this, 'umseo_settings_nobase_post_tag_sanitize']);

    foreach ($enabled_nobase as $nobase) {
      add_action('created_'.$nobase, [$this, 'no_term_base_refresh_rules'], 99);
      add_action('delete_'.$nobase, [$this, 'no_term_base_refresh_rules'], 99);
      add_action('edited_'.$nobase, [$this, 'no_term_base_refresh_rules'], 99);
      add_action('init', [$this, 'no_'.$nobase.'_base_permastruct']);
      add_filter($nobase.'_rewrite_rules', [$this, $nobase.'_rewrite_rules']);
    }
    
    if ($nobase_category === 'on' || $nobase_post_tag === 'on') {
      add_filter('do_parse_request', [$this, 'do_parse_request'], 1, 3);
      add_filter('term_link', [$this, 'term_link'], 10, 3);
      add_filter('query_vars', [$this, 'no_terms_base_query_vars']); // Adds 'category_redirect' query variable
      add_filter('request', [$this, 'no_category_base_request']); // Redirects if 'category_redirect' is set
    }
	}

	public function activate() 
	{
		$this->no_term_base_refresh_rules();
	}

	public function deactivate() 
	{
    $nobase_category = umseo_get_option('nobase_category', 'umseo_permalinks', false);
    $nobase_post_tag = umseo_get_option('nobase_post_tag', 'umseo_permalinks', false);
    
    if ($nobase_category !== 'on') {
      remove_filter( 'category_rewrite_rules', [$this, 'category_rewrite_rules']); // We don't want to insert our custom rules again
    }

    if ($nobase_post_tag !== 'on') {
      remove_filter( 'post_tag_rewrite_rules', [$this, 'post_tag_rewrite_rules']); // We don't want to insert our custom rules again
    }

		$this->no_term_base_refresh_rules();
  }

  public function do_parse_request($do_parse_request, $wp, $extra_query_vars)
  {
    global $wp_rewrite;

    $rewrite = $wp_rewrite->wp_rewrite_rules();
    $post_query = $this->post_query;
    $page_query = $this->page_query;

    // Find only page/posts rewrite rules
    $posts_rewrite = array_filter($rewrite, function($rule) use ($post_query) {
      return in_array($rule, $post_query);
    });

    $pages_rewrite = array_filter($rewrite, function($rule) use ($page_query) {
      return in_array($rule, $page_query);
    });

    $pathinfo         = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
    list( $pathinfo ) = explode( '?', $pathinfo );
    $pathinfo         = str_replace( '%', '%25', $pathinfo );

    list( $req_uri ) = explode( '?', $_SERVER['REQUEST_URI'] );
    $home_path       = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
    $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

    // Trim path info from the end and the leading home path from the
    // front. For path info requests, this leaves us with the requesting
    // filename, if any. For 404 requests, this leaves us with the
    // requested permalink.
    $req_uri  = str_replace( $pathinfo, '', $req_uri );
    $req_uri  = trim( $req_uri, '/' );
    $req_uri  = preg_replace( $home_path_regex, '', $req_uri );
    $req_uri  = trim( $req_uri, '/' );
    $post_mathed = false;

    if (strpos($req_uri, 'amp/') === 0) {
      $req_uri = str_replace('amp/', '', $req_uri);
    }

    if (strpos($req_uri, '/amp') === (mb_strlen($req_uri) - 4)) {
      $req_uri = str_replace('/amp', '', $req_uri);
    }
    

    // The requested permalink is in $pathinfo for path info requests and
    //  $req_uri for other requests.
    foreach ( (array) $posts_rewrite as $match => $query ) {
      
      if ( preg_match( "#^$match#", $req_uri, $matches ) ||
        preg_match( "#^$match#", urldecode( $req_uri ), $matches ) ) {
          $post = get_page_by_path( $matches[1], OBJECT, ['post'] );

          if (!$post) {
            continue;
          }

          $post_status_obj = get_post_status_object( $post->post_status );

          if ( ! $post_status_obj->public && ! $post_status_obj->protected
            && ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
            continue;
          }

          $post_mathed = true;
          // Found post for current URL. So we can remove category/tags rules from wp_rewrite_rules
          add_filter( 'option_rewrite_rules', [$this, 'clean_terms_rewrite_rules'], 10, 2 );

          break;
      }
    }

    if (!$post_mathed) {
      foreach ( (array) $pages_rewrite as $match => $query ) {
        
        if ( preg_match( "#^$match#", $req_uri, $matches ) ||
          preg_match( "#^$match#", urldecode( $req_uri ), $matches ) ) {
            $post = get_page_by_path( $matches[1], OBJECT, ['page'] );
  
            if (!$post) {
              continue;
            }
  
            $post_status_obj = get_post_status_object( $post->post_status );
  
            if ( ! $post_status_obj->public && ! $post_status_obj->protected
              && ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
              continue;
            }
  
            $post_mathed = true;
            // Found page for current URL. So we can remove category/tags rules from wp_rewrite_rules
            add_filter( 'option_rewrite_rules', [$this, 'clean_terms_rewrite_rules'], 10, 2 );
  
            break;
        }
      }
    }

    // No post found by url. Maybe it is cat/tag url?
    if (!$post_mathed) {
      $req_slug = $req_uri;

      if (strpos($req_slug, '/page/') !== false) {
        $req_slug_arr = explode('/page/', $req_slug);
        $req_slug = array_shift($req_slug_arr);
      }

      if (strpos($req_slug, '/feed') !== false) {
        $req_slug_arr = explode('/feed', $req_slug);
        $req_slug = array_shift($req_slug_arr);
      }

      if (strpos($req_slug, '/embed') !== false) {
        $req_slug_arr = explode('/embed', $req_slug);
        $req_slug = array_shift($req_slug_arr);
      }

      $req_slug_arr = explode('/', $req_slug);
      $req_slug = array_pop($req_slug_arr);

      $is_category = get_term_by( 'slug', $req_slug, 'category' );

      if ($is_category) {
        add_filter( 'option_rewrite_rules', [$this, 'clean_post_tag_rewrite_rules'], 10, 2 );
      } else {
        $is_tag = get_term_by( 'slug', $req_slug, 'post_tag' );

        if ($is_tag) {
          add_filter( 'option_rewrite_rules', [$this, 'clean_category_rewrite_rules'], 10, 2 );
        } else {
          add_filter( 'option_rewrite_rules', [$this, 'clean_terms_rewrite_rules'], 10, 2 );
        }
      }
    }

    return $do_parse_request;
  }

  public function clean_terms_rewrite_rules( $value, $option ){
    $nobase_category = umseo_get_option('nobase_category', 'umseo_permalinks', false) === 'on';
    $nobase_post_tag = umseo_get_option('nobase_post_tag', 'umseo_permalinks', false) === 'on';

    $remove_rules = [];

    if ($nobase_category) {
      // Categories
      $remove_rules = array_merge($remove_rules, $this->category_query);
    }

    if ($nobase_post_tag) {
      // Tags
      $remove_rules = array_merge($remove_rules, $this->post_tag_query);
    }

    $value = array_filter($value, function($rule) use ($remove_rules) {
      return !in_array($rule, $remove_rules);
    });

    return $value;
  }

  public function clean_category_rewrite_rules( $value, $option ){
    $nobase_category = umseo_get_option('nobase_category', 'umseo_permalinks', false) === 'on';

    $remove_rules = [];

    if ($nobase_category) {
      // Categories
      $remove_rules = array_merge($remove_rules, $this->category_query);
    }

    $value = array_filter($value, function($rule) use ($remove_rules) {
      return !in_array($rule, $remove_rules);
    });

    return $value;
  }

  public function clean_post_tag_rewrite_rules( $value, $option ){
    $nobase_post_tag = umseo_get_option('nobase_post_tag', 'umseo_permalinks', false) === 'on';

    $remove_rules = [];

    if ($nobase_post_tag) {
      // Tags
      $remove_rules = array_merge($remove_rules, $this->post_tag_query);
    }

    $value = array_filter($value, function($rule) use ($remove_rules) {
      return !in_array($rule, $remove_rules);
    });

    return $value;
  }
  
  public function umseo_settings_nobase_category_sanitize($new_value)
  {
    $nobase_category = umseo_get_option('nobase_category', 'umseo_permalinks', false);

    if ($new_value !== $nobase_category) {
      if ($new_value === 'on') {
        update_option('umseo_require_flush_rules', 'activate');
      } else {
        update_option('umseo_require_flush_rules', 'deactivate');
      }
    }
  }

  public function umseo_settings_nobase_post_tag_sanitize($new_value)
  {
    $nobase_category = umseo_get_option('nobase_post_tag', 'umseo_permalinks', false);

    if ($new_value !== $nobase_category) {
      if ($new_value === 'on') {
        update_option('umseo_require_flush_rules', 'activate');
      } else {
        update_option('umseo_require_flush_rules', 'deactivate');
      }
    }
  }

  public function get_tax_base($tax)
  {
    $default = 'category';

		if ($tax === 'post_tag') {
			$default = 'tag';
		}

		$term_base = get_option( $tax.'_base' ) ? get_option( $tax.'_base' ) : $default;
    $term_base = trim( $term_base, '/' );
    
    return $term_base;
  }

	public function term_link($termlink, $term, $taxonomy)
	{
    $nobase_tax = umseo_get_option('nobase_'.$taxonomy, 'umseo_permalinks', false);

    if ($nobase_tax === 'on') {
      $old_term_base = $this->get_tax_base($taxonomy);
      $termlink = str_replace("/{$old_term_base}/", '/', $termlink);
    }

		return $termlink;
	}

	public static function no_term_base_refresh_rules()
	{
		global $wp_rewrite;
    $wp_rewrite->flush_rules();
	}
	
	public function category_rewrite_rules($category_rewrite)
	{
		global $wp_rewrite;

		// Redirect support from Old Category Base
		$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
		$old_category_base = trim( $old_category_base, '/' );
		$category_rewrite[$old_category_base.'/(.*)$'] = 'index.php?category_redirect=$matches[1]';

		return $category_rewrite;
  }
  
  public function post_tag_rewrite_rules($post_tag_rewrite)
	{
		global $wp_rewrite;

		// Redirect support from Old post_tag Base
		$old_post_tag_base = get_option( 'tag_base' ) ? get_option( 'tag_base' ) : 'tag';
		$old_post_tag_base = trim( $old_post_tag_base, '/' );
		$post_tag_rewrite[$old_post_tag_base.'/(.*)$'] = 'index.php?post_tag_redirect=$matches[1]';

		return $post_tag_rewrite;
	}

	public function no_terms_base_query_vars($public_query_vars)
	{
		$public_query_vars[] = 'category_redirect';
		$public_query_vars[] = 'post_tag_redirect';
		return $public_query_vars;
	}

	public function no_category_base_request($query_vars)
	{
		if( isset( $query_vars['category_redirect'] ) ) {
			$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['category_redirect'], 'category' );
			status_header( 301 );
			header( "Location: $catlink" );
			exit();
    }
    
    if( isset( $query_vars['post_tag_redirect'] ) ) {
			$catlink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $query_vars['post_tag_redirect'], 'tag' );
			status_header( 301 );
			header( "Location: $catlink" );
			exit();
		}
	
		return $query_vars;
	}

	public function no_category_base_permastruct()
	{
		global $wp_rewrite;
		$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';
  }
  
  public function no_post_tag_base_permastruct()
	{
		global $wp_rewrite;
		$wp_rewrite->extra_permastructs['post_tag']['struct'] = '%post_tag%';
	}
}