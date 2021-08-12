<?php

class UmSEO_OpenGraph
{

	function __construct()
	{
		add_action('umseo_head', [ $this, 'head_og_output' ]);
	}

	public static function get_settings()
	{
		return [
			[
				'desc' => '<ol>
                                <li>Go to <a href="' . admin_url('theme-editor.php') . '">Appearance ⇒ Editor</a> (if you get a permissions error, you may be on a WordPress multi-site environment and may not be able to use the filtering rewrite method)</li>
                                <li>Click “Header (header.php)”</li>
                                <li>Look for the <code>&lt;html&gt;</code> start tag</li>
                                <li>Edit the tag, add prefix attribute <code>prefix="og: http://ogp.me/ns#"</code> so that it looks like this: <code>&lt;html prefix="og: http://ogp.me/ns#"&gt;</code></li>
                                <li>Click “Update File”</li>
                              </ol>',
				'type' => 'html',
				'name' => 'umseo_title_tag_rewriter_instruction',
			],
			[
				'name' => 'umseo_og_sitename',
				'label' => __('Site Name', 'umseo'),
				'type' => 'text',
				'default' => get_bloginfo('name'),
			],
			[
				'name' => 'umseo_og_fb_app_id',
				'label' => __('Facebook App ID', 'umseo'),
				'type' => 'text',
				'default' => '',
			],
			[
				'name' => 'umseo_og_image',
				'label' => __('Default image', 'umseo'),
				'type' => 'file',
			],
			[
				'name' => 'umseo_og_image_width',
				'label' => __('Default image width', 'umseo'),
				'type' => 'number',
			],
			[
				'name' => 'umseo_og_image_height',
				'label' => __('Default image height', 'umseo'),
				'type' => 'number',
			],
			[
				'desc' => '<h3>Twitter card</h3>',
				'type' => 'html',
				'name' => 'umseo_og_twitter_desc',
			],
			[
				'name' => 'umseo_og_tw_username',
				'label' => __('@username of website', 'umseo'),
				'type' => 'text',
				'default' => '',
			],
		];
	}

