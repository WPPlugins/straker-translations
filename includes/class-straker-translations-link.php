<?php

class Straker_Link
{

	/**
	 *
	 */

	public function __construct()
	{}

	public function straker_post_link($permalink, $post, $leavename)
	{

		$locale              = get_locale();
		$sample              = (isset($post->filter) && 'sample' == $post->filter);
		$permalink_structure = get_option('permalink_structure');

		$using_permalinks = $permalink_structure &&
			($sample || !in_array($post->post_status, array('draft', 'pending', 'auto-draft')));

		$permalink = $this->straker_shortcode_url($permalink, $locale, array('using_permalinks' => $using_permalinks));

		return $permalink;

	}

	public function straker_home_url($url, $locale = '')
	{

		if (is_admin() || !did_action('template_redirect')) {
			return $url;
		}

		if ($locale === '') {
			$locale = get_locale();
		}

		$rewrite_option = Straker_Translations_Config::straker_rewrite_type();

		if ($locale != $GLOBALS['straker_default']) {
			if ( $rewrite_option === 'code') {
				$args = array('using_permalinks' => (bool) get_option('permalink_structure'));
				return $this->straker_shortcode_url($url, $locale, $args);
			} else if ( $rewrite_option === 'domain' ) {

				$straker_urls = get_option( Straker_Translations_Config::straker_option_urls );
				$straker_added_language   = Straker_Language::get_added_language();
				$code = '';

				foreach ($straker_added_language as $value) {
					if ($value['wp_locale'] === $locale) {
						$code = $value['code'];
					}
				}

				return $straker_urls[$code];
			} else {
				return $url;
			}
		} else {
			return $url;
		}

	}

	public function straker_page_link($permalink, $id, $sample)
	{

		$locale = get_locale();
		$post   = get_post($id);

		$permalink_structure = get_option('permalink_structure');

		$using_permalinks = $permalink_structure &&
			($sample || !in_array($post->post_status, array('draft', 'pending', 'auto-draft')));

		$permalink = $this->straker_shortcode_url($permalink, $locale, array('using_permalinks' => $using_permalinks));

		return $permalink;
	}

	public function straker_shortcode_url($url = null, $locale = null, $args = '')
	{

		$defaults = array('using_permalinks' => true);

		$args = wp_parse_args($args, $defaults);

		if (!$url) {
			$url = is_ssl() ? 'https://' : 'http://';
			$url .= $_SERVER['HTTP_HOST'];
			$url .= $_SERVER['REQUEST_URI'];

			if ($frag = strstr($url, '#')) {
				$url = substr($url, 0, -strlen($frag));
			}

			if ($query = @parse_url($url, PHP_URL_QUERY)) {
				parse_str($query, $query_vars);

				foreach (array_keys($query_vars) as $qv) {
					if (!get_query_var($qv)) {
						$url = remove_query_arg($qv, $url);
					}
				}
			}
		}

		$home = set_url_scheme(get_option('home'));
		$home = trailingslashit($home);

		$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
		$url       = remove_query_arg('lang', $url);

		if (!$args['using_permalinks']) {
			$url = add_query_arg(array('lang' => $lang_meta['short_code']), $url);
			return $url;
		}

		$regex = Straker_Language::shortcode_regex();
		$url   = preg_replace('#^' . preg_quote($home) . '(' . $regex . '/)?#', $home . $lang_meta['short_code'] . '/', trailingslashit($url));
		return $url;

	}

	public function straker_default_home()
	{

		$url = get_option('home');
		return $url;

	}

	public function straker_locale_home($wp_locale)
	{

		$url = $this->straker_home_url($url = home_url(), $locale = $wp_locale);
		return $url;

	}

}
