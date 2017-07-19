<?php

class Straker_Locale
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 * @var string The current version of this plugin.
	 */
	private $version;

	/**
	 *
	 */

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		global $straker_default;
		$straker_default   = $this->default_locale();
	}

	public function straker_locale($locale)
	{
		global $wp_rewrite, $wp_query;
		if (is_admin()) {
			return $locale;
		}

		// set locale by URL
		$rewrite   = Straker_Translations_Config::straker_rewrite_type();

		if ($rewrite === 'domain') {
			$urls     = get_option(Straker_Translations_Config::straker_option_urls);
			$urls 		= array_map( 'esc_url', $urls );

			$site_url = site_url();

			if (in_array($site_url, $urls)) {
				$lang   = array_search($site_url, $urls);
				$locale = $this->wp_locale($lang);
				return $locale;
			}
		}

		// set locale by Language code
		if ($rewrite === 'code') {
			$locale = $this->langage_code();
			if ($locale) {
				return $locale;
			}
		}

		$locale = $this->default_locale();
		return $locale;
	}

	public function straker_query_vars($query_vars)
	{
		$query_vars[] = 'lang';
		return $query_vars;
	}

	public function default_locale()
	{
		if (defined('WPLANG')) {
			$locale = WPLANG;
		}

		if (is_multisite()) {
			if (defined('WP_INSTALLING') || (false === $ms_locale = get_option('WPLANG'))) {
				$ms_locale = get_site_option('WPLANG');
			}

			if ($ms_locale !== false) {
				$locale = $ms_locale;
			}
		} else {
			$db_locale = get_option('WPLANG');

			if ($db_locale !== false) {
				$locale = $db_locale;
			}
		}

		if (empty($locale)) {
			$locale = 'en_US';
		}

		return $locale;
	}

	public function wp_locale($lang)
	{
		$lang_meta = Straker_Language::straker_language_meta('code', $lang);
		$locale    = $lang_meta[Straker_Translations_Config::straker_wp_locale];
		return $locale;
	}

	public function langage_code()
	{
		$added_language    = Straker_Language::get_added_language();
		$straker_shortcode = Straker_Language::get_shortcode($added_language, 'code');

		$url = is_ssl() ? 'https://' : 'http://';
		$url .= $_SERVER['HTTP_HOST'];
		$url .= $_SERVER['REQUEST_URI'];

		$home  = set_url_scheme(get_option('home'));
		$home  = trailingslashit($home);
		$regex = '#^' . preg_quote($home) . '(' . implode('|', $straker_shortcode) . ')/#';

		if (preg_match($regex, trailingslashit($url), $matches)) {
			$lang_meta = Straker_Language::straker_language_meta('short_code', $matches[1]);
			$locale    = $lang_meta[Straker_Translations_Config::straker_wp_locale];
			return $locale;
		}

		// for Permalink Settings : Plain
		if ($query = @parse_url($url, PHP_URL_QUERY)) {
			parse_str($query, $query_vars);
		}

		if (isset($query_vars['lang']) && in_array($query_vars['lang'], $straker_shortcode)) {
			$lang_meta = Straker_Language::straker_language_meta('short_code', $query_vars['lang']);
			$locale    = $lang_meta[Straker_Translations_Config::straker_wp_locale];
			return $locale;
		}

		return false;
	}

}
