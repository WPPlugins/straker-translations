<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Straker_Translations
 * @subpackage Straker_Translations/includes
 * @author     Straker Translations <extensions@strakertranslations.com>
 */
class Straker_Translations
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Straker_Translations_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */

	public function __construct()
	{

		$this->plugin_name = 'straker-translations';
		$this->version     = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Straker_Translations_Loader. Orchestrates the hooks of the plugin.
	 * - Straker_Translations_i18n. Defines internationalization functionality.
	 * - Straker_Translations_Admin. Defines all hooks for the admin area.
	 * - Straker_Translations_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-loader.php';

		/**
		 * The class responsible for global configuration
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-config.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-i18n.php';

		/**
		 *  The classes responsible for Utility.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-util.php';

		/**
		 * The class responsible for global configuration
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-language.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-straker-translations-admin.php';

		/**
		 *  The classes responsible for rendering the list of posts and pages.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-list-table.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-ajax-list-table.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-pages-list-table.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-list-table-language-management.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-list-table-translation-order.php';

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-list-table-translation-cart-order.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-straker-translations-public.php';

		/**
		 *  The classes responsible for rendering the widget.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-widget.php';

		/**
		 *  The classes responsible for setting the locale.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-locale.php';

		/**
		 *  The classes responsible for the nav_menu.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-menu.php';

		/**
		 *  The classes responsible for the rewrite.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-rewrite.php';

		/**
		 *  The classes responsible for the links.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-link.php';

		/**
		 *  The classes responsible for the xml.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-xml.php';

		/**
		 *  The classes responsible for Walker_Nav_Menu.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-navmenu.php';

		/**
		 *  The classes responsible for WordPress Plugin support.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-plugin.php';

		/**
		 *  The classes responsible for rendering the shortcode.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-shortcode.php';

		/**
		 *  The class responsible for handling the trnaslation cart.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-straker-translations-cart-handling.php';

		/**
		 * The class responsible for language menu switcher.
		 */
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/settings/menu-switcher/class-straker-translations-menu-switcher.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/settings/menu-switcher/class-straker-translations-menu-list-item.php';

		/*
		 *  include_before_theme
		 *  This function will include core files before the theme's functions.php file has been excecuted.
		 *  */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/straker-translations-functions.php';

		$this->loader = new Straker_Translations_Loader();
		$Straker_Translations_Config = new Straker_Translations_Config();
		$Straker_Language = new Straker_Language();
		$Straker_Plugin = new Straker_Plugin();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Straker_Translations_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		// $plugin_i18n = new Straker_Translations_i18n();
		// $plugin_i18n->set_domain( $this->get_plugin_name() );
		//
		// $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
		$straker_locale = new Straker_Locale($this->get_plugin_name(), $this->get_version());
		$this->loader->add_filter('query_vars', $straker_locale, 'straker_query_vars');
		$this->loader->add_filter('locale', $straker_locale, 'straker_locale');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Straker_Translations_Admin($this->get_plugin_name(), $this->get_version());
		$check_default_lang = Straker_Language::get_default_language();
		$check_added_langs	= Straker_Language::get_added_language();
		$builtin_types	= array('post' => 'post','page' =>'page' );
		$all_post_types = ( get_option( Straker_Translations_Config::straker_registered_posts ) ) ? get_option( Straker_Translations_Config::straker_registered_posts ) : $builtin_types;
		$rewrite_option = Straker_Translations_Config::straker_rewrite_type();

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		/* add menu */
		$this->loader->add_action('admin_menu', $plugin_admin, 'straker_admin_menu');

		/* Add Settings link to the plugin */
		$this->loader->add_filter( 'plugin_action_links_' .STRAKER_PLUGIN_FILE, $plugin_admin, 'straker_translation_plugin_action_links' );
		$this->loader->add_filter('parent_file', $plugin_admin, 'straker_plugin_select_submenu');

		$this->loader->add_action('init', $plugin_admin, 'app_output_buffer');

		/* add various functions */
		$this->loader->add_action('admin_post_straker_register', $plugin_admin, 'straker_register');
		$this->loader->add_action('admin_post_straker_general_settings', $plugin_admin, 'straker_general_settings');
		$this->loader->add_action('admin_post_straker_test_delete', $plugin_admin, 'straker_test_delete');
		$this->loader->add_action('admin_post_straker_language_settings', $plugin_admin, 'straker_language_settings');
		$this->loader->add_action('admin_post_straker_url_settings', $plugin_admin, 'straker_url_settings');
		$this->loader->add_action('admin_post_straker_shortcode_settings', $plugin_admin, 'straker_shortcode_settings');
		$this->loader->add_action('admin_post_straker_request_quote', $plugin_admin, 'straker_request_quote');
		$this->loader->add_action('admin_post_straker_cancel_job', $plugin_admin, 'straker_cancel_job');
		$this->loader->add_action('admin_post_straker_support', $plugin_admin, 'straker_support');

		/* translation cart related hooks */
		$this->loader->add_action('admin_post_straker_clear_tranbslation_cart', $plugin_admin, 'straker_clear_tranbslation_cart');

		/* Ajax hooks */
		$this->loader->add_action('wp_ajax_straker_replace_translated_post', $plugin_admin, 'straker_replace_post');
		$this->loader->add_action('wp_ajax_st_order_list_table_ajax', $plugin_admin, 'straker_translation_order_ajax_custom_list_callback');
		$this->loader->add_action('wp_ajax_st_cart_order_list_table_ajax', $plugin_admin, 'straker_translation_cart_order_ajax_custom_list_callback');
		$this->loader->add_action('wp_ajax_st_translation_cart_ajax', $plugin_admin, 'straker_translation_add_single_item_into_cart');
		$this->loader->add_action('wp_ajax_st_translation_cart_remove_item_ajax', $plugin_admin, 'straker_translation_remove_item_from_cart');

		/* Ajax hooks for menu switcher class */
		$this->loader->add_action('wp_ajax_st_language_menu_switcher', 'Straker_Translations_Menu_Switcher', 'save_switcher_settings' );

		if( ! empty( $check_default_lang ) && ! empty( $check_added_langs ) ) {

			if ( ( 'code' === $rewrite_option ) ||  ( 'domain' === $rewrite_option ) ) {
				//WP Nav related hooks
				$this->loader->add_action( 'wp_add_nav_menu_item', $plugin_admin, 'straker_add_nav_items', 10, 3 );
				$this->loader->add_action( 'wp_update_nav_menu_item', $plugin_admin, 'straker_update_nav_fields', 10, 3 );

				//Taxonomies related hooks
				$this->loader->add_action( 'category_add_form_fields', $plugin_admin,'straker_taxonomy_add_page_custom_meta_field' );
				$this->loader->add_action( 'create_category', $plugin_admin,'straker_add_category_custom_fields_form',10, 2 );
				$this->loader->add_action( 'edited_category', $plugin_admin,'straker_add_category_custom_fields_form', 10, 2 );
				$this->loader->add_action( 'manage_category_custom_column', $plugin_admin,'straker_category_custom_field_column', 10, 3 );
				$this->loader->add_action( 'category_edit_form_fields', $plugin_admin,'straker_taxonomy_edit_page_custom_meta_field', 10, 1 );

				//Tags related hooks
				$this->loader->add_action('add_tag_form_fields', $plugin_admin,'straker_add_tag_page_custom_meta_field');
				$this->loader->add_action('create_post_tag', $plugin_admin,'straker_add_tag_custom_fields_form', 10, 2 );
				$this->loader->add_action('manage_post_tag_custom_column', $plugin_admin,'straker_tag_custom_field_column',10,3);
				$this->loader->add_action('edited_post_tag', $plugin_admin,'straker_add_tag_custom_fields_form', 10, 2 );
				$this->loader->add_action('post_tag_edit_form_fields', $plugin_admin,'straker_edit_tag_custom_fields', 10, 2 );

				//Edit Permalink Structure
				$this->loader->add_filter( 'post_type_link', $plugin_admin, 'straker_translated_custom_post_link', 10, 2 );
				$this->loader->add_filter( 'post_link', $plugin_admin, 'straker_translated_post_link', 10, 3 );
				$this->loader->add_filter( 'page_link', $plugin_admin, 'straker_translated_page_link', 10, 2 );

				//posts, pages and custom posts filters
				$this->loader->add_action( 'restrict_manage_posts', $plugin_admin, 'straker_language_dropdown_filter' );
				$this->loader->add_filter( 'parse_query', $plugin_admin, 'straker_language_filtering' );

				//MetaBoxs Hooks
				$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'straker_localized_assets_metabox' );

				//Categories and Post Tags Hook
				$this->loader->add_filter( 'manage_edit-category_columns', $plugin_admin, 'straker_category_custom_column' );
				$this->loader->add_filter( 'manage_edit-post_tag_columns', $plugin_admin, 'straker_tag_custom_column' );

				//Walker Nav Hook
				$this->loader->add_filter( 'wp_edit_nav_menu_walker', $plugin_admin, 'straker_edit_nav_menu_walker', 10, 2 );
			}

			$this->loader->add_action('add_meta_boxes', $plugin_admin, 'straker_translation_lang_meta');
			$this->loader->add_action('post_edit_form_tag', $plugin_admin, 'update_edit_form');
			$this->loader->add_action('save_post', $plugin_admin, 'save_post_locale_language_mb');
			$this->loader->add_action('edit_post', $plugin_admin, 'save_assets_metabox');
			$this->loader->add_action('admin_post_straker_import_translation', $plugin_admin, 'straker_import_translation');

			$this->loader->add_action('wp_loaded', $plugin_admin, 'straker_get_registered_posts_names', 20);
			$this->loader->add_action('before_delete_post', $plugin_admin, 'post_trash');

			// $this->loader->add_action('upgrader_process_complete', $plugin_admin, 'straker_plugin_update',10, 2);

			if( $all_post_types ) {
				foreach( $all_post_types as $val ) {
					$this->loader->add_filter('manage_'.$val.'_posts_columns', $plugin_admin, 'straker_add_custom_cloumn_manage_posts_pages');
					$this->loader->add_filter('manage_'.$val.'_posts_custom_column', $plugin_admin, 'straker_custom_cloumn_content_posts_pages', 10, 2);
				}
			}

		}

		$this->loader->add_filter('upload_mimes', $plugin_admin, 'straker_enable_mime_types');
		$this->loader->add_filter('straker_job_title', $plugin_admin, 'straker_job_title');
		$this->loader->add_filter('straker_content_query', $plugin_admin, 'straker_content_query');
		$this->loader->add_filter('straker_get_jobs', $plugin_admin, 'straker_get_jobs');
		$this->loader->add_filter('http_request_timeout', $plugin_admin, 'straker_timeout_time');
		$this->loader->add_action( 'wp_logout', $plugin_admin, 'straker_remove_plugin_custom_cookies' );

		// Edit the Revision title if revision has updated translation
		$this->loader->add_filter( 'wp_post_revision_title_expanded', $plugin_admin, 'straker_revision_title_edit', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$rewrite_option = Straker_Translations_Config::straker_rewrite_type();
		if ( ( 'code' === $rewrite_option ) ||  ( 'domain' === $rewrite_option ) ) {

			$plugin_public = new Straker_Translations_Public( $this->get_plugin_name(), $this->get_version() );

			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
			$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

			/* Rewrite */
			$straker_rewrite = new Straker_Rewrite($this->get_plugin_name(), $this->get_version());
			$this->loader->add_action('template_redirect', $straker_rewrite, 'straker_frontpage_rewrite');

			if ( 'code' === $rewrite_option ) {
				/* rewrite_rules */
				$this->loader->add_filter('init', $straker_rewrite, 'straker_add_rewrite_tags');
				$this->loader->add_filter('root_rewrite_rules', $straker_rewrite, 'straker_root_rewrite_rules');
				$this->loader->add_filter('page_rewrite_rules', $straker_rewrite, 'straker_page_rewrite_rules');
				$this->loader->add_filter('post_rewrite_rules', $straker_rewrite, 'straker_post_rewrite_rules');
				$this->loader->add_filter('post_tag_rewrite_rules', $straker_rewrite, 'straker_post_tags_rewrite_rules');
				$this->loader->add_filter('category_rewrite_rules', $straker_rewrite, 'straker_categories_rewrite_rules');

				/* rewrite_rules for custom post types*/
				$this->loader->add_action('registered_post_type', $straker_rewrite, 'straker_register_custom_post_type_rules', 10, 2 );
				$this->loader->add_action('registered_taxonomy', $straker_rewrite, 'straker_register_custom_taxonomies_rules', 10, 3 );

				/*  categories,tags arg*/
				$this->loader->add_filter('widget_categories_args', $plugin_public, 'straker_widget_categories_args_filter', 10, 1 );
				$this->loader->add_filter('widget_tag_cloud_args', $plugin_public, 'straker_widget_tag_cloud_args_filter', 10, 1 );
				// $this->loader->add_filter('get_terms_args', $plugin_public, 'straker_terms_widget_args', 10, 2 );
			}

			/* nav_menu */
			$straker_nav_menu = new Straker_Nav_Menu($this->get_plugin_name(), $this->get_version());

			$this->loader->add_filter( 'wp_get_nav_menu_items', $straker_nav_menu, 'get_nav_menu_items' );
			$this->loader->add_filter( 'wp_setup_nav_menu_item', $straker_nav_menu, 'setup_nav_menu_item' );
			$this->loader->add_filter( 'wp_nav_menu_objects', $straker_nav_menu, 'set_nav_menu_objects', 10, 2 );

			/* Links */
			$straker_link = new Straker_Link();
			if ( 'code' === $rewrite_option ) {
				$this->loader->add_filter('home_url', $straker_link, 'straker_home_url');
			}

			/* Widget */
			$this->loader->add_action('widgets_init', $plugin_public, 'straker_register_widget');
			$this->loader->add_action('widget_title', $plugin_public, 'straker_widget_title');
			$this->loader->add_filter('widget_posts_args', $plugin_public, 'straker_alter_widget');

			/* Archive Widget */
			$this->loader->add_filter('getarchives_where', $plugin_public, 'straker_archives_widget_args', 10, 1);

			/* Comments Widget */
			$this->loader->add_filter('widget_comments_args', $plugin_public, 'straker_comments_widget_args', 10, 1);
			$this->loader->add_action('wp_insert_comment', $plugin_public, 'straker_insert_comment', 10, 2);

			/* Main Query */
			$this->loader->add_action('pre_get_posts', $plugin_public, 'straker_alter_query');

		}

		if ( ( 'code' === $rewrite_option ) ||  ( 'domain' === $rewrite_option )  || ( 'none' === $rewrite_option ) ) {

			/* Shortcode */
			$straker_short_code = new Straker_Short_Code();

		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Straker_Translations_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

}
