<?php

class UmSEO_Canonical {

	function __construct() 
	{
    $generate_canonical = umseo_get_option('generate_canonical', 'umseo_canonicalizer', false);

    if ($generate_canonical === 'on') {
      //...remove WordPress's default canonical tags (since they only handle posts/pages/attachments)
      remove_action('wp_head', 'rel_canonical');

      //...and add our custom canonical tags.
			add_action('umseo_head', [$this, 'link_rel_canonical_tag']);
    }
  }
  
  public function link_rel_canonical_tag()
  {
    if (umseo_is_amp()) {
      return;
    }

    //Display the canonical tag if a canonical URL is available
		if ($url = self::get_canonical_url()) {
			$url = esc_attr($url);
			echo "<link rel=\"canonical\" href=\"$url\" />\n";
		}
  }

  public static function get_canonical_url()
  {
    global $wp_query, $wp_rewrite;
		
		//404s and search results don't have canonical URLs
		if ($wp_query->is_404 || $wp_query->is_search) return false;
		
		//Are there posts in the current Loop?
    $haspost = count($wp_query->posts) > 0;
    
    $link = '';
		
		//Handling special case with '?m=yyyymmddHHMMSS'.
		if (get_query_var('m')) {
			$m = preg_replace('/[^0-9]/', '', get_query_var('m'));
			switch (strlen($m)) {
				case 4: // Yearly
					$link = get_year_link($m);
					break;
				case 6: // Monthly
					$link = get_month_link(substr($m, 0, 4), substr($m, 4, 2));
					break;
				case 8: // Daily
					$link = get_day_link(substr($m, 0, 4), substr($m, 4, 2),
										 substr($m, 6, 2));
					break;
				default:
					//Since there is no code for producing canonical archive links for is_time, we will give up and not try to produce a link.
					return false;
			}
		
		//Posts and pages
		} elseif (($wp_query->is_single || $wp_query->is_page) && $haspost) {
			$post = $wp_query->posts[0];
			$link = get_permalink($post->ID);
			if (is_front_page()) $link = trailingslashit($link);
			
		//Author archives
		} elseif ($wp_query->is_author && $haspost) {
			$author = get_userdata(get_query_var('author'));
			if ($author === false) return false;
			$link = get_author_posts_url($author->ID, $author->user_nicename);
			
		//Category archives
		} elseif ($wp_query->is_category && $haspost) {
			$link = get_category_link(get_query_var('cat'));
			
		//Tag archives
		} else if ($wp_query->is_tag  && $haspost) {
			$tag = get_term_by('slug',get_query_var('tag'),'post_tag');
			if (!empty($tag->term_id)) $link = get_tag_link($tag->term_id);
		
		//Day archives
		} elseif ($wp_query->is_day && $haspost) {
			$link = get_day_link(get_query_var('year'),
								 get_query_var('monthnum'),
								 get_query_var('day'));
		
		//Month archives
		} elseif ($wp_query->is_month && $haspost) {
			$link = get_month_link(get_query_var('year'),
								   get_query_var('monthnum'));
		
		//Year archives
		} elseif ($wp_query->is_year && $haspost) {
			$link = get_year_link(get_query_var('year'));
		
		//Homepage
		} elseif ($wp_query->is_home) {
			if ((get_option('show_on_front') == 'page') && ($pageid = get_option('page_for_posts')))
				$link = trailingslashit(get_permalink($pageid));
			else
				$link = trailingslashit(get_option('home'));
			
		//Other
		} else
			return false;
		
		//Handle pagination
		$page = get_query_var('paged');
		if ($page && $page > 1) {
			if ($wp_rewrite->using_permalinks()) {
				$link = trailingslashit($link) ."page/$page";
				$link = user_trailingslashit($link, 'paged');
			} else {
				$link = esc_url( add_query_arg( 'paged', $page, $link ) );
			}
		}
		
		return $link;
  }
}