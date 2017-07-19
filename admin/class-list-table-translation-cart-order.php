<?php

/**
 * A Wp_List_Table implementation for listing posts and pages.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/admin
 */

class Straker_Translation_Cart_Order_Page_List_Table_Ajax extends Straker_Translations_Ajax_List_Table
{

	
	/**
	 *  The Query Args for the WP_Query.
	 *
	 * @access  private
	 * @var     array  The  Query Args for the WP_Query.
	 */
	private $text_domain;
	
    public function __contruct( $text_domain ) {
        
        global $status, $page;
		//Set parent defaults
		parent::__construct(
			array(
				//singular name of the listed records
				'singular'	=> 'st_cart_order',
				//plural name of the listed records
				'plural'	=> 'st_trans_cart_oreder_tbl',
				//does this table support ajax?
				'ajax'		=> true
			)
		);
		$this->text_domain = $text_domain;		
    }

	/**
	 * Get the Query Arguments for the WP_QUERY.
	 *
	 */
	 public function get_translation_cart() {

		return ( get_option(  Straker_Translations_Config::straker_option_translation_cart ) ) ? explode(',', get_option(  Straker_Translations_Config::straker_option_translation_cart ) ) : false ;

	 }

	/**
	 * Get the Query Arguments for the WP_QUERY.
	 *
	 */
	 public function get_types() {
		return get_option( Straker_Translations_Config::straker_registered_posts );
	 }

	 private function check_acf_data_chb() {

		if( isset( $_REQUEST['acf_data_checked'] ) ){
			if( 'true' === $_REQUEST['acf_data_checked'] ) {
				return 'show';
			} elseif(  'false' === $_REQUEST['acf_data_checked'] ) {
				return 'hide';
			}
		}
	}
	
