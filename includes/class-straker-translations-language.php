<?php

class Straker_Language
{

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 *
		 */
		public static $straker_languages      = array();
		public static $straker_site_languages = array();

		public function __construct()
		{

				self::$straker_languages      = self::get_json();
				self::$straker_site_languages = get_option(Straker_Translations_Config::straker_option_languages);
		}

		public static function straker_language_meta($key, $value)
		{

				$lang_meta = array();
				$lang_meta = self::search(self::get_json(), $key, $value);
				return $lang_meta;

		}

		public static function search($array, $key, $value)
		{
				$results = array();

				if (is_array($array)) {
						if (isset($array[$key]) && $array[$key] == $value) {
								$results = $array;
						}

						foreach ($array as $subarray) {
								$results = array_merge($results, self::search($subarray, $key, $value));
						}
				}

				return $results;
		}

		public static function get_lang_wp_locale($added_langs)
		{
				$lang_meta = array();
				$langs     = $added_langs;

				foreach ($langs as $value) {
						$aLan = array();
						$aLan = self::search(self::$straker_languages, 'code', $value);

						array_push($lang_meta, $aLan);
				}
				return $lang_meta;
		}

		public static function get_target_languages()
		{
				$lang_meta           = array();
				$array_diff_is       = array();
				$straker_added_langs = self::get_added_language();
				$straker_all_langs   = self::get_json();

				foreach ($straker_all_langs as $data1) {
						$duplicate = false;
						foreach ($straker_added_langs as $data2) {
								if ($data1['native_name'] === $data2['native_name'] && $data1['name'] === $data2['name'] && $data1['code'] === $data2['code']) {
										$duplicate = true;
								}

						}

						if ($duplicate === false) {
								$lang_meta[] = $data1;
						}

				}

				$array_diff_is = $lang_meta;
				return $array_diff_is;
		}

		public static function get_site_languages()
		{
				return self::$straker_site_languages;
		}

		public static function get_default_language()
		{

				$lang_meta = array();
				$langs     = self::$straker_site_languages;

				if ($langs === false) {
						return $lang_meta;
				} else {
						$lang_meta = self::search(self::$straker_languages, 'code', $langs['sl']);
						return $lang_meta;
				}

		}

		public static function get_added_language()
		{

				$lang_meta = array();
				$langs     = self::$straker_site_languages;

				if ($langs === false) {
						return $lang_meta;
				} else {

						$aTl = $langs['tl'];
						foreach ($aTl as $value) {
								$aLan = array();
								$aLan = self::search(self::$straker_languages, 'code', $value);
								array_push($lang_meta, $aLan);
						}
						return $lang_meta;
				}

		}

		public static function get_default_and_target_languages()
		{

				$lang_meta = array();
				$langs     = self::$straker_site_languages;

				if ($langs === false) {
						return $lang_meta;
				} else {
						array_push($lang_meta,self::search(self::$straker_languages, 'code', $langs['sl']));
						$aTl = $langs['tl'];
						foreach ($aTl as $value) {
								$aLan = array();
								$aLan = self::search(self::$straker_languages, 'code', $value);
								array_push($lang_meta, $aLan);
						}
						return $lang_meta;
				}
		}

		public static function get_shortcode($aLang, $key)
		{

				$aCode = array();
				foreach ($aLang as $value) {
						$shortcode = $value[Straker_Translations_Config::straker_short_code];
						array_push($aCode, $shortcode);
				}

				return $aCode;

		}

		public static function get_single_shortcode($aLang)
		{
				$aCode = '';
				foreach (self::get_default_and_target_languages() as $value) {
					if($value['code'] == $aLang)
					{
						$aCode =  $value['wp_locale'];
					}
				}
				return $aCode;
		}

		public static function get_json()
		{

				$json = file_get_contents(plugin_dir_path(dirname(__FILE__)) . '/includes/languages.json');
				$body = json_decode($json, true);
				$straker_languages = $body['languages'];

				return $straker_languages;

		}

		public static function get()
		{

				if (self::$straker_languages) {
					return self::$straker_languages;
				}

				$response = wp_remote_get(Straker_Translations_Config::straker_api_url('languages'));
				$body = json_decode($response['body'], true);
				self::$straker_languages = $body['languages'];

				return self::$straker_languages;

		}

		public static function shortcode_regex()
		{

				$added_language    = self::get_added_language();
				$straker_shortcode = self::get_shortcode($added_language, 'code');

				if (empty($straker_shortcode)) {
						return '';
				}

				return '(' . implode('|', $straker_shortcode) . ')';
		}

}