	public function head_og_output()
	{
		if (is_404())
		{
			return;
		}

		$tags = [
			[
				'name' => 'og:locale',
				'content' => get_locale(),
			],
		];

		$twitter_tags = [];

		$og_sitename = umseo_get_option('umseo_og_sitename', 'umseo_opengraph', false);
		$og_tw_username = umseo_get_option('umseo_og_tw_username', 'umseo_opengraph', false);
		$og_fb_app = umseo_get_option('umseo_og_fb_app_id', 'umseo_opengraph', false);

		if (is_search())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];
		} elseif (is_front_page() || is_home())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];
		} elseif (is_single())
		{
			$post = get_queried_object();
			$categories = get_the_category();
			$single_category = ( count($categories) == 1 );

			$tags[] = [
				'name' => 'og:type',
				'content' => 'article',
			];

			$tags[] = [
				'name' => 'article:published_time',
				'content' => get_the_date('Y-m-d H:m'),
			];

			$tags[] = [
				'name' => 'article:modified_time',
				'content' => get_the_modified_date('Y-m-d H:m'),
			];

			$tags[] = [
				'name' => 'article:author',
				'content' => get_author_posts_url($post->post_author),
			];

			$taxonomies = get_object_taxonomies($post->post_type, 'objects');
			$taxonomies = wp_filter_object_list($taxonomies, array( 'public' => true, 'show_ui' => true ));

			foreach ($taxonomies as $taxonomy)
			{
				if ('category' === $taxonomy->name && $single_category)
				{
					foreach ($categories as $category)
					{
						$tags[] = [
							'name' => 'article:section',
							'content' => $category->cat_name,
						];
					}
				} elseif ($terms = get_the_terms($post->ID, $taxonomy->name))
				{
					foreach ($terms as $term)
					{
						$tags[] = [
							'name' => 'article:tag',
							'content' => $term->name,
						];
					}
				}
			}

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary_large_image',
			];
		} elseif (is_page())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		} elseif (is_category())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		} elseif (is_tag())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		} elseif (is_author() && $author = get_queried_object())
		{
			if (is_object($author))
			{
				$tags[] = [
					'name' => 'og:type',
					'content' => 'profile',
				];

				$tags[] = [
					'name' => 'profile:first_name',
					'content' => get_the_author_meta('first_name', $author->ID),
				];

				$tags[] = [
					'name' => 'profile:last_name',
					'content' => get_the_author_meta('last_name', $author->ID),
				];

				$tags[] = [
					'name' => 'profile:username',
					'content' => $author->user_login,
				];
			}
		} elseif (is_year())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		} elseif (is_month())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		} elseif (is_day())
		{
			$tags[] = [
				'name' => 'og:type',
				'content' => 'website',
			];

			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		}


		if ($og_tw_username)
		{
			$twitter_tags[] = [
				'name' => 'twitter:site',
				'content' => $og_tw_username,
			];
		}

		if (!$og_sitename)
		{
			$og_sitename = get_bloginfo('name');
		}

		if ($og_fb_app)
		{
			$tags[] = [
				'name' => 'fb:app_id',
				'content' => $og_fb_app,
			];
		}

		$tags[] = [
			'name' => 'og:site_name',
			'content' => $og_sitename,
		];

		$tags[] = [
			'name' => 'og:title',
			'content' => UmSEO_Title::get_title(''),
		];

		$twitter_tags[] = [
			'name' => 'twitter:title',
			'content' => UmSEO_Title::get_title(''),
		];

		$tags[] = [
			'name' => 'og:description',
			'content' => UmSEO_Description::get_meta_desc(),
		];

		$twitter_tags[] = [
			'name' => 'twitter:description',
			'content' => UmSEO_Description::get_meta_desc(),
		];

		$tags[] = [
			'name' => 'og:url',
			'content' => UmSEO_Canonical::get_canonical_url(),
		];

		$twitter_tags[] = [
			'name' => 'twitter:url',
			'content' => UmSEO_Canonical::get_canonical_url(),
		];

		if (is_singular() && has_post_thumbnail())
		{
			$image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), [ 968, 504 ]);

			$tags[] = [
				'name' => 'og:image',
				'content' => $image_data[0],
			];
			$tags[] = [
				'name' => 'og:image:width',
				'content' => $image_data[1],
			];
			$tags[] = [
				'name' => 'og:image:height',
				'content' => $image_data[2],
			];

			$twitter_tags[] = [
				'name' => 'twitter:image:src',
				'content' => $image_data[0],
			];
		} else
		{
			$image_url = umseo_get_option('umseo_og_image', 'umseo_opengraph', false);
			$image_width = umseo_get_option('umseo_og_image_width', 'umseo_opengraph', 968);
			$image_height = umseo_get_option('umseo_og_image_height', 'umseo_opengraph', 504);

			if ($image_url)
			{
				$tags[] = [
					'name' => 'og:image',
					'content' => $image_url,
				];
				$tags[] = [
					'name' => 'og:image:width',
					'content' => $image_width,
				];
				$tags[] = [
					'name' => 'og:image:height',
					'content' => $image_height,
				];

				$twitter_tags[] = [
					'name' => 'twitter:image',
					'content' => $image_url,
				];

				$twitter_tags[] = [
					'name' => 'twitter:image:src',
					'content' => $image_url,
				];

			}
		}

		$has_card = false;
		foreach ($twitter_tags as $k => $v)
		{
			if ($v['name'] === 'twitter:card') $has_card = true;
		}

		if (!$has_card)
		{
			$twitter_tags[] = [
				'name' => 'twitter:card',
				'content' => 'summary',
			];
		}

		$output_formats = [
			'<meta property="%1$s" content="%2$s" />' => $tags,
			'<meta name="%1$s" content="%2$s" />' => $twitter_tags
		];

		foreach ($output_formats as $output_format => $tags)
		{
			foreach ($tags as $tag)
			{
				echo "\t";
				printf($output_format, $tag['name'], esc_attr($tag['content']));
				echo "\n";
			}
		}
	}
}
