<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/admin
 * @author     Straker Translations <extensions@strakertranslations.com>
 */

class Straker_Translations_Admin
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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	private $straker_api_workflow;
	public $straker_access_token;
	public $straker_application_key;
	private $straker_translation_cart;

	private $straker_languages       	= array();
	private $straker_countries       	= array();
	private $straker_auth            	= array();
	private $straker_site_languages  	= array();
	private $straker_default_language	= array();
	private $straker_added_languages 	= array();
	private $straker_target_language 	= array();
	private $straker_job_keys        	= array();
	private $straker_jobs            	= array();
	private $straker_imported        	= array();
	private $straker_urls            	= array();
	private $straker_sandbox_jobs    	= array();
	private $straker_posts_types		= array();
	private $straker_posts_status		= array();

	protected $straker_po_uploaded_file;
	protected $straker_registered_posts;
	protected $straker_def_targ_langs;
	protected $plugin_path_name;
	protected $plugin_file_name;
	protected $plugin_absolute_path;
	protected $flags_path;

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name          = $plugin_name;
		$this->version              = $version;
		$this->straker_api_workflow = 'TRANSLATION';

		$this->straker_auth = get_option(Straker_Translations_Config::straker_option_auth);
		if ($this->straker_auth != false) {
			$this->straker_access_token    = $this->straker_auth['access_token'];
			$this->straker_application_key = $this->straker_auth['application_key'];
		}

		$this->straker_languages        = Straker_Language::get_json();
		$this->straker_countries        = Straker_Translations_Config::get_straker_countries();
		$this->straker_site_languages   = Straker_Language::get_site_languages();
		$this->straker_default_language = Straker_Language::get_default_language();
		$this->straker_added_language   = Straker_Language::get_added_language();
		$this->straker_def_targ_langs 	= Straker_Language::get_default_and_target_languages();
		$this->straker_target_language  = Straker_Language::get_target_languages();
		$this->straker_urls             = get_option( Straker_Translations_Config::straker_option_urls );
		$this->straker_job_keys         = get_option( Straker_Translations_Config::straker_option_jobs );
		$this->st_rewrite_type          = Straker_Translations_Config::straker_rewrite_type();
		$this->st_relative_path			= STRAKER_PLUGIN_RELATIVE_PATH;
		$this->plugin_file_name			= STRAKER_PLUGIN_FILE;
		$this->plugin_absolute_path     = STRAKER_PLUGIN_ABSOLUTE_PATH;
		$this->flags_path               = STRAKER_PLUGIN_ABSOLUTE_PATH . '/assets/img/flags/';
		$this->straker_posts_types		= get_option( Straker_Translations_Config::straker_registered_posts );
		$this->straker_posts_status		= array( 'publish', 'pending', 'draft', 'future', 'private' );
		$this->straker_translation_cart = ( get_option( Straker_Translations_Config::straker_option_translation_cart )  ) ? explode(',', get_option( Straker_Translations_Config::straker_option_translation_cart ) ) : false ;
		$this->straker_sandbox_jobs     = get_option( Straker_Translations_Config::straker_option_sandbox_jobs );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook )
	{
		/**
		 * Register custom styles for the admin layout
		 *
		 * The Straker_Translations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		 wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/straker-translations-admin.css', array(), $this->version, 'all' );
		 if ( 'straker_page_st-translation-cart' === $hook || 'admin_page_st-translation' === $hook ) {
			 wp_enqueue_style( 'st-admin-confirm-css', plugin_dir_url( __FILE__ ) . 'css/straker-translations-admin-confirm.css', array(), $this->version, 'screen' );			 
		  }
		  
		  if ( 'straker_page_st-settings' === $hook ) { 
			  wp_enqueue_style( 'st-admin-settings-css', plugin_dir_url( __FILE__ ) . 'css/straker-translations-settings.css', array(), $this->version, 'all' );		
		  }
		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook )
	{
		/**
		 * Register custom javascript validation for the form.
		 *
		 * The Straker_Translations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this  
		 * class.
		 */
		
		wp_enqueue_script( 'jquery-validator', plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array('jquery'), $this->version, true );
		wp_enqueue_script( 'st-plugin-admin-js-file', plugin_dir_url( __FILE__ ) . 'js/straker-translations-admin.js', array('jquery'), $this->version, true );
		wp_enqueue_script('st-tooltip', plugin_dir_url( __FILE__ ) . 'js/straker-translations-admin-tooltip.js', array('jquery'), $this->version, true );
		wp_localize_script( 'st-plugin-admin-js-file', 'stCartAjaxObejct',
            array( 
				'admin_ajax_url' => admin_url( 'admin-ajax.php' ), 
				'st_cart_nonce' => wp_create_nonce( "st-cart-nonce" ),
				'successResponse' => '<span style="color:green;" class="dashicons dashicons-yes"></span> Item added into the <a href="'.admin_url('admin.php?page=st-translation-cart').'">cart.</a>',
				'errorResponse' => '<span style="color:red;" class="dashicons dashicons-no"></span> Item can\'t be add into the translation cart. Please try again.',
			) 
		);
		wp_enqueue_script( 'straker-google-analytics', plugin_dir_url( __FILE__ ) . 'js/straker-google-analytics.js', false, $this->version, true );

		if ( 'straker_page_st-settings' === $hook ) {
		
			wp_enqueue_script( 'jquery-ddslick', plugin_dir_url( __FILE__ ) . 'js/jquery.ddslick.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'st-clipboard-copy', plugin_dir_url( __FILE__ ) . 'js/clipboard.min.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'jquery-cookie', plugin_dir_url( __FILE__ ) . 'js/jquery-cookie.min.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'st-addMethod-jquery', plugin_dir_url( __FILE__ ) . 'js/additional-methods.min.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'st-settings-script', plugin_dir_url( __FILE__ ) . 'js/straker-translations-admin-settings-script.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'st-menu-switcher-script', plugin_dir_url( __FILE__ ) . 'js/settings/menu-switcher/straker-translations-menu-switcher.js', array('jquery'), $this->version, true );
			wp_localize_script(
				'st-menu-switcher-script', 
				'stMenuSwitcherObj', 
				array(
					'st_lang_switcher_nonce' => wp_create_nonce( "st-lang-switcher-nonce" ),
					'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
					'content' => __( "Loading Items....", $this->plugin_name ),
					'imgsrc'  => plugin_dir_url( __FILE__ ).'/img/loading.gif',
				)
			);
			
		}

		if ( 'admin_page_st-reimport' === $hook ) {
			wp_enqueue_script('st-import-resx', plugin_dir_url( __FILE__ ) . 'js/straker-translations-admin-import-resx.js', array('jquery'), $this->version, true );
			wp_localize_script( 'st-import-resx', 'ST_Import_Resx', 
				array(
					'security' => wp_create_nonce('st-import-resx-file')
				)
			);
		}

		if ( 'straker_page_st-translation-cart' === $hook ) {

			wp_enqueue_script( 'st-translations-cart', plugin_dir_url( __FILE__ ) . 'js/straker-translations-translation-cart.js', array('jquery'), $this->version, true );
			wp_enqueue_script( 'st-jquery-confirm', plugin_dir_url( __FILE__ ) . 'js/straker-translations-jquery-confirm.js', array('jquery'), $this->version, true );
			wp_localize_script(
				'st-translations-cart', 
				'ST_Trans_Cart', 
				array(
					'admin_ajax_url' => admin_url( 'admin-ajax.php' ),
					'content' => __( "Loading Items....", $this->plugin_name ),
					'imgsrc'  => plugin_dir_url( __FILE__ ).'/img/loading.gif',
				)
			);
		}

		if ( 'admin_page_st-translation' === $hook ) {
			wp_enqueue_script(
				'st-admin-trans-ajax-js-file', 
				plugin_dir_url( __FILE__ ) . 'js/straker-translations-admin-ajax-list-table.js', 
				array('jquery'), 
				$this->version, 
				true 
			);
			wp_enqueue_script(
				'st-admin-trans-dashboard', 
				plugin_dir_url( __FILE__ ) . 'js/straker-translations-dashboard.js', 
				array('jquery'), 
				$this->version, 
				true 
			);
			wp_localize_script(
				'st-admin-trans-ajax-js-file', 
				'WP_Load_POSTs', 
				array(
					'content' => __( "Loading Items....", $this->plugin_name ),
					'imgsrc'  => plugin_dir_url( __FILE__ ).'/img/loading.gif',
				)
			);
			wp_enqueue_script( 'st-jquery-confirm', plugin_dir_url( __FILE__ ) . 'js/straker-translations-jquery-confirm.js', array('jquery'), $this->version, true );
		}

	}

	public function straker_get_version()
	{
		$plugin_data    = get_plugin_data( STRAKER_PLUGIN_RELATIVE_PATH . '/straker-translations.php');
		$plugin_version = $plugin_data['Version'];
		return $plugin_version;
	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function straker_admin_menu()
	{
		global $submenu;
		$cart_item = '';
		if ( $this->straker_translation_cart && count( $this->straker_translation_cart ) > 0 ) {
			$cart_item = sprintf(' <span class="update-plugins count-%1$s" style="background:#0073aa"><span class="update-count">%1$s</span></span>', count( $this->straker_translation_cart ) );
		}
		add_menu_page(
			__('Straker Translations', 'straker-translations'),
			__('Straker ', 'straker-translations'),
			'manage_options',
			'st-start',
			array($this, 'display_start_page'),
			plugins_url('straker-translations/admin/img/favicon.png')
		);

		add_submenu_page('st-start', 'Translation Settings', 'Settings', 'manage_options', 'st-settings', array($this, 'display_settings_page'));
		add_submenu_page('st-start', 'Translation Jobs', 'My Jobs', 'manage_options', 'st-jobs', array($this, 'display_jobs_page'));
		add_submenu_page('st-jobs', '', 'Translations', 'manage_options', 'st-translation', array($this, 'display_translation_page'));
		add_submenu_page('st-jobs', 'Re Import Content', 'Re Import', 'manage_options', 'st-reimport', array($this, 'display_straker_re_import_content'));
		add_submenu_page('st-jobs', 'Translate More', 'Translate More', 'manage_options', 'st-tm', array($this, 'translate_more_link'));
		add_submenu_page('st-start', 'Translation Cart', 'Cart '. $cart_item, 'manage_options', 'st-translation-cart', array($this, 'display_straker_translation_cart'));
		add_submenu_page('st-start', 'Translation Support', 'Support', 'manage_options', 'st-support', array($this, 'display_straker_support'));
		add_submenu_page('st-callback', 'MyAccount Callback', 'Callback', 'manage_options', 'st-callback', array($this, 'display_straker_callback'));
		$submenu['st-start'][0][0] = 'Quick Start';
	}

	// Be used for myaccount auth callback
	public function app_output_buffer()
	{
		ob_start();
	} // soi_output_buffer

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_start_page()
	{
		include_once 'partials/start/quick-start.php';
	}

	public function display_settings_page()
	{
		include_once 'partials/settings/index.php';
	}

	public function display_translation_page()
	{
		include_once 'partials/straker-translations-admin-translation.php';
	}

	public function display_jobs_page()
	{
		include_once 'partials/jobs/listing/all-jobs.php';
	}

	public function display_straker_translation_cart()
	{
		include_once 'partials/straker-translations-admin-order.php';
	}

	public function translate_more_link()
	{
		include_once 'partials/translate-more/translate-more.php';
	}

	public function display_straker_support()
	{
		include_once 'partials/support/support.php';
	}

	public function display_straker_callback()
	{
		include_once 'partials/straker-translations-admin-callback.php';
	}

	public function display_straker_re_import_content()
	{
		include_once 'partials/content-import/reimport-content.php';
	}

	public function straker_taxonomy_add_page_custom_meta_field()
	{
		include_once 'partials/taxonomies-custom-meta-fields/taxonomy-add-form.php';
	}
	public function straker_taxonomy_edit_page_custom_meta_field( $term )
	{
		include_once 'partials/taxonomies-custom-meta-fields/taxonomy-edit-form.php';
	}
	public function straker_add_tag_page_custom_meta_field()
	{
		include_once 'partials/tags-meta-fields/add-tag-form.php';
	}
	public function straker_edit_tag_custom_fields($term)
	{
		include_once 'partials/tags-meta-fields/edit-tag-form.php';
	}


	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function straker_translation_plugin_action_links( $links )
	{
		$settings_link = array(
			'<a href="' . admin_url('admin.php?page=st-start') . '">' . __( 'Quick Start', $this->plugin_name ) . '</a>',
			'<a href="' . admin_url('admin.php?page=st-settings') . '">' . __( 'Settings', $this->plugin_name ) . '</a>',
			'<a href="https://help.strakertranslations.com/docs/wordpress/" target="_blank">' . __( 'Help', $this->plugin_name ) . '</a>',
		);
		return array_merge( $settings_link, $links );
	}

	/**
	 * Add Highlight parent menu from submenu.
	 *
	 * @since    1.0.0
	 */
	public function straker_plugin_select_submenu($menu_slug)
	{
		global $plugin_page;
		if ("st-translation" == $plugin_page) {
			$plugin_page = 'st-jobs';
		} elseif ("st-import" == $plugin_page) {
			$plugin_page = 'st-jobs';
		} elseif ("st-reimport" == $plugin_page) {
			$plugin_page = 'st-jobs';
		} elseif ("st-tm" == $plugin_page) {
			$plugin_page = 'st-jobs';
		}
		return $menu_slug;
	}

	/**
	 * returns the function specific api url
	 *
	 * @since  1.0.0
	 */
	public function straker_api($method = '')
	{
		$straker_api_url = Straker_Translations_Config::straker_api_url($method);
		return $straker_api_url;
	}

	/**
	 * returns the function specific myaccount url
	 *
	 * @since  1.0.0
	 */
	public function straker_myaccount($method = '')
	{
		$constants     = get_defined_constants(true);
		$myaccount_url = $constants['user']['STRAKER_MYACCOUNT'];
		return $myaccount_url . $method;
	}

	public function straker_myaccount_link()
	{
		$api_sig = md5('access_token'.$this->straker_access_token.'app_key'.$this->straker_application_key);
		return $api_sig;
	}


	/**
	 * returns the function specific quote url
	 *
	 * @since  1.0.0
	 */
	public function straker_quote($method = '')
	{
		$constants = get_defined_constants(true);
		$quote_url = $constants['user']['STRAKER_QUOTE'];
		return $quote_url . $method;
	}

	/**
	 * returns the trunciated title
	 *
	 * @since  1.0.0
	 */

	public function straker_posts_words_count($post_title, $post_content)
	{
		$post_title_no_tags 	= wp_strip_all_tags($post_title,true);
		$post_content_no_tags = wp_strip_all_tags($post_content,true);
		$words_count = str_word_count($post_title_no_tags,0)+str_word_count($post_content_no_tags,0);

		return $words_count;
	}

	/**
	 * send support form
	 *
	 * @since  1.0.0
	 */
	public function straker_support()
	{

		if (!empty($_POST) && check_admin_referer('straker-translations-support', 'straker-translations-support-nonce')) {
			if ($this->straker_auth === false) {
				$current_user  = wp_get_current_user();
				$email_subject = 'Straker WordPress Plugin Support';
				$name          = sanitize_text_field($_POST["name"]);
				$email_address = sanitize_email($_POST["email_address"]);
				$detail        = esc_textarea($_POST["detail"]);
				$message       = "Name : " . $name . "\n";
				$message .= "Email Address : " . $email_address . "\n";
				$message .= "Detail : " . $detail . "\n";
				$send_to = 'extensions@strakertranslations.com';
				$headers = 'From: ' . $current_user->user_firstname . ' ' . $current_user->user_lastname . ' <extensions@strakertranslations.com>' . "\r\n";
				if (wp_mail($send_to, $email_subject, $message, $headers)) {
					wp_redirect(admin_url('admin.php?page=st-support&msg=success'));
					exit();
				} else {
					wp_redirect(admin_url('admin.php?page=st-support&msg=failed'));
					exit();
				}
			} else {
				$url     = $this->straker_api('support');
				$headers = array(
					'Authorization' => 'Bearer ' . $this->straker_access_token,
					'X-Auth-App'    => $this->straker_application_key,
				);
				$job_id = '';
				if (!empty($_POST["job_id"])) {
					$job_id = sanitize_text_field($_POST["job_id"]);
				}
				$post_fields = array(
					"name"        => sanitize_text_field($_POST["name"]),
					"email"       => sanitize_email($_POST["email_address"]),
					"job_id"      => sanitize_text_field($_POST["job_id"]),
					"category"    => sanitize_text_field($_POST["category"]),
					"detail"      => esc_textarea($_POST["detail"]),
					"app_version" => $this->straker_get_version(),
					"url"         => site_url(),
				);
				$response = wp_remote_post(
					$url, array(
						'headers' => $headers,
						'body'    => $post_fields,
					)
				);
				$result = json_decode($response['body']);
				if ($result->success === true) {
					wp_redirect(admin_url('admin.php?page=st-support&msg=success'));
				} else {
					$this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_support', $eMessage = 'Failed at sending support email from API.', $eDetail = $result->message, $HTMLReport = $this->straker_buglog_html_report( $this->straker_access_token, $result->message, $result->code, __LINE__ ) );
					wp_redirect(admin_url('admin.php?page=st-support&msg=failed'));
				}
			}
		}
	}

	/**
	 * register for Straker Account
	 *
	 * @since  1.0.0
	 */
	public function straker_register()
	{
		if (!empty($_POST) && check_admin_referer('straker-translations-register', 'straker-translations-register-nonce')) {
			$url      = $this->straker_api('register');
			$response = wp_remote_post($url,
				array(
					'method' => 'POST',
					'body'   => array(
						'first_name'   => sanitize_text_field($_POST['first_name']),
						'last_name'    => sanitize_text_field($_POST['last_name']),
						'email'        => sanitize_email($_POST['email_address']),
						'country'      => sanitize_text_field($_POST['country']),
						'company'      => sanitize_text_field($_POST['company_name']),
						'phone'        => sanitize_text_field($_POST['phone_number']),
						'url'          => get_site_url(),
						'callback_url' => get_site_url() . Straker_Translations_Config::straker_myaccount_callback,
						'meta_data'    => $this->getPluginList()
					),
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$body = $response['body'];
				$json = json_decode($body, true);
				if ($json['success']) {
					$new_keys = array(
						'access_token'    => $json['access_token'],
						'application_key' => $json['application_key'],
					);
					if ($this->straker_auth === false) {
						add_option('straker_auth', $new_keys);
					} else {
						update_option('straker_auth', $new_keys);
					}
					wp_redirect(admin_url('admin.php?page=st-settings&msg=success&ac=register'));
					exit();
				} else {

					$this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_register', $eMessage = 'Failed at registering.', $eDetail = $json['message'], $HTMLReport = $this->straker_buglog_html_report( '', $json['message'], $json['code'], __LINE__ ) );
					wp_redirect(admin_url('admin.php?page=st-settings&msg=error'));
					exit();
				}
			} else {
				$error_code = ( ! is_null( $response->get_error_code() ) ) ? $response->get_error_code() : '';
				$error_message = ( ! is_null( $response->get_error_message() ) ) ? $response->get_error_message() : '';
				$this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_register', $eMessage = 'Failed at registering.', $eDetail = $error_message, $HTMLReport = $this->straker_buglog_html_report( '', $error_message, $error_code, __LINE__ ) );
				wp_redirect(admin_url('admin.php?page=st-settings&msg=error'));
				exit;
			}
		} else {
			wp_redirect(admin_url('admin.php?page=st-settings&msg=error'));
			exit();
		}
	}

	/**
	 * Update the general settings for the site
	 *
	 * @since  1.0.0
	 */
	public function straker_general_settings()
	{
		if( check_admin_referer('straker-translations-general-settings', 'straker-translations-general-settings-nonce') &&
				!empty($_POST['sandbox_mode'])
		  ) {
			$sandbox_mode = sanitize_text_field($_POST['sandbox_mode']);
			update_option(Straker_Translations_Config::straker_option_sandbox, $sandbox_mode);
			wp_redirect(admin_url('admin.php?page=st-settings&msg=success&ac=plugin_mode'));
		}	else {
			wp_redirect(admin_url('admin.php?page=st-settings'));
		}
	}

	/**
	 * Update the general settings for the site
	 *
	 * @since  1.0.0
	 */
	public function straker_test_delete()
	{
		if( check_admin_referer('straker-translations-test-delete', 'straker-translations-test-delete-nonce') ) {
			$url     = $this->straker_api('test/delete');
			$headers = array(
				'Authorization' => 'Bearer ' . $this->straker_access_token,
				'X-Auth-App'    => $this->straker_application_key,
			);
			$response = wp_remote_post($url,
				array(
					'headers' => $headers,
					'body'   => array(),
				)
			);
			// Delete page, post created by test job
			foreach ($this->straker_sandbox_jobs as $key => $value) {
				wp_delete_post($value, true);
			}
			wp_redirect(admin_url('admin.php?page=st-settings&msg=success&ac=plugin_mode'));
		}	else {
			wp_redirect(admin_url('admin.php?page=st-settings'));
		}
	}

	/**
	 * add or update the source and target language for the site
	 *
	 * @since  1.0.0
	 */
	public function straker_language_settings()
	{
		if(	check_admin_referer('straker-translations-language-settings', 'straker-translations-language-settings-nonce') &&
				!empty($_POST['tl'])
			) {
			$target_langs 	= isset( $_POST['tl'] ) ? (array) $_POST['tl'] : array();
			$target_langs 	= array_map( 'esc_attr', $target_langs );
			$source_lang  	= sanitize_text_field($_POST['sl']);
			$builtin_types	= array('post' => 'post','page' =>'page' );
			$all_post_types = ( get_option( Straker_Translations_Config::straker_registered_posts ) ) ? get_option( Straker_Translations_Config::straker_registered_posts ) : $builtin_types;
			$aData        	= array(
				'sl' => $source_lang,
				'tl' => $target_langs,
			);

			if ( ! $this->straker_site_languages ) {
				add_option(Straker_Translations_Config::straker_option_languages, $aData);

				$args = array(
					'post_status' => $this->straker_posts_status,
					'post_type' => $all_post_types,
					'posts_per_page' => -1,
					'meta_query' => array(
						array(
							'key' => Straker_Translations_Config::straker_meta_locale,
							'compare' => 'NOT EXISTS' // this should work...
						),
					),
				);
				$query = new WP_Query($args);

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();
						add_post_meta( get_the_ID(), Straker_Translations_Config::straker_meta_locale, $source_lang );
					}
					wp_reset_postdata();
				}
	
			} else {
				update_option(Straker_Translations_Config::straker_option_languages, $aData);
			}
			if ( ! Straker_Translations_Config::straker_rewrite_type() ) {
				wp_redirect(admin_url('admin.php?page=st-settings&tab=url_settings&msg=success&ac=lang_url_setting'));
			} else
			{
				wp_redirect(admin_url('admin.php?page=st-settings&tab=language_settings&msg=success&ac=language'));
			}
		} else {
			wp_redirect(admin_url('admin.php?page=st-settings&tab=language_settings&msg=failed'));
		}
	}

	public function straker_url_settings()
	{
		if( check_admin_referer('straker-translations-url-settings', 'straker-translations-url-settings-nonce') &&
				!empty($_POST['rewrite_type'])
		  ) {
			$rewrite_type = sanitize_text_field($_POST['rewrite_type']);
			$aRewrite     = array('rewrite_type' => $rewrite_type);
			if (!Straker_Translations_Config::straker_rewrite_type()) {
				add_option(Straker_Translations_Config::straker_option_rewrite, $aRewrite);
			} else {
				update_option(Straker_Translations_Config::straker_option_rewrite, $aRewrite);
			}
			// save url options only for rewrite domain type
			if ($rewrite_type === 'domain') {
				$target_langs 	= isset( $_POST['lang'] ) ? (array) $_POST['lang'] : array();
				$target_langs 	= array_map( 'esc_attr', $target_langs );
				$aUrl 					= array();
				$urls 					= isset( $_POST['url'] ) ? (array) $_POST['url'] : array();
				$urls 					= array_map( 'esc_attr', $urls );
				foreach ($target_langs as $key => $value) {
					$aUrl[$value] = esc_url($urls[$key]);
				}
				if ($this->straker_site_languages === false) {
					add_option(Straker_Translations_Config::straker_option_urls, $aUrl);
				} else {
					update_option(Straker_Translations_Config::straker_option_urls, $aUrl);
				}
			}
			wp_redirect(admin_url('admin.php?page=st-settings&tab=url_settings&msg=success&ac=url'));
		}	else {
			wp_redirect(admin_url('admin.php?page=st-settings&tab=url_settings&msg=failed'));
		}
	}

	/**
	 * add or update the source and target language for the site
	 *
	 * @since  1.0.0
	 */
	public function straker_shortcode_settings()
	{
		if(	check_admin_referer('straker-translations-shortcode-settings', 'straker-translations-shortcode-settings-nonce'))
		{
			$avail_langs 		= isset( $_POST['tl'] ) ? (array) $_POST['tl'] : array();
			$avail_langs 		= array_map( 'esc_attr', $avail_langs );
			$dis_flags	  	= isset( $_POST['display_flags'] ) ? sanitize_text_field($_POST['display_flags']):"off";
			$dis_langs	  	= isset( $_POST['display_langs'] ) ? sanitize_text_field($_POST['display_langs']):"off";
			$dis_horiz	  	= isset( $_POST['display_horizontal'] ) ? sanitize_text_field($_POST['display_horizontal']):"off";
			$shortcode_opt	=	Straker_Translations_Config::straker_option_shortcode;
			$shortcode_data	= array(
				'available_langs' => $avail_langs,
				'display_flags' => $dis_flags,
				'display_langs' => $dis_langs,
				'display_horiz' => $dis_horiz
			);

			if (!get_option($shortcode_opt)) {
				add_option($shortcode_opt, $shortcode_data);
			} else {
				update_option($shortcode_opt, $shortcode_data);
			}
			wp_redirect(admin_url('admin.php?page=st-settings&msg=success&ac=shortcode'));
		} else {
			wp_redirect(admin_url('admin.php?page=st-settings&msg=failed'));
		}
	}

	/**
	 *
	 */
	public function straker_job_title()
	{
		$job_title = preg_replace('/\s+/', '', get_bloginfo('name')) . '_' . date('YmdHis', current_time('timestamp', 1));
		return $job_title;
	}

	public function straker_content_query(array $post_ids)
	{
		$post_query = new WP_Query();

		if ($post_ids) {
			$post_query = new WP_Query(
				array(
					'post__in'  => $post_ids,
					'orderby' => 'title',
					'order'   => 'ASC',
					'post_type' => get_post_types(),
					'posts_per_page' => -1
				)
			);
		}
		return $post_query;
	}

	/**
	 * Send the job for translation. This function is requesting quotation.
	 *
	 * @since  1.0.0
	 */
	public function straker_timeout_time($time)
	{
		$time = 25; //number of seconds
		return $time;
	}

	public function straker_request_quote()
	{
		if (
			!empty($_POST) &&
			check_admin_referer('straker-translations-request-quote', 'straker-translations-request-quote-nonce') &&
			isset($_POST['sl']) &&
			isset($_POST['tl']) &&
			isset($_POST['title']) &&
			!empty($_POST['title']) &&
			!empty($_POST['sl']) &&
			array_filter($_POST['post_page']) &&
			array_filter($_POST['tl'])
		) {
			$url          = $this->straker_api('translate');
			$post_ids 		= isset( $_POST['post_page'] ) ? (array) $_POST['post_page'] : array();
			$post_ids 		= array_map( 'esc_attr', $post_ids );
			$job_title    = sanitize_text_field($_POST['title']);
			$soruce_lang  = sanitize_text_field($_POST['sl']);
			$target_langs = isset( $_POST['tl'] ) ? (array) $_POST['tl'] : array();
			$target_langs = array_map( 'esc_attr', $target_langs );
			$notes        = sanitize_text_field($_POST['notes']);
			$straker_xml  = new Straker_XML('<?xml version="1.0" encoding="utf-8"?><root></root>');
			$post_query		= $this->straker_content_query($post_ids);
			$st_trans_cart = isset( $_POST['st_translation_cart'] ) ? sanitize_text_field( $_POST['st_translation_cart'] ) : false;
			// $post_shcodes = $this->straker_extract_shortcodes( $post_ids );
			$post_shcodes = "";
			$yoast_seo		= (isset($_POST['yoast']) ? true : false);
			$acf_plugin   = (isset($_POST['acf-plugin']) ? true : false);
			$resx					= $straker_xml->straker_generate_resx( $straker_xml, $post_query, $yoast_seo, $acf_plugin );
			if ($resx) {
				$boundary = wp_generate_password(24);
				$headers  = array(
					'Authorization' => 'Bearer ' . $this->straker_access_token,
					'X-Auth-App'    => $this->straker_application_key,
					'content-type'  => 'multipart/form-data; boundary=' . $boundary,
				);
				$yo = '';
				$acf = '';
				if ($yoast_seo) {
					$yo = '===== Yoast SEO Job =====';
				}
				if ($acf_plugin) {
					$acf = '===== Advanced Custom Fields Job =====';
				}
				$all_note = $yo.$acf.sanitize_text_field($_POST['name']).':'.sanitize_text_field($_POST['email']).':'.$notes;

				$post_fields = array(
					'title' => sanitize_text_field($job_title),
					'sl'    => sanitize_text_field($soruce_lang),
					'tl'    => implode(',', $target_langs),
					'token' => implode(',', $post_ids),
					// 'reserved_word' => implode( ',', $post_shcodes ),
					'reserved_word' => $post_shcodes,
					'notes' => $all_note,
				);
				$body = '';
				// First, add the standard POST fields:
				foreach ($post_fields as $name => $value) {
					$body .= '--' . $boundary;
					$body .= "\r\n";
					$body .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
					$body .= $value;
					$body .= "\r\n";
				}
				// Add the source_file field:
				$body .= '--' . $boundary;
				$body .= "\r\n";
				$body .= 'Content-Disposition: form-data; name="source_file";' . "\r\n";
				$body .= "\r\n";
				$body .= $resx;
				$body .= "\r\n";
				$body .= '--' . $boundary . '--';
				$response = wp_remote_post(
					$url, array(
						'headers' => $headers,
						'body'    => $body,
					)
				);
				if( ! is_wp_error( $response ) ) {
					$result = json_decode($response['body']);

					if ($result->success === true) {
						/* add post meta information */
						$this->add_post_meta($post_ids, $target_langs);
						/* save job_keys in option table */
						$this->save_job_keys($result->job_key);
						/* save posts linked with job id */
						$this->save_job_links($result->job_key, $post_ids);
						if( $st_trans_cart ) {
							delete_option( Straker_Translations_Config::straker_option_translation_cart );
						}
						wp_redirect(admin_url('admin.php?page=st-jobs&msg=success&ac=job_new'));//&jobid=' . $result->tj_number));
					} elseif ( $result->success === 'false' && $result->code === '2100' ) {
						# code...
						$this->straker_bug_report($api_key = $this->straker_access_token, $msg = 'Failed exceeded the filze size', $eMessage = 'Failed exceeded the filze size', $eDetail = $response);
						wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed&ac=file_size_exceeded'));
					} else {
						$this->straker_bug_report($api_key = $this->straker_access_token, $msg = 'Failed json_decode body', $eMessage = 'Failed json_decode body', $eDetail = $response);
						wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed'));
					}
				} else {
					$this->straker_bug_report($api_key = $this->straker_access_token, $msg = 'Failed straker_request_quote', $eMessage = 'Failed json_decode body', $eDetail = $response);
					wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed'));
				}
			} else {
				$this->straker_bug_report($api_key = $this->straker_access_token, $msg = 'Failed straker_generate_resx', $eMessage = 'Failed straker_generate_resx', $eDetail = $resx);
				wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed'));
			}
		} else {
			wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed'));
		}
	}

	public function straker_extract_shortcodes( $post_ids )
	{
		$post_shortcode = array();
		global $shortcode_tags;
		$pattern = get_shortcode_regex();
		foreach( $post_ids as $post_id ) {
			$content_post = get_post( $post_id );
			$content = $content_post->post_content;
			foreach( $shortcode_tags as $tag => $function ) {
				if( has_shortcode( $content, $tag ) )
				{
					if (   preg_match_all( '/'. $pattern .'/s', $content, $matches ) && array_key_exists( 2, $matches ) && in_array( $tag, $matches[2] ) )
					{
						foreach ( $matches[0] as $key => $value )
						{
							array_push( $post_shortcode, $post_shortcode[$key] = $value );
						}
					}
				}
			}
		}

		return array_unique( $post_shortcode ) ;
	}

	public function straker_replace_post()
	{
		if (!check_admin_referer( 'st-import-resx-file', 'security' ) ) {
			return wp_send_json_error( "InValid Nonce" );
		}
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}
		$uploadedfile  = $_FILES["resxFile"]["tmp_name"];
		$post_replaced = sanitize_text_field( $_POST["post_id"] );
		$straker_xml   = new Straker_XML( '<?xml version="1.0" encoding="utf-8"?><root></root>' );
		$imported_post = $straker_xml->straker_re_import_resx(
			$body = $uploadedfile,
			$post_replaced = $post_replaced
		);
		if ( $imported_post ) {
			return wp_send_json_success( "Content Imported Successfully " );
		} else {
			//$this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_replace_post', $eMessage = 'Failed at replacing post content from Resx File.', $eDetail = $imported_post );
			return wp_send_json_error( "Content not available in file" );
		}
	}

	/**
	 *  straker view post button
	 *
	 * @since  1.0.0
	 */

	public function straker_translated_custom_post_link( $url, $post ) {

		$meta_val  = get_post_meta($post->ID, Straker_Translations_Config::straker_meta_locale, true);
		$post_lang = Straker_Language::straker_language_meta('code', $meta_val);
		$link_url  = get_option('permalink_structure');

		if ( !empty($meta_val) && is_admin() && $post_lang['code'] != $this->straker_default_language['code'])
		{
			if ( !empty($link_url) ){
				return str_replace(get_site_url(), untrailingslashit(get_site_url())."/".$post_lang["short_code"],$url);
			}else{
				return add_query_arg( 'lang', $post_lang["short_code"], $url );
			}
		}else
		{
			return $url;
		}
	}

	public function straker_translated_post_link( $url, $post, $leavename=false ) {

		$url_parse = parse_url($url);
		$meta_val  = get_post_meta($post->ID, Straker_Translations_Config::straker_meta_locale, true);
		$post_lang = Straker_Language::straker_language_meta('code', $meta_val);
		$link_url  = get_option('permalink_structure');
		if ( !empty($meta_val) && is_admin() && $post_lang['code'] != $this->straker_default_language['code'])
		{
			if ( !empty($link_url) ){
				return str_replace(get_site_url(), untrailingslashit(get_site_url())."/".$post_lang["short_code"],$url);
			}else{
				return add_query_arg( 'lang', $post_lang["short_code"], $url );
			}
		}else
		{
			return $url;
		}
	}

	public function straker_translated_page_link( $url, $id )
	{
		$meta_val  = get_post_meta($id, Straker_Translations_Config::straker_meta_locale, true);
		$post_lang = Straker_Language::straker_language_meta('code', $meta_val);
		$link_url  = get_option('permalink_structure');
		if ( !empty($meta_val) && is_admin() && $post_lang['code'] != $this->straker_default_language['code'])
		{
			if ( !empty($link_url) )
			{
				return str_replace(get_site_url(), untrailingslashit(get_site_url())."/".$post_lang["short_code"],$url);
			}else{
				return add_query_arg( 'lang', $post_lang["short_code"], $url );
			}
		}else
		{
			return $url;
		}
	}

	public function straker_translation_lang_meta()
	{
		global $post;
		$screen     = get_current_screen();
		$meta_value = get_post_meta($post->ID, Straker_Translations_Config::straker_meta_locale, true);
		if ($screen->action != 'add' && !empty($meta_value)) {
			add_meta_box(
				'straker-trans-detail-metabox', // Meta Box ID
				__('Straker Translations', $this->plugin_name),
				array($this, 'straker_translation_lang_meta_cb'),
				$this->straker_get_registered_posts_names(),
				'side',
				'high'
			);
		}elseif($screen->action == 'add'){
			add_meta_box(
				'straker-trans-detail-metabox', // Meta Box ID
				__('Straker Translations', $this->plugin_name),
				array($this, 'straker_translation_lang_meta_cb'),
				$this->straker_get_registered_posts_names(),
				'side',
				'high'
			);
		}
	}

	public function straker_translation_lang_meta_cb( $post )
	{
		include_once 'partials/metaboxes/language-metabox.php';
	}

	/**
	 *  straker assets meta box settings.
	 *
	 * @since  1.0.0
	 */
	public function straker_localized_assets_metabox()
	{
		global $post;
		$screen     = get_current_screen();
		$meta_value = get_post_meta($post->ID, Straker_Translations_Config::straker_meta_locale, true);
		if ($screen->action != 'add' && !empty($meta_value)) {
			add_meta_box(
				'straker-assets-metabox', // Meta Box ID
				__('Localized Images', $this->plugin_name),
				array($this, 'straker_localized_assets_metabox_callback'),
				$this->straker_registered_posts,
				'advanced',
				'high'
			);
		}
	}

	/**
	 * assets post meta box settings.
	 *
	 * @since  1.0.0
	 */
	public function straker_localized_assets_metabox_callback()
	{
		include plugin_dir_path(__FILE__) . 'partials/metaboxes/localized-assets-metabox.php';
	}

	public function update_edit_form()
	{
		echo ' enctype="multipart/form-data"';
	}

	public function straker_getLang_shorcode($lang)
	{
		foreach ($this->straker_languages as $key => $value) {
			if ($lang == $value['code']) {
				return $value['wp_locale'];
			}
		}
	}

	public function straker_getImgs_ids($pid)
	{
		$attachment_id       = array();
		$straker_locale      = get_post_meta($pid, Straker_Translations_Config::straker_meta_locale, true);
		$lang_short_code     = $this->straker_getLang_shorcode($straker_locale);
		$straker_default     = get_post_meta($pid, Straker_Translations_Config::straker_meta_default . $lang_short_code, true);
		$source_post_content = get_post($straker_default);
		$target_post_content = get_post($pid);
		preg_match_all("<img.+?src=[\"'](.+?)[\"'].*?>", $source_post_content->post_content, $source_imgs_urls);
		preg_match_all("<img.+?src=[\"'](.+?)[\"'].*?>", $target_post_content->post_content, $target_imgs_urls);
		$common_images_urls = array_intersect($source_imgs_urls[1], $target_imgs_urls[1]);

		foreach ($common_images_urls as $ciKey => $ciVal) {
			$file_name  = basename($ciVal);
			$query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'fields'      => 'ids',
				'meta_query'  => array(
					array(
						'value'   => $file_name,
						'compare' => 'LIKE',
						'key'     => '_wp_attachment_metadata',
					),
				),
			);
			$query = new WP_Query($query_args);
			if ($query->have_posts()) {
				foreach ($query->posts as $post_id) {
					$meta                = wp_get_attachment_metadata($post_id);
					$original_file       = basename($meta['file']);
					$cropped_image_files = isset($meta['sizes']) ? wp_list_pluck($meta['sizes'], 'file') : "";
					if ($original_file === $file_name || in_array($file_name, $cropped_image_files)) {
						array_push($attachment_id, $attachment_id[$post_id] = $ciVal);
						break;
					}
				}
			}
		}
		return array_unique($attachment_id);
	}

	/**
	 * save assets post meta box settings.
	 *
	 * @since  1.0.0
	 */
	public function save_post_locale_language_mb( $post_id )
	{
		// If this is a revision, get real post ID
		if ( $parent_id = wp_is_post_revision( $post_id ) )
		{
			$post_id = $parent_id;
		}

		// Bail if we're doing an auto save
	 if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	 // if our nonce isn't there, or we can't verify it, bail
	 if( ! isset( $_POST['straker_save_lang_meta_nonce'] ) || ! wp_verify_nonce( $_POST['straker_save_lang_meta_nonce'], 'straker_save_lang_meta' ) ) return;

	 // if our current user can't edit this post, bail
	 if( ! current_user_can( 'edit_posts' ) ) return;

	 // Add post locale langugae
	 if ( isset( $_POST['st_lang_select'] ) && ! $_POST['st_lang_select']  == '' )
	 {
		 update_post_meta( $post_id, Straker_Translations_Config::straker_meta_locale, sanitize_text_field( $_POST['st_lang_select'] ) );
	 }
	}

	/**
	 * save assets post meta box settings.
	 *
	 * @since  1.0.0
	 */
	public function save_assets_metabox($post_id)
	{
		// verify nonce
		if (isset($_POST['straker_assets_meta_box_nonce']) && isset($_POST['post_type'])) {
			// Don't save if the user hasn't submitted the changes
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}
			// Verify that the input is coming from the proper form
			if (!wp_verify_nonce($_POST['straker_assets_meta_box_nonce'], $this->plugin_name)) {
				return;
			}
			// Make sure the user has permissions to post
			if ('post' == $_POST['post_type']) {
				if (!current_user_can('edit_post', $post_id)) {
					return;
				}
			}
			if (!isset($_FILES['localized_files'])) {
				return;
			}
			if (!function_exists('wp_handle_upload')) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once ABSPATH . 'wp-admin/includes/image.php';
			global $wpdb;
			$table_post       = $wpdb->prefix . 'posts';
			$upload_overrides = array('test_form' => false);
			$files            = $_FILES['localized_files'];
			$src_img_num      = 0;

			foreach ($files['name'] as $key => $value) {
				$uploadedfile = array(
					'name'     => sanitize_file_name($files['name'][$key]),
					'type'     => $files['type'][$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error'    => $files['error'][$key],
					'size'     => $files['size'][$key],
				);
				$movefile        = wp_handle_upload($uploadedfile, $upload_overrides);
				$source_img_url = isset( $_POST['s_img'] ) ? (array) $_POST['s_img'] : array();
				$source_img_url = array_map( 'esc_attr', $source_img_url );
				$source_imgs_ids = isset( $_POST['imgs_ids'] ) ? (array) $_POST['imgs_ids'] : array();
				$source_imgs_ids = array_map( 'esc_attr', $source_imgs_ids );

				if ($movefile && !isset($movefile['error']) && isset($source_img_url)) {
					// Check the type of file. We'll use this as the 'post_mime_type'.
					$filetype = wp_check_filetype(basename($movefile['file']), null);
					$ufiles   = get_post_meta($post_id, 'straker_localized_files', true);
					// Get the path to the upload directory.
					$wp_upload_dir = wp_upload_dir();
					// Prepare an array of post data for the attachment.
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename($movefile['file']),
						'post_mime_type' => $movefile['type'],
						'post_title'     => preg_replace('/\.[^.]+$/', '', basename($movefile['file'])),
						'post_content'   => '',
						'post_status'    => 'inherit',
					);
					// Insert the attachment.
					$attach_id = wp_insert_attachment($attachment, $movefile['file'], $post_id);
					// Generate the metadata for the attachment, and update the database record.
					$attach_data = wp_generate_attachment_metadata($attach_id, $movefile['file']);
					wp_update_attachment_metadata($attach_id, $attach_data);
					if (empty($ufiles)) {
						$ufiles = array();
					}

					$ufiles[] = $movefile;
					update_post_meta($post_id, 'straker_localized_files', $ufiles);
					$query_results = $wpdb->query($wpdb->prepare("UPDATE $table_post SET post_content = replace(post_content, 'wp-image-%d', 'wp-image-%d') WHERE ID = %d ", $source_imgs_ids[$key], $attach_id, $post_id));
					$query_results = $wpdb->query($wpdb->prepare("UPDATE $table_post SET post_content = replace(post_content, '%s', '%s') WHERE ID = %d ", $source_img_url[$key], $movefile['url'], $post_id));
				}
				$src_img_num++;
			}
		}
	}

	/**
	 * add custom column on manage posts and pages.
	 *
	 * @since  1.0.0
	 */
	public function straker_add_custom_cloumn_manage_posts_pages( $columns )
	{
		$columns['straker_locale_column']        = __( 'Locale', $this->plugin_name );
		$columns['straker_original_post_column'] = __( 'Source', $this->plugin_name );
		return $columns;
	}

	/**
	 * custom column conent on manage posts and pages.
	 *
	 * @since  1.0.0
	 */
	public function straker_custom_cloumn_content_posts_pages($column_name, $post_id)
	{
		$straker_locale = get_post_meta($post_id, Straker_Translations_Config::straker_meta_locale, true);
		switch ($column_name) {
			case 'straker_locale_column':
				if (!empty($straker_locale)) {
					foreach ($this->straker_languages as $key => $value) {
						if ($straker_locale == $value['code']) {
						
							printf( 
									'<a href="#" st-data-tooltip title="%s"><img src="%s.png" /></a>',
									$value['name'],
									$this->flags_path . $value['code']
								); 
						}
					}
				} else {
					_e('', $this->plugin_name);
				}
				break;
			case 'straker_original_post_column':
				if (!empty($straker_locale)) {
					foreach ($this->straker_languages as $key => $value) {
						if ($straker_locale == $value['code']) {
							$straker_original_post = get_post_meta($post_id, Straker_Translations_Config::straker_meta_default . $value['wp_locale'], true);
							if($straker_original_post){
							?>
								<a href="<?php echo get_edit_post_link($straker_original_post); ?>"><?php echo get_the_title($straker_original_post); ?></a>
							<?php
							}
						}
					}
				}
				break;
		}
	}

	/**
	 * add different file mime types in wordpress.
	 *
	 * @since  1.0.0
	 */

	public function straker_enable_mime_types($mime_types = array())
	{
		$mime_types['xml'] = 'text/xml';
		return $mime_types;
	}

	/**
	 * add post meta data for posts and pages.
	 *
	 * @since  1.0.0
	 */
	public function add_post_meta(array $post_ids, array $langs)
	{
		foreach ($post_ids as $post_id) {
			/* check if the target language is already added */
			$lang_meta = get_post_meta($post_id, Straker_Translations_Config::straker_meta_target, true);
			if (strlen($lang_meta) > 0) {
				foreach ($langs as $lang) {
					if (stripos($lang_meta, $lang) === false) {
						$lang_meta .= ',' . $lang;
					}
				}
			} else {
				$lang_meta = implode(',', $langs);
			}
			/* check if the language exists on the current list */
			if (!add_post_meta($post_id, Straker_Translations_Config::straker_meta_target, $lang_meta, true)) {
				update_post_meta($post_id, Straker_Translations_Config::straker_meta_target, $lang_meta);
			}
		}
	}

	/**
	 * Store job keys as options
	 *
	 * @since  1.0.0
	 */
	public function save_job_keys($job_key)
	{
		$aJob = array($job_key);
		if ($this->straker_job_keys === false) {
			$result = add_option(Straker_Translations_Config::straker_option_jobs, $aJob);
		} else {
			$aNew = $this->straker_job_keys;
			array_push($aNew, $job_key);
			$result = update_option(Straker_Translations_Config::straker_option_jobs, $aNew);
		}
		return $result;
	}

	/**
	 * Store TJs post links
	 *
	 * @since  1.0.0
	 */
	public function save_job_links($job_key, $post_ids)
	{
		if (get_option(Straker_Translations_Config::straker_option_job . $job_key) === false) {
			$result = add_option(Straker_Translations_Config::straker_option_job . $job_key, $post_ids);
		} else {
			$result = update_option(Straker_Translations_Config::straker_option_job . $job_key, $post_ids);
		}
		return $result;
	}

	/**
	 * get job details of all the jobs of the client
	 *
	 * @since  1.0.0
	 */
	public function straker_get_jobs($job_keys)
	{
		$url      = $this->straker_api('translate');
		$response = array();
		$jobs 	  = array();
		if ($job_keys) {
			$args = array(
				'timeout' => 10,
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->straker_access_token,
					'X-Auth-App'    => $this->straker_application_key,
					'Content-type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'job_key' => $job_keys,
				),
			);
			$response = wp_remote_get( $url, $args );
		}
		if( ! is_wp_error( $response ) ) {

			$body = $response['body'];
			$jobs = json_decode( $response['body'], true );

			if( isset( $jobs['job'] ) && is_array( $jobs['job'] ) && ! isset( $jobs['message'] ) ) {
				return $jobs;
			} else{
				return false;
			}

		} else {
			return false;

		}
	}

	public function straker_return_api_jobs() {
		
		if ( $this->straker_job_keys ) {
			return $this->straker_get_jobs( implode( ',' , $this->straker_job_keys ) );
		} else {
			return false;
		}
	}
	/**
	 * get number of jobs based on status
	 *
	 * @since  1.0.0
	 */
	public function straker_get_total_jobs()
	{
		$url      = $this->straker_api('translate');
		$response = array();
		if ($this->straker_job_keys) {
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->straker_access_token,
					'X-Auth-App'    => $this->straker_application_key,
					'Content-type'  => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'job_key' => implode(',', $this->straker_job_keys),
				),
			);
			$response = wp_remote_get($url, $args);
		}
		$job_array = array();
		if( ! is_wp_error( $response ) ) {
			if (array_key_exists('body', $response)) {
				$body = $response['body'];
				$jobs = json_decode($body, true);
				foreach ($jobs as $key => $value) {
					if (is_array($value)) {
						foreach ($value as $data ) {
							switch ($data["status"]) {
								case 'QUEUED':
									if ($data['quotation'] === 'READY') {
											$job_array["ready"][] = $data["job_key"];
									} else {
										$job_array["awaiting_quote"][] = $data["job_key"];
									}
									break;
								case 'IN_PROGRESS':
									$job_array["in_progress"][] = $data["job_key"];
									break;
								case 'COMPLETED':
									$job_array["completed"][] = $data["job_key"];
									break;
							}
						}
					}
				}
			}
		} else {
			// $this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_get_total_jobs', $eMessage = 'Failed at getting total jobs from API.', $eDetail = $response );
		}
		return $job_array;
	}

	public function straker_download_resx($download_urls, $tj, $job_keys, $tlang)
	{
		$filetype               = array("xml");
		$attachment_meta_data   = array();
		$wp_upload_dir          = wp_upload_dir();
		$translated_content_api = array();
		$csv_content            = array();
		$attachment_meta_xml		= array();
		$attachment_meta_csv		= array();
		$translation_resx       = get_option(Straker_Translations_Config::straker_option_translation_resx . $tj);

		if ($job_keys && !$translation_resx) {
			add_option(Straker_Translations_Config::straker_option_translation_resx . $tj);
			foreach ($download_urls as $tKey => $tVal) {
				$url                         = $tVal["download_url"];
				$tl                          = $tVal["tl"];
				$body                        = $this->straker_get_translation($url);
				$translated_content_api[$tl] = $body;
			}
			foreach ($filetype as $ftKey => $ftVal) {
				$custom_folders = 'straker_translations' . '/' . $tj . '/' . $ftVal . '/';
				$user_dirname   = $wp_upload_dir['basedir'] . '/' . $custom_folders;
				if (!file_exists($user_dirname)) {
					wp_mkdir_p($user_dirname);
				}
				switch ($ftVal) {
					case 'xml':
						foreach ($translated_content_api as $key => $body) {
							$filename   = $wp_upload_dir["basedir"] . "/" . $custom_folders . $tj . "_" . $key . "." . $ftVal;
							$fp         = fopen($filename, "w");
							$file_write = fwrite($fp, $body);
							fclose($fp);
							$filetype   = wp_check_filetype(basename($filename), null);
							$attachment = array(
								'guid'           => $wp_upload_dir['baseurl'] . '/' . $custom_folders . basename($filename),
								'post_mime_type' => $filetype['type'],
								'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
								'post_content'   => '',
								'post_status'    => 'inherit',
							);
							// Insert the attachment.
							$attach_id   = wp_insert_attachment($attachment, $filename);
							$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
							wp_update_attachment_metadata($attach_id, $attach_data);
							$attachment_meta_xml[$key] = $attach_id;
						}
						$attachment_meta_data['xml_files'] = $attachment_meta_xml;
						update_option(Straker_Translations_Config::straker_option_translation_resx . $tj, $attachment_meta_data);
						break;
					/*case 'csv':
						foreach ($translated_content_api as $key => $body) {
							$resx = simplexml_load_string($body) or die('Error: Cannot get resx file');
							// output the column headings
							foreach ($resx->children() as $data) {
								$post_id      = 0;
								$post_title   = '';
								$post_content = '';
								if ($data['content_context'] == 'Title') {
									$post_title      = $data->value;
									$post_id         = $data['post_id'];
									$post_type       = $data['post_type'];
									$post_name       = $data['post_name'];
									$post_categories = explode(',', $data['post_categories']);
									$content_id      = 'content_' . $post_id;
									$content_node    = $resx->xpath('/root/data[@name="' . $content_id . '"]');
									foreach ($content_node as $content) {
										array_push($csv_content, $tlang,$post_name, get_post_field('post_content', $post_id), $key,$post_title,$content->value);
									}
								}
							}
						}
						$filename = $wp_upload_dir["basedir"] . "/" . $custom_folders . $tj . "." . $ftVal;
						$file     = fopen($filename, "w");
						fputcsv($file, array('source_language', 'source_title', 'source_content', 'target_language', 'translated_title', 'translated_content'));
						fputcsv($file, $csv_content);
						fclose($file);
						$filetype   = wp_check_filetype(basename($filename), null);
						$attachment = array(
							'guid'           => $wp_upload_dir['baseurl'] . '/' . $custom_folders . basename($filename),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace('/\.[^.]+$/', '', basename($filename)),
							'post_content'   => '',
							'post_status'    => 'inherit',
						);
						// Insert the attachment.
						$attach_id   = wp_insert_attachment($attachment, $filename);
						$attach_data = wp_generate_attachment_metadata($attach_id, $filename);
						wp_update_attachment_metadata($attach_id, $attach_data);
						$attachment_meta_data['csv_file'] = $attach_id;
						update_option(Straker_Translations_Config::straker_option_translation_resx . $tj, $attachment_meta_data);
						break;*/
				}
			}
			return $attachment_meta_data;
		}
	}

	public function straker_job_links($job_key)
	{
		return get_option(Straker_Translations_Config::straker_option_job . $job_key);
	}

	public function straker_imported_links($job_key)
	{
		return get_option(Straker_Translations_Config::straker_option_imported . $job_key);
	}

	public function straker_get_translation($url)
	{
		$response = wp_remote_get($url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->straker_access_token,
				'X-Auth-App'    => $this->straker_application_key,
				'Content-Type'  => 'application/x-www-form-urlencoded',
			),
			'body'    => array(
			),
		));
		if ( ! is_wp_error( $response ) ) {
			// The request went through successfully, check the response code against
			if (200 == wp_remote_retrieve_response_code($response)) {
				$body = wp_remote_retrieve_body($response);
				return $body;
			} else {
				$error_message = wp_remote_retrieve_response_message($response);
				echo $error_message;
				exit;
			}
		} else {
			// $this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_get_translation', $eMessage = 'Failed at getting Job Translation.', $eDetail = $response );
			$error_message = $response->get_error_message();
			echo $error_message;
			exit;
		}
	}

	public function straker_import_translation()
	{
		if (
			!empty($_POST) &&
			isset($_POST['jk'])
		) {
			$job_key      = sanitize_text_field( $_POST['jk'] );
			$do_re_import = false;

			if ( isset( $_POST['re_import'] ) && ! empty( $_POST['re_import'] ) ) {
				$re_import    = sanitize_text_field( $_POST['re_import'] );
				if ( $re_import == 'true' ) {
					$do_re_import = true;
				}
			}
			$straker_xml = new Straker_XML('<?xml version="1.0" encoding="utf-8"?><root></root>');
			$aFile       = array();
			$straker_api_jobs = $this->straker_return_api_jobs();
			foreach ( $straker_api_jobs as $jobs ) {
				foreach ( $jobs as $key => $value ) {
					if ( $value['job_key'] == $job_key ) {
						array_push( $aFile, $value );
					}
				}
			}
			$job = array();
			foreach ($aFile as $key => $value) {
				$job	= $value['translated_file'];
				$imported_all_post = array();
				foreach ($job as $download) {
					$url         = $download['download_url'];
					$lang        = $download['tl'];
					$lang_meta   = Straker_Language::straker_language_meta('code', $lang);
					$body        = $this->straker_get_translation($url);
					$default_key = Straker_Translations_Config::straker_meta_default . $lang_meta['wp_locale'];

					$imported_post = $straker_xml->straker_import_resx(
						$body = $body,
						$lang_code = $lang_meta['code'],
						$meta_locale = Straker_Translations_Config::straker_meta_locale,
						$meta_default = $default_key,
						$short_code = $lang_meta['short_code'],
						$re_import = $do_re_import
					);
					foreach ($imported_post as $key => $value) {
						array_push($imported_all_post, $value);
					}
				}
				// Insert the options
				if ($this->straker_imported_links($job_key) === false) {
					$result = add_option(Straker_Translations_Config::straker_option_imported . $job_key, $imported_all_post);
				} else {
					update_option(Straker_Translations_Config::straker_option_imported . $job_key, $imported_all_post);
					$result = true;
				}
				// Sandbox job information
				if (Straker_Translations_Config::straker_sandbox_mode() === 'true') {
					$this->save_sandbox_jobs($imported_all_post);
				}
			}

			if ($result) {
				wp_redirect(admin_url('admin.php?page=st-jobs&jk=' . $job_key . '&ac=job_import&msg=success&pr=ready'));
				exit();

			} else {
				// $this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_import_translation', $eMessage = 'Failed at importing jobs.', $eDetail = 'Failed at importing jobs.' );
				wp_redirect(admin_url('admin.php?page=st-jobs&ac=job_import&msg=failed'));
				exit();
			}
		} else {
			wp_redirect(admin_url('admin.php?page=st-jobs&ac=job_import&msg=failed'));
			exit();
		}
	}

	public function save_sandbox_jobs($imported_post)
	{
		if ($this->straker_sandbox_jobs === false) {
			$result = add_option(Straker_Translations_Config::straker_option_sandbox_jobs, $imported_post);
		} else {
			$aSand = $this->straker_sandbox_jobs;
			$aNew =array_merge($aSand, $imported_post);
			$result = update_option(Straker_Translations_Config::straker_option_sandbox_jobs, $aNew);
		}
		return $result;
	}

	public function straker_cancel_job()
	{
		if (
			!empty($_POST) &&
			check_admin_referer('straker-translations-cancel-job', 'straker-translations-cancel-job-nonce') &&
			isset($_POST['jk'])

		) {
			$job_key = sanitize_text_field($_POST['jk']);
			$url     = $this->straker_api('translate/cancel');

			$response = wp_remote_post($url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->straker_access_token,
					'X-Auth-App'    => $this->straker_application_key,
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Accept'        => 'text/xml',
				),
				'body'    => array('job_key' => $job_key,
				),
			));
			$body   = json_decode($response['body'], true);
			$result = $body['job'];
			if ($result[0]['success']) {
				$option_jobs	=	get_option(Straker_Translations_Config::straker_option_jobs);
				$delete_job_key = array_search ($job_key, $option_jobs);
				unset($option_jobs[$delete_job_key]);
				update_option(Straker_Translations_Config::straker_option_jobs, $option_jobs);
				wp_redirect(admin_url('admin.php?page=st-jobs&ac=job_cancel&msg=success'));
				exit();
			} else {
				// $this->straker_bug_report( $api_key = $this->straker_access_token, $msg = 'Failed straker_cancel_job', $eMessage = 'Failed at canceling job.', $eDetail = $response );
				wp_redirect(admin_url('admin.php?page=st-jobs&ac=job_cancel&msg=failed'));
				exit();
			}
		}

	}

	public function straker_api_signature()
	{
		$str     = 'access_token' . $this->straker_access_token . 'app_key' . $this->straker_application_key;
		$api_sig = md5($str);
		return $api_sig;
	}

	/**
	 * Status, Message
	 */
	public function straker_api_status($api_status, $quotation)
	{
		switch ($api_status) {
			case 'QUEUED':
				if ($quotation === 'READY') {
					$view_status = '<span class=""></span> Order Now';
				} else {
					$view_status = '<span class=""></span> Awaiting Quote';
				}
				break;
			case 'IN_PROGRESS':
				$view_status = '<span class=""></span> In Progress';
				break;
			case 'COMPLETED':
				$view_status = '<span class=""></span> Ready';
				break;
			default:
				$view_status = '';
				break;
		}
		return $view_status;
	}

	public function straker_api_action($api_status, $quotation, $job_key)
	{
		switch ($api_status) {
			case 'QUEUED':
				if ($quotation === 'READY') {
					$action = 'View Quote';
					$link   = admin_url('admin.php?page=st-jobs&jk=' . $job_key . '&v=quote');
					$class  = 'button button-primary';
					$title  = '';
					$target = '_blank';
				} else {
					$action = 'View Job';
					$link   = admin_url('admin.php?page=st-jobs&jk=' . $job_key);
					$class  = '';
					$title  = '';
					$target = '_self';
				}
				break;
			case 'IN_PROGRESS':
				$action = 'In Progress';
				$link   = admin_url('admin.php?page=st-jobs&jk=' . $job_key);
				$target = '_self';
				$class  = '';
				$title  = '';
				break;
			case 'COMPLETED':
				if ($this->straker_imported_links($job_key)) {
					$action = 'View Job';
					$link   = admin_url('admin.php?page=st-jobs&jk=' . $job_key . '&pr=ready');
					$target = '_self';
					$class  = '';
					$title  = '';
				} else {
					$action = 'Import';
					$link   = admin_url('admin-post.php');
					$target = '_self';
					$class  = 'button button-primary';
					$title  = __("This will import your translation to 'Pending review' status. You will need to go to the Pages section of WordPress to set it live.", $this->plugin_name);
				}
				break;
			default:
				$action = '';
				$link   = '#';
				$target = '_self';
				$class  = '';
				$title  = '';
				break;
		}

		if ($action == 'Import') {
			$button = '<form action="' . $link . '" name="straker_import_translation_form" id="straker_import_translation_form" method="post">';
			$button .= '<input type="hidden" name="action" value="straker_import_translation">';
			$button .= '<input type="hidden" name="jk" value="' . $job_key . '">';
			$button .= '<button type="submit" name="submit" id="submit" class="' . $class . '" st-data-tooltip title="' . $title . '">' . $action . '</button>';
			$button .= '</form>';
		} else {
			$button = '<a href="' . $link . '" class="' . $class . '" target="' . $target . '" title="' . $title . '">' . $action . '</a>';
		}
		return $button;
	}

	public function straker_message($action, $jobid = '')
	{
		switch ($action) {
			case 'register':
				$message = __("Thank you for registering with us. You will receive an email from us shortly.", $this->plugin_name);
				break;
			case 'language':
				$message = __("Your settings have been updated.", $this->plugin_name);
				break;
			case 'shortcode':
				$message = __("Shortcode settings have been updated.", $this->plugin_name);
				break;
			case 'job_new':
				$message = __("Thank you, your job has been submitted successfully.", $this->plugin_name);
				break;
			case 'file_size_exceeded':
				$message = __("Maximum file size exceeded. However, we can still translate your site, please reselect your content or <a href='". esc_url( admin_url( 'admin.php?page=st-support' ) ) ."'>contact</a> us at extensions@strakertranslations.com.", $this->plugin_name);
				break;
			case 'job_cancel':
				$message = __("Your job has been cancelled successfully.", $this->plugin_name);
				break;
			case 'lang_setting':
				$message = __("Setup has not been completed. Please select your languages preferences below.", $this->plugin_name);
				break;
			case 'url_setting':
				$message = __("Setup has not been completed. Please select your url preferences below.", $this->plugin_name);
				break;
			case 'lang_url_setting':
				$message = __("Language settings have been updated. Please select your url preferences below.", $this->plugin_name);
				break;
			case 'url':
				$message = __("Your settings have been updated.", $this->plugin_name);
				break;
			case 'lang_management':
			$message = __("Your settings have been updated.", $this->plugin_name);
			break;
			case 'empty_translation':
				$message = __("You can't send an empty job. ", $this->plugin_name);
				break;
			case 'job_import':
				$message = __("Job imported successfully.", $this->plugin_name);
				break;
			case 'test_mode':
				$message = __("Sandbox Mode Enabled", $this->plugin_name);
				break;
			case 'plugin_mode':
				$message = __("Sandbox settings have been updated.", $this->plugin_name);
				break;
			case 'test_text':
				$message = __("Jobs created in the Sandbox testing mode will not be received by Straker Translations. Translated content will be machine created sample text with a restriction of 10 pages, this may take longer than live. To switch to live, go to", $this->plugin_name);
				break;
			case 'trans_cart_empty':
				$message = __( "Translation cart is empty. Please add items into cart.", $this->plugin_name );
				break;
			case 'trans_cart_empty_error':
				$message = __( "Failed to makes translation cart empty.", $this->plugin_name );
				break;
			default:
				$message = '';
				break;
		}
		return $message;
	}

	public function straker_url_structure($short_code)
	{
		$url_structure = home_url();
		if (get_option('permalink_structure')) {
				$url_structure = $url_structure.'/'.$short_code;
		} else {
			$url_structure = $url_structure.'/?lang='.$short_code;
		}
		return $url_structure;
	}

	/**
	 * Posts, Pages
	 */
	public function straker_posts_columns($posts_columns, $post_type)
	{
		$aTypes = array('post', 'page');
		if (!in_array($post_type, $aTypes)) {
			return $posts_columns;
		}
		if ( ! isset( $posts_columns['locale'] ) ) {
			$posts_columns = array_merge(
				array_slice($posts_columns, 0, 3),
				array('locale' => __('Locale', $this->plugin_name)),
				array_slice($posts_columns, 3));
		}
		return $posts_columns;
	}

	public function straker_manage_posts_custom_column($column_name, $post_id)
	{
		$post_type = get_post_type($post_id);
		$aTypes    = array('post', 'page');
		if (!in_array($post_type, $aTypes)) {
			return;
		}
		if ('locale' != $column_name) {
			return;
		}
		$locale = get_post_meta($post_id, Straker_Translations_Config::straker_meta_locale, true);

		if (empty($locale)) {
			return;
		}
		echo esc_html($locale);
	}

	/**
	 * Nav menu
	 */
	public function straker_edit_nav_menu_walker( $class, $menu_id )
	{
		return 'Straker_Walker_Nav_Menu_Edit';
	}

	public function straker_add_nav_items($menu_id, $menu_item_db_id, $args)
	{
		$term_meta_locale	=	get_term_meta( $args["menu-item-object-id"],Straker_Translations_Config::straker_cat_lang_meta );

		if( $args["menu-item-object"] !== "category" ){
			$post_meta_locale = get_post_meta( $args["menu-item-object-id"], Straker_Translations_Config::straker_meta_locale, true );
			$default_lang     = $this->straker_default_language['code'];
			$locale 		  = ! empty( $post_meta_locale ) ? $post_meta_locale : $default_lang;
			// For pages, posts have straker_locale value
			if ($locale) {
				if (!add_post_meta($menu_item_db_id, Straker_Translations_Config::straker_meta_locale, $locale, true)) {
						update_post_meta($menu_item_db_id, Straker_Translations_Config::straker_meta_locale, $locale);
				}
			}
		}

		$straker_default_lang = !empty($this->straker_default_language)?$this->straker_default_language:"";
		if($args["menu-item-object"] == "category" && !empty($term_meta_locale)){
			$get_menu_cat_lang 		= get_term_meta($args["menu-item-object-id"],Straker_Translations_Config::straker_cat_lang_meta,true);
			add_post_meta($menu_item_db_id,Straker_Translations_Config::straker_meta_locale,$get_menu_cat_lang );
		}
	}

	public function straker_update_nav_fields( $menu_id, $menu_item_db_id, $args ){

		if( $args["menu-item-object"] == "custom" ){
			if ( isset( $_REQUEST['menu-item-meta'] ) && is_array( $_REQUEST['menu-item-meta'] ) && $args["menu-item-object"] == "custom" ) {
				$custom_value = $_REQUEST['menu-item-meta'][$menu_item_db_id];
				update_post_meta( $menu_item_db_id, Straker_Translations_Config::straker_meta_locale, $custom_value );
			}
		}
		
	}


	public function straker_add_category_custom_fields_form($cat_id)
	{
		if ( isset( $_POST['straker-cat-lang'] ) ) {
			$term_id 	= $cat_id;
			$cat_lang = sanitize_text_field($_POST['straker-cat-lang']);
			$cat_meta = get_term_meta ($term_id);
			if(get_term_meta ($term_id) && !empty($cat_meta)){
				update_term_meta ($term_id, Straker_Translations_Config::straker_cat_lang_meta, $cat_lang);
			}else{
				add_term_meta ($term_id, Straker_Translations_Config::straker_cat_lang_meta, $cat_lang);
			}
		}
	}

	public function straker_add_tag_custom_fields_form($tag_id){
		if ( isset( $_POST['straker-tag-lang'] ) ) {
			$term_id 	= $tag_id;
			$cat_lang = sanitize_text_field($_POST['straker-tag-lang']);
			$tag_meta = get_term_meta ($term_id);
			if(get_term_meta ($term_id) && !empty($tag_meta)){
				update_term_meta ($term_id, Straker_Translations_Config::straker_tag_lang_meta, $cat_lang);
			}else{
				add_term_meta ($term_id, Straker_Translations_Config::straker_tag_lang_meta, $cat_lang);
			}
		}
	}

	public function straker_category_custom_column($columns){
		$columns['st_cat_lang_column'] = 'Language';
		return $columns;
	}

	public function straker_category_custom_field_column($deprecated,$column_name,$term_id){
		$term_meta_locale = get_term_meta($term_id,Straker_Translations_Config::straker_cat_lang_meta);
		$cat_meta 				= !empty($term_meta_locale)? get_term_meta($term_id,Straker_Translations_Config::straker_cat_lang_meta,true): "";
		if ($column_name == 'st_cat_lang_column' && !empty($cat_meta)) {
			$cat_lang_meta = Straker_Language::straker_language_meta('code', $cat_meta);
			?>
			<img style="vertical-align:middle" st-data-tooltip title="<?php echo str_replace('_', ' ', $cat_lang_meta['name']) ?>" src='<?php echo $this->flags_path . $cat_lang_meta["code"]; ?>.png'>
		<?php
		}
	}

	public function straker_tag_custom_field_column($deprecated,$column_name,$term_id){
		$tag_meta_locale =	get_term_meta($term_id,Straker_Translations_Config::straker_tag_lang_meta);
		$cat_meta 			 = !empty($tag_meta_locale)? get_term_meta($term_id,Straker_Translations_Config::straker_tag_lang_meta,true): "";
		if ($column_name == 'st_tag_lang_column' && !empty($cat_meta)) {
			$cat_lang_meta = Straker_Language::straker_language_meta('code', $cat_meta);
			?>
				<img style="vertical-align:middle" st-data-tooltip title="<?php echo str_replace('_', ' ', $cat_lang_meta['name']) ?>" src='<?php echo $this->flags_path . $cat_lang_meta["code"]; ?>.png'>
			<?php
		}
	}

	public function straker_tag_custom_column($columns){
		$columns['st_tag_lang_column'] = 'Language';
		return $columns;
	}

	public function straker_get_registered_posts_names()
	{

		$post_types 	= get_post_types( array( 'public'   => true, '_builtin' => false ), 'names' );
		$merge_array	= array_merge( $post_types, array( 'post' => 'post', 'page' =>'page' ) );
		update_option( Straker_Translations_Config::straker_registered_posts, array_values( $merge_array ) );
		$this->straker_plugin_update();
		return $merge_array;

	}

	public function straker_language_dropdown_filter()
	{
		$language = isset($_REQUEST['straker_lang_filter']) ?sanitize_text_field($_REQUEST['straker_lang_filter']) : '';
		$added_langs = Straker_Language::get_default_and_target_languages();
		echo '<select name="straker_lang_filter">';
		echo '<option value="" selected="selected">'.__("Select Language", $this->plugin_name).'</option>';
		foreach($added_langs as $val){
			echo '<option value="'.$val['code'].'" '.selected($language, $val['code']).'>'.$val['name'].'</option>';
		}
		echo '</select>';

	}
	public function straker_language_filtering( $query )
	{
		global $pagenow;
		if( isset( $_GET['post_type'] ) && isset( $_GET['straker_lang_filter'] ) && $pagenow == 'edit.php' &&  $_GET['straker_lang_filter'] !='' ){
			$query->query_vars['meta_key'] = Straker_Translations_Config::straker_meta_locale;
       	 	$query->query_vars['meta_value'] = sanitize_text_field( $_REQUEST['straker_lang_filter'] );
		}
	}

	public function straker_plugin_update()
	{
		$all_post_types = get_option( Straker_Translations_Config::straker_registered_posts );
		if( $all_post_types )
		{
			$post_types = $all_post_types;
		} else
		{
			$post_types = array_values( $this->straker_get_registered_posts_names() );
		}
		$args = array(
			'post_status' => $this->straker_posts_status,
			'post_type' => $post_types,
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => Straker_Translations_Config::straker_meta_locale,
					'compare' => 'NOT EXISTS' // this should work...
				),
			),
		);
		$query = new WP_Query( $args );
		if ( $query->have_posts() )
		{
		while ( $query->have_posts() )
			{
				$query->the_post();
				update_post_meta( get_the_ID(), Straker_Translations_Config::straker_meta_locale, $this->straker_default_language['code'] );
			}
			wp_reset_postdata();
		}
		
	}

	public function straker_remove_plugin_custom_cookies()
	{
		setcookie( 'st_lang_manag', ' ', time() - YEAR_IN_SECONDS, SITECOOKIEPATH );
	}

	public function check_posts_have_locale()
	{
		if ( $this->straker_default_language ) {
			$args  = array(
				'post_type'      => Straker_Util::get_all_post_types_names(),
			);

			$extra_args = array(
				'post_status'    => $this->straker_posts_status,
				'meta_key'			 => Straker_Translations_Config::straker_meta_locale,
				'meta_value'		 => $this->straker_default_language['code']
			);
			$results = new WP_Query( array_merge( $args, $extra_args ) );
			if ( $results->have_posts() )
			{
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function straker_revision_title_edit( $revision_date_author, $revision, $link ) {
		$revision_id = get_post_meta( $revision->post_parent, Straker_Translations_Config::straker_translated_revision_id, true );
		if( $revision->ID == $revision_id ) {
			return $revision_date_author. ' [Updated Translation]';
		}
		return $revision_date_author;
	}

	public function straker_translation_order_ajax_custom_list_callback() {

		$wp_posts_ids = isset( $_REQUEST['st_wp_posts_ids'] ) ? sanitize_text_field( $_REQUEST['st_wp_posts_ids'] ) : '';
		$wp_posts_types = isset( $_REQUEST['st_wp_posts_types'] ) ? sanitize_text_field( $_REQUEST['st_wp_posts_types'] ) : '';
		$wp_list_table = new Straker_Translation_Order_Page_List_Table_Ajax();
		$wp_list_table->set_ids( explode( ',', $wp_posts_ids ) );
		$wp_list_table->set_types( explode( ',', $wp_posts_types ) );
		$wp_list_table->ajax_response();

	}

	public function straker_translation_cart_order_ajax_custom_list_callback() {

		$wp_list_table = new Straker_Translation_Cart_Order_Page_List_Table_Ajax( $this->plugin_name );
		$wp_list_table->ajax_response();

	}

	public function straker_translation_add_single_item_into_cart() {

		check_ajax_referer( 'st-cart-nonce', 'nonce_security' );
		$wp_posts_ids = isset( $_REQUEST['postID'] ) ? sanitize_text_field( $_REQUEST['postID'] ) : '';
		if ( Straker_Translations_Cart_Handling::add_item_into_cart( $wp_posts_ids ) ) {
			wp_send_json_success(array('isResponse'=>true));
			wp_die();
		} else {
			wp_send_json_error(array('isResponse'=>false));
			wp_die();
		}	
	}

	public function straker_translation_remove_item_from_cart() {

		check_ajax_referer( 'st-cart-nonce', 'nonce_security' );
		$wp_post_id = isset( $_REQUEST['postID'] ) ? sanitize_text_field( $_REQUEST['postID'] ) : '';
		if ( Straker_Translations_Cart_Handling::remove_item_from_cart( $wp_post_id ) ) {
			wp_send_json_success( array( 'isResponse' => true ) );
			wp_die();
		} else {
			wp_send_json_error( array( 'isResponse' => false ) );
			wp_die();
		}	
	}
	
	/**
	 *
	 */
	public function straker_buglog()
	{
		$constants  = get_defined_constants(true);
		$buglog_url = $constants['user']['STRAKER_BUGLOG'];
		return $buglog_url;
	}

	public function straker_bug_report($api_key, $msg, $eMessage, $eDetail, $HTMLReport = '')
	{
		$response = wp_remote_post($this->straker_buglog(), array(
			'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
			'body'    => array(
				'APIKey'           => $api_key,
				'applicationCode'  => 'WordPress Plugin',
				'HTMLReport'       => $HTMLReport,
				'templatePath'     => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
				'message'          => $msg,
				'severityCode'     => 'ERROR',
				'exceptionMessage' => $eMessage,
				'exceptionDetails' => $eDetail,
				'userAgent'        => $_SERVER['HTTP_USER_AGENT'],
				'dateTime'         => date('m/d/Y H:i:s'),
				'hostName'         => $_SERVER['HTTP_HOST'],
			),
		));
	}

	private function straker_buglog_html_report( $acces_token = '', $errorMsg, $errorCode, $lineNo ) {

		$html_report = '<h4>Exception Summary</h4>';
		$html_report .= '<table style="text-align: left;">';
		$html_report .= '<tr>';
		$html_report .= '<th>Server Address: </th>';
		$html_report .= '<td>' . $_SERVER['SERVER_ADDR'] . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Remote Address: </th>';
		$html_report .= '<td>' . $_SERVER['REMOTE_ADDR'] . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Host Name: </th>';
		$html_report .= '<td>' . $_SERVER['SERVER_NAME'] . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Request Method: </th>';
		$html_report .= '<td>' . $_SERVER['REQUEST_METHOD'] . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Server Date/Time: </th>';
		$html_report .= '<td>' . current_time( 'mysql' ) . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Acess Token: </th>';
		$html_report .= '<td>' . $acces_token . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Plugin URL: </th>';
		$html_report .= '<td>' . plugin_dir_path(__FILE__) . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Error File: </th>';
		$html_report .= '<td>' . __FILE__ . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Error Line No.</th>';
		$html_report .= '<td>' . $lineNo .'</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Site URL: </th>';
		$html_report .= '<td>' . get_site_url() . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Error Code: </th>';
		$html_report .= '<td>' . $errorCode . '</td>';
		$html_report .= '</tr>';
		$html_report .= '<tr>';
		$html_report .= '<th>Error Message: </th>';
		$html_report .= '<td>' . $errorMsg . '</td>';
		$html_report .= '</tr>';
		$html_report .= '</table>';

		return $html_report;
	}

	public function straker_clear_tranbslation_cart() {

		if (
			check_admin_referer('straker-translations-clear-cart', 'straker-translations-clear-cart-nonce') && 
			! empty( $_POST )			
		) {
			if( get_option( Straker_Translations_Config::straker_option_translation_cart ) ) {

				if ( delete_option( Straker_Translations_Config::straker_option_translation_cart ) )
					wp_redirect(admin_url('admin.php?page=st-jobs&msg=success&ac=trans_cart_empty'));
			} else {
				wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed&ac=trans_cart_empty_error'));
			}
		} else {
			wp_redirect(admin_url('admin.php?page=st-jobs&msg=failed&ac=trans_cart_empty_error'));
		}
	}

	public function getPluginList(){

		$installed_plugins = get_plugins();
		$active_plugins = get_option( "active_plugins" );
		$list_of_active_plugins['active_plugins'] = array();

		foreach ( $installed_plugins as $plugin_path => $value ) {
		
			if( in_array( $plugin_path, $active_plugins ) ) {
				array_push(
					$list_of_active_plugins['active_plugins'], 
					array( 
						'Plugin_Name' => $value['Name'], 
						'Plugin_Version' => $value['Version']
					)
				);	
			}
		}

		$server_information['server_information'] = 
			 array( 
				'Web_Server' => $_SERVER['SERVER_SOFTWARE'], 
				'PHP_Version' => PHP_VERSION, 
				'WordPress_Version' => get_bloginfo( 'version' ),
				'Server_Protocol' => $_SERVER['SERVER_PROTOCOL'],
				'User_Agent' => $_SERVER['HTTP_USER_AGENT']
			); 
		
		return json_encode( array_merge( $server_information, $list_of_active_plugins ) );
	}

	public function post_trash( $post_id ) {

		$straker_locale = '';

		if ( get_post_meta( $post_id, Straker_Translations_Config::straker_meta_locale ) ) {
			
			$straker_locale = get_post_meta( $post_id, Straker_Translations_Config::straker_meta_locale, true );
			$straker_lang_meta = Straker_Language::straker_language_meta( 'code', $straker_locale );
			$straker_source_post = get_post_meta( $post_id, Straker_Translations_Config::straker_meta_default . $straker_lang_meta['wp_locale'], true );
			$straker_target_langs_current = get_post_meta( $straker_source_post, Straker_Translations_Config::straker_meta_target, true );
			$straker_target_langs =  explode(',', get_post_meta( $straker_source_post, Straker_Translations_Config::straker_meta_target, true ) );
			
			if( in_array( $straker_locale, $straker_target_langs ) ) {
				
				unset( $straker_target_langs[ array_search( $straker_locale, $straker_target_langs ) ] );
				$updated_target_langs = implode( ',', $straker_target_langs );

				if( strlen( $updated_target_langs ) > 0 ){
					update_post_meta( $straker_source_post, Straker_Translations_Config::straker_meta_target, $updated_target_langs, $straker_target_langs_current );
				} else {
					delete_post_meta( $straker_source_post, Straker_Translations_Config::straker_meta_target );
				}
			}
		}
	}
}
