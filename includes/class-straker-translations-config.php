<?php

/**
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 */

/**
 * @since      1.0.0
 *
 * @author     Straker Translations <extensions@strakertranslations.com>
 */
class Straker_Translations_Config
{
	/**
	 * Short Description. (use period).
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */

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

	const straker_meta_locale             = 'straker_locale';
	const straker_meta_default            = 'straker_default_';
	const straker_option_urls             = 'straker_urls';
	const straker_option_jobs             = 'straker_jobs';
	const straker_option_sandbox_jobs     = 'straker_sandbox_jobs';
	const straker_option_job              = 'straker_job_';
	const straker_option_imported         = 'straker_imported_';
	const straker_option_translation_resx = 'straker_download_translation_';
	const straker_option_languages        = 'straker_languages';
	const straker_option_shortcode        = 'straker_shortcode_settings';
	const straker_option_auth             = 'straker_auth';
	const straker_option_sandbox		  = 'straker_sandbox';
	const straker_option_translation_cart = 'straker_translation_cart';
	const straker_option_menu_switcher    = 'straker_translation_menu_language_switcher';
	const straker_meta_target             = 'straker_target';
	const straker_wp_locale               = 'wp_locale';
	const straker_short_code              = 'short_code';
	const straker_option_rewrite          = 'straker_rewrite';
	const straker_cat_lang_meta           = 'straker_category_language';
	const straker_tag_lang_meta           = 'straker_tag_language';
	const straker_registered_posts        = 'straker_get_registered_posts';
	const straker_translated_revision_id  = 'straker_updated_translation_revison_id';
	const straker_app_version             = 'v1';
	const straker_app_hash                = 'ta2wo';
	const straker_myaccount_callback      = '/wp-admin/admin.php?page=st-callback';
	const straker_support_message         = 'Oops! Something went wrong. Please contact our support team at extensions@strakertranslations.com using your email client.';

	public function __construct()
	{
		self::straker_url();
	}

	public static function straker_url()
	{
		if (self::straker_sandbox_mode() === 'true') {
			$straker_api_url = 'https://app-sandbox.strakertranslations.com/';
		} else {
			$straker_api_url = 'https://app.strakertranslations.com/';
		}
    $straker_myaccount_url = 'https://myaccount.strakertranslations.com/';
    $straker_quote_url = 'https://myaccount.strakertranslations.com/o/?action=api.quote&';
    $straker_buglog_url = 'https://buglog.strakertranslations.com/bugLog/listeners/bugLogListenerREST.cfm';

		define('STRAKER_API', $straker_api_url);
		define('STRAKER_MYACCOUNT', $straker_myaccount_url);
		define('STRAKER_QUOTE', $straker_quote_url);
		define('STRAKER_BUGLOG', $straker_buglog_url);
	}

	public static function straker_api_url($method = '')
	{
		$constants           = get_defined_constants(true);
		$straker_api_url     = $constants['user']['STRAKER_API'];
		$straker_app_version = self::straker_app_version . '/';
		$api_hash            = self::straker_app_hash . '/';
		$api_end             = '';

		switch ($method) {
			case 'languages':
				$api_end = $straker_api_url . $straker_app_version . $method;
				break;
			case 'countries':
				$api_end = $straker_api_url . $straker_app_version . $method;
				break;
			case 'register':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			case 'translate':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			case 'translate/cancel':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			case 'myaccount/token':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			case 'myaccount/authorize':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			case 'support':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			case 'flag':
				$api_end = $straker_api_url . $method . '/';
				break;
			case 'test/delete':
				$api_end = $straker_api_url . $straker_app_version . $api_hash . $method;
				break;
			default:
				$api_end = $straker_api_url;
				break;
		}

		return $api_end;
	}

	public static function straker_rewrite_type()
	{
		$rewrite = get_option(self::straker_option_rewrite);
		switch ($rewrite['rewrite_type']) {
			case 'domain':
				return 'domain';
				break;
			case 'code':
				return 'code';
				break;
			case 'none':
				return 'none';
				break;
			default:
				return '';
				break;
		}
	}

	public static function get_straker_countries()
	{
		$json_countries         = file_get_contents(plugin_dir_path(__FILE__) . 'countries.json');
		$body_countries         = json_decode($json_countries, true);
		$straker_countries_list = $body_countries['country'];
		return $straker_countries_list;
	}

	public static function straker_sandbox_mode()
	{
		$sandbox_mode = get_option(self::straker_option_sandbox);
		if (!$sandbox_mode) {
			add_option(self::straker_option_sandbox, 'true');
			$sandbox_mode = get_option(self::straker_option_sandbox);
		}
		return $sandbox_mode;
	}

}
