<?php



/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/public
 * @author     Straker Translations <apisupport@strakertranslations.com>
 */
class Straker_Translations_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Straker_Translations_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Straker_Translations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/straker-translations-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Straker_Translations_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Straker_Translations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/straker-translations-public.js', array('jquery'), $this->version, false);

	}

	/**
	 *
	 */
	public function straker_register_widget()
	{
		register_widget('Straker_Language_List');
	}

	/**
	 *
	 */
	public function straker_widget_title($instance)
	{

		$theme      = wp_get_theme();
		$translated = translate($instance, $theme->template);
		return $translated;

	}

	public function straker_widget_categories_args_filter($cat_args){
		if (!is_admin()) {
			$locale = get_locale();
			$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
			$cat_args = array(
				'hide_empty' => false,
				'title_li' => '',
					'meta_key' =>Straker_Translations_Config::straker_cat_lang_meta,
					'meta_value'=> $lang_meta['code']);
			return $cat_args;
		}
	}

	public function straker_widget_tag_cloud_args_filter($tag_args){

		if (!is_admin()) {
		$locale = get_locale();
		$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
		$tag_args = array(
			'echo' => '',
			'orderby'	=> 'name',
				'meta_key' =>Straker_Translations_Config::straker_tag_lang_meta,
				'meta_value'=> $lang_meta['code']);
		return $tag_args;
		}
	}

	public function straker_terms_widget_args($args, $taxonomies ){
		$locale = get_locale();
		$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
		if (is_admin()) {
			return $args;
		}
	
		if ( ( is_array( $args['taxonomy'] ) && in_array( 'category', $args['taxonomy'] ) ) || 'category' === $args['taxonomy'] ) {

			$args = array(
					'taxonomy'   => null,
					'hide_empty' => false,
					'child_of'	 => '',
					'parent'		 => '',
					'exclude_tree'		 => '',
					'update_term_meta_cache'		 => '',
					'fields'		  => '',
					'type'		  => '',
					'offset'		  => '',
					'number'		  => '',
					'get'         => '',
					'fields'		 	=> 'all',
					'hierarchical'=> '',
					'childless'		=> '',
					'include'		 	=> '',
					'exclude'		 	=> '',
					'order'		 	 	=> '',
					'orderby'		 	=> '',
					'pad_counts' 	=> '',
					'meta_query' 	=> array (
				array (
				'key' => Straker_Translations_Config::straker_cat_lang_meta,
				'value'=> $lang_meta['code'],
			)));
				return $args;

		}
		
			if($taxonomies[0] == "category"){
				$args = array(
					'taxonomy'   => null,
					'hide_empty' => false,
					'child_of'	 => '',
					'parent'		 => '',
					'exclude_tree'		 => '',
					'update_term_meta_cache'		 => '',
					'fields'		  => '',
					'offset'		  => '',
					'number'		  => '',
					'get'         => '',
					'fields'		 	=> 'all',
					'hierarchical'=> '',
					'childless'		=> '',
					'include'		 	=> '',
					'exclude'		 	=> '',
					'order'		 	 	=> '',
					'orderby'		 	=> '',
					'pad_counts' 	=> '',
					'meta_query' 	=> array (
				array (
				'key' => Straker_Translations_Config::straker_cat_lang_meta,
				'value'=> $lang_meta['code'],
			)));
				return $args;
			}else if($taxonomies[0] == "post_tag"){
				$args = array(
					'taxonomy'   => null,
					'hide_empty' => false,
					'child_of'	 => '',
					'parent'		 => '',
					'exclude_tree'		 => '',
					'update_term_meta_cache'		 => '',
					'fields'		  => '',
					'offset'		  => '',
					'number'		  => '',
					'fields'		 	=> 'all',
					'hierarchical'=> '',
					'childless'		=> '',
					'get'         => '',
					'include'		 	=> '',
					'exclude'		 	=> '',
					'order'		 	 	=> '',
					'orderby'		 	=> '',
					'pad_counts' 	=> '',
					'meta_query' 	=> array (
				array (
				'key' => Straker_Translations_Config::straker_tag_lang_meta,
				'value'=> $lang_meta['code'],
			)));
				return $args;
			}else{
				return $args;
			}
			return $args;
	}

	public function straker_alter_widget($args){
		$locale = get_locale();
		$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
		$args = array('posts_per_page'=>$args['posts_per_page'],'meta_query' 	=> array(array('key' => Straker_Translations_Config::straker_meta_locale,'value'=> $lang_meta['code'])));
		return $args;
	}

	public function straker_archives_widget_args($where)
	{
		global $wpdb;
		$table_post = $wpdb->prefix . 'posts';
		$table_meta = $wpdb->prefix . 'postmeta';
		$locale = get_locale();
		$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
		$$where = " INNER JOIN $table_meta ON (	$table_post.ID = $table_meta.post_id) WHERE $table_meta.meta_key = '".Straker_Translations_Config::straker_meta_locale."' AND $table_meta.meta_value = '".$lang_meta['code']."'" ;
    return $where;
	}

	public function straker_comments_widget_args($comment_args ){
	  $locale = get_locale();
    $lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
    $comment_args = array('meta_key' => Straker_Translations_Config::straker_meta_locale,'meta_value'=> $lang_meta['code']);
		return $comment_args;
	}

	public function  straker_insert_comment($id,$comment)
	{
		$post_locale = get_post_meta($comment->comment_post_ID, Straker_Translations_Config::straker_meta_locale);
		$post_meta = !empty($post_locale)?get_post_meta($comment->comment_post_ID, Straker_Translations_Config::straker_meta_locale,true):false;
		if($post_meta){
			add_comment_meta( $comment->comment_ID, Straker_Translations_Config::straker_meta_locale, $post_meta);
		}
	}

	/**
	 *
	 */
	public function straker_alter_query($query)
	{
		global $wp_query;

		if (!is_admin() && $query->is_main_query()) {

			$locale 			   = get_locale();
			$get_site_locale = Straker_Language::get_default_language();
			$lang_meta = Straker_Language::straker_language_meta(Straker_Translations_Config::straker_wp_locale, $locale);
			$query->set('meta_key', Straker_Translations_Config::straker_meta_locale);
			$query->set('meta_value', $lang_meta['code']);
			remove_all_actions('__after_loop');
		}

	}

}
