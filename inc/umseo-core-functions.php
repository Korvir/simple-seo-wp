<?php
/*
*
*	***** UmTale SEO *****
*
*	Core Functions
*
*/
// If this file is called directly, abort. //
if (!defined('WPINC'))
{
	die;
} // end if

function umseo_get_option( $option, $section, $default = '' )
{
	$options = get_option($section);

	if (isset($options[ $option ]))
	{
		return $options[ $option ];
	}

	return $default;
}

function umseo_get_term_meta( $term_id, $meta_key )
{
	global $wpdb;
	$meta_cache = wp_cache_get($term_id, 'term_meta');
	$meta_value = '';

	if (!$meta_cache)
	{
		$meta_cache = wp_cache_get('umseo_' . $term_id, 'umseo_term_meta');
	}

	if ($meta_cache && isset($meta_cache[ $meta_key ]))
	{
		if (is_array($meta_cache[ $meta_key ]))
		{
			$meta_value = $meta_cache[ $meta_key ][0];
		} else
		{
			$meta_value = $meta_cache[ $meta_key ];
		}
	} else
	{
		if (!$meta_cache)
		{
			$meta_cache = [];
		}

		$meta_value = $wpdb->get_var($wpdb->prepare(
			"SELECT meta_value FROM $wpdb->termmeta WHERE meta_key = %s AND term_id = %s", $meta_key, $term_id
		));

		if (!$meta_value)
		{
			$meta_value = '';
		}

		$meta_cache[ $meta_key ] = $meta_value;

		wp_cache_set('umseo_' . $term_id, $meta_cache, 'umseo_term_meta', HOUR_IN_SECONDS);
	}

	return $meta_value;
}

function umseo_excerpt( $args = '' )
{
	global $post;

	if (is_string($args))
		parse_str($args, $args);

	$rg = (object)array_merge(array(
		'maxchar' => 350,
		'text' => '',
		'autop' => true,
		'save_tags' => '',
		'more_text' => '...',
		'ignore_more' => false,
	), $args);

	$rg = apply_filters('umseo_excerpt_args', $rg);

	if (!$rg->text)
		$rg->text = $post->post_excerpt ? : $post->post_content;

	$text = $rg->text;
	$text = preg_replace('~\[([a-z0-9_-]+)[^\]]*\](?!\().*?\[/\1\]~is', '', $text);
	$text = preg_replace('~\[/?[^\]]*\](?!\()~', '', $text);
	$text = trim($text);

	// <!--more-->
	if (!$rg->ignore_more && strpos($text, '<!--more-->'))
	{
		preg_match('/(.*)<!--more-->/s', $text, $mm);

		$text = trim($mm[1]);

		$text_append = ' <a href="' . get_permalink($post) . '#more-' . $post->ID . '">' . $rg->more_text . '</a>';
	} // text, excerpt, content
	else
	{
		$text = trim(strip_tags($text, $rg->save_tags));

		if (mb_strlen($text) > $rg->maxchar)
		{
			$text = mb_substr($text, 0, $rg->maxchar);
			$text = preg_replace('~(.*)\s[^\s]*$~s', '\\1...', $text);
		}
	}

	if ($rg->autop)
	{
		$text = preg_replace(
			array( "/\r/", "/\n{2,}/", "/\n/", '~</p><br ?/?>~' ),
			array( '', '</p><p>', '<br />', '</p>' ),
			$text
		);
	} else
	{
		$text = preg_replace(
			array( "/\r/", "/\n{2,}/" ),
			' ',
			$text
		);
	}

	$text = apply_filters('umseo_excerpt', $text, $rg);

	if (isset($text_append))
		$text .= $text_append;

	return ( $rg->autop && $text ) ? "<p>$text</p>" : $text;
}

function umseo_is_amp()
{
	return function_exists('is_amp_endpoint') && is_amp_endpoint() || get_query_var('amp') === 'amp';
}