	private function check_yoast_data_chb() {

		if( isset( $_REQUEST['yoast_data_checked'] ) ){
			if( 'true' === $_REQUEST['yoast_data_checked'] ) {
				return 'show';
			} elseif(  'false' === $_REQUEST['yoast_data_checked'] ) {
				return 'hide';
			}
		}
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function no_items() {
		_e( 'No items in the translation cart.', $this->text_domain );
	}

	public function column_default( $item, $column_name ) {
		
		$translated_tooltip = sprintf( __( "Remove from selection", '%s'  ), $this->text_domain ) ;
		$translated_acf = sprintf( __( "Advance Custom Field source data created", '%s'  ), $this->text_domain );
		$translated_yoast = sprintf( __( "Yoast source data created", '%s'  ), $this->text_domain ) ;
        switch ( $column_name ) {
            case 'post_title':
			case 'post_status':
				if ($item->post_status == 'publish') {
					return ucfirst('Published');
				} else {
					return ucfirst($item->post_status);
				}
			case 'post_type':
				return ucfirst( $item->post_type );
			case 'st_post_acf_data':
				if ( Straker_Plugin::plugin_exist( 'acf' ) ) {
					if ( sizeof( Straker_Plugin::straker_acf_plugin_check( $item->ID ) ) > 0 ) { 
						if( 'show' === $this->check_acf_data_chb() ) {
							return '<img st-data-tooltip title="'. $translated_acf. '" width="25px" src="' .plugins_url( 'admin/img/green-mark.svg', dirname(__FILE__) ). '" class="st_acf_img" alt="acf data">';
						} elseif( 'hide' === $this->check_acf_data_chb()) {
							return '<img st-data-tooltip title="'. $translated_acf. '" width="25px" src="' .plugins_url( 'admin/img/green-mark.svg', dirname(__FILE__) ). '" class="st_acf_img" alt="acf data" style="display:none;">';
						} else {
							return '<img st-data-tooltip title="'. $translated_acf. '" width="25px" src="' .plugins_url( 'admin/img/green-mark.svg', dirname(__FILE__) ). '" class="st_acf_img" alt="acf data">';
						}
						
					} else{
						return '';
					}
				}
			case 'st_post_yoast_data':
				if ( Straker_Plugin::plugin_exist( 'wp-seo' ) ) {
					if ( sizeof( Straker_Plugin::straker_wpseo_check( $item->ID ) ) > 0 ) {
						if( 'show' === $this->check_yoast_data_chb() ) {
							return '<img st-data-tooltip title="'. $translated_yoast. '" width="25px" src="' .plugins_url( 'admin/img/green-mark.svg', dirname(__FILE__) ). '" class="st_yoast_img" alt="yoast data">';
						} elseif( 'hide' === $this->check_yoast_data_chb()) {
							return '<img st-data-tooltip title="'. $translated_yoast. '" width="25px" src="' .plugins_url( 'admin/img/green-mark.svg', dirname(__FILE__) ). '" class="st_yoast_img" alt="yoast data" style="display:none;">';
						} else {
							return '<img st-data-tooltip title="'. $translated_yoast. '" width="25px" src="' .plugins_url( 'admin/img/green-mark.svg', dirname(__FILE__) ). '" class="st_yoast_img" alt="yoast data">';
						}
						
					} else {
						return '';
					}
				}
			case 'post_date':
				return date('Y/m/d h:i A', strtotime($item->post_date));
			case 'remove_selection':
				return '<a href="#" class="st-delete-cart-item" st-data-tooltip title="' . $translated_tooltip . '" data-type="' . $item->post_type . '" id="' . $item->ID . '"><img src="'.plugins_url( 'admin/img/remove_icon.gif', dirname(__FILE__) ).'" /></a>';
			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	public function get_columns() {

		if ( Straker_Plugin::plugin_exist( 'wp-seo' ) && ! Straker_Plugin::plugin_exist( 'acf' ) ) {
			return $columns = array(
				'post_title' => __('Title', $this->text_domain ),
				'post_type'	 => __('Type', $this->text_domain ),
				'st_post_yoast_data'	 => __('Yoast', $this->text_domain ),
				'post_status' => __('Status', $this->text_domain ),
				'post_date'	=> __('Date Published', $this->text_domain ),
				'remove_selection' => '',
			);
		} elseif ( ! Straker_Plugin::plugin_exist( 'wp-seo' ) && Straker_Plugin::plugin_exist( 'acf' )  ){
			return $columns = array(
				'post_title' => __('Title', $this->text_domain ),
				'post_type'	 => __('Type', $this->text_domain ),
				'st_post_acf_data'	 => __('ACF', $this->text_domain ),
				'post_status' => __('Status', $this->text_domain ),
				'post_date'	=> __('Date Published', $this->text_domain ),
				'remove_selection' => '',
			);
		} elseif ( Straker_Plugin::plugin_exist( 'wp-seo' ) && Straker_Plugin::plugin_exist( 'acf' ) ) {
			return $columns = array(
				'post_title' => __('Title', $this->text_domain ),
				'post_type'	 => __('Type', $this->text_domain ),
				'st_post_acf_data'	 => __('ACF', $this->text_domain ),
				'st_post_yoast_data'	 => __('Yoast', $this->text_domain ),
				'post_status' => __('Status', $this->text_domain ),
				'post_date'	=> __('Date Published', $this->text_domain ),
				'remove_selection' => '',
			);
		} else {
			return $columns = array(
				'post_title' => __('Title', $this->text_domain ),
				'post_type'	 => __('Type', $this->text_domain ),
				'post_status' => __('Status', $this->text_domain ),
				'post_date'	=> __('Date Published', $this->text_domain ),
				'remove_selection' => '',
			);
		}
	}

	public function get_hidden_columns()
	{
		return array('post_modified');
	}
	
	public function column_post_title($item) {
		
		$post_id = $item->ID;
		$post = get_post($post_id);

		if ($post) {
			//$url = 'post.php?post=' . $post_id . '&action=edit';
			return '<a href="' . get_edit_post_link($post->ID) . '" target="_blank">' . $post->post_title . '</a>';
		}

		return 'No post';//__('No post', $this->text_domain);
	}

	/**
	 * Gets the name of the default primary column.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @return string Name of the default primary column, in this case, an empty string.
	 */
	protected function get_default_primary_column_name()
	{
		$columns = $this->get_columns();
		$column  = '';

		// We need a primary defined so responsive views show something,
		// so let's fall back to the first non-checkbox column.
		foreach ($columns as $col => $column_name) {
			if ('post_title' === $col) {
				continue;
			}

			$column = $col;
			break;
		}

		return $column;
	}

	/**
	 * Returns the columns that can be used for sorting the list table data.
	 *
	 * @return array    The database columns that can be used for sorting the table.
	 */
	public function get_sortable_columns()
	{
		return array(
			'post_title' => array('post_title', true),
			'post_date' => array('post_date', true),
			'post_type' => array('post_type', true),

		);
	}
	
	public function prepare_items() {
		
		global $wpdb;
		$columns      = $this->get_columns();
		$hidden       = $this->get_hidden_columns();
		$sortable     = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$page_per_page = 100;

		if ( ! $this->get_translation_cart() ) {
			$this->_pagination_args['total_pages'] = 0;
			$this->_pagination_args['total_items'] = 0;
			return ! empty( $this->items );
		}

		$query_args = array(
				'post__in' => $this->get_translation_cart(),
				'post_type'	=> $this->get_types(),
			);
		$query       = new WP_Query( $query_args );
        $total_items = $query->found_posts;
		$offset = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $page_per_page) : 0;
		$page = 1;

		if (isset($_REQUEST['paged'])) {
			$page = $_REQUEST['paged'];
		}

		// Sorting
		$order_by = 'post_title'; // Default sort key
		if (isset($_REQUEST['orderby'])) {
			// If the requested sort key is a valid column, use it for sorting
			if (in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) {
				$order_by = $_REQUEST['orderby'];
			}
		}
		$order = 'asc'; // Default sort order
		if (isset($_REQUEST['order'])) {
			if (in_array($_REQUEST['order'], array('asc', 'desc'))) {
				$order = $_REQUEST['order'];
			}
		}
		$orders = $order_by . ', ' . $order;

		$extra_args = array(
			'posts_per_page' => $page_per_page,
			'offset' => $offset,
			'paged' => $page,
			'orderby' => $order_by,
			'order' => $order
		);

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $page_per_page,
				'total_pages' => ceil($total_items / $page_per_page),
				'orderby'     => $order_by,
				'order'       => $order,
			)
		);
		// Do the SQL query and populate items

        $get_posts       = new WP_Query( array_merge( $query_args, $extra_args ) );
        $this->items = $get_posts->posts;
	}

    	/**
	 * Display the table
	 * Adds a Nonce field and calls parent's display method
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function display() {

		wp_nonce_field( 'st-trans-cart-order-ajax-nonce', '_st_trans_cart_oredr_ajax_nonce' );

		if( isset( $this->_pagination_args['order'] ) ) {
			echo '<input type="hidden" id="order" name="order" value="' . $this->_pagination_args['order'] . '" />';
		}
		if( isset( $this->_pagination_args['orderby'] ) ) {
			echo '<input type="hidden" id="orderby" name="orderby" value="' . $this->_pagination_args['orderby'] . '" />';
		}
		parent::display();
	}

	/**
	 * Handle an incoming ajax request (called from admin-ajax.php)
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function ajax_response() {

		check_ajax_referer( 'st-trans-cart-order-ajax-nonce', '_st_trans_cart_oredr_ajax_nonce' );

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();
		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();

		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		die( json_encode( $response ) );
	}
}