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

class Pages_List_Table extends Straker_Translations_List_Table
{

	/**
	 * The plugin's text domain.
	 *
	 * @access  private
	 * @var     string  The plugin's text domain. Used for localization.
	 */

	private $text_domain;
	/**
	 * The plugin's text domain.
	 *
	 * @access  private
	 * @var     string  The plugin's text domain. Used for localization.
	 */
	private $all_post_types;
	private $all_posts_status;
	private $default_lang;

	/**
	 * The Option name of the Cart.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cart_option_name    The name of the cart.
	*/

    private static $translation_cart_option = '';

	/**
	 * Initializes the WP_List_Table implementation.
	 *
	 * @param $text_domain  string  The text domain used for localizing the plugin.
	 */
	public function __construct($text_domain)
	{

		global $status, $page;

		//Set parent defaults
		parent::__construct(array(
			'singular' => 'post_page', //singular name of the listed records
			'plural'   => 'post_pages', //plural name of the listed records
			'ajax'     => false, //does this table support ajax?
		));

		$this->text_domain 		= $text_domain;
		$this->default_lang		= Straker_Language::get_default_language();
		$this->all_post_types	= get_option( Straker_Translations_Config::straker_registered_posts );
		$this->all_posts_status	= array( 'publish', 'pending', 'draft', 'future', 'private' );
		self::$translation_cart_option =  ( false !== get_option(  Straker_Translations_Config::straker_option_translation_cart ) ) ? get_option(  Straker_Translations_Config::straker_option_translation_cart ) : false;

	}

	/**
	 * Defines the database columns shown in the table and a
	 * header for each column. The order of the columns in the
	 * table define the order in which they are rendered in the list table.
	 *
	 * @return array    The database columns and their headers for the table.
	 */
	public function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'post_title':
			case 'post_status':
				if ($item->post_status == 'publish') {
					return ucfirst('Published');
				} else {
					return ucfirst($item->post_status);
				}

			case 'post_type':
				return ucfirst( $item->post_type );

			case 'post_locale':
				$lang_meta = Straker_Util::get_meta_by_post_id($item->ID);
				return '<img src="' . STRAKER_PLUGIN_ABSOLUTE_PATH . '/assets/img/flags/' . $this->default_lang['code'] . '.png" style="vertical-align:middle" st-data-tooltip title="'. $this->default_lang['name'] .'">';

			case 'post_date':
				return date('Y/m/d h:i A', strtotime($item->post_date));

			case 'meta_value':
				$aMeta = Straker_Util::get_meta_by_value($item->ID);
				if ($aMeta) {
					$flag = '';
					foreach ( $aMeta as $key => $value ) {
						$alng = Straker_Language::straker_language_meta( 'code', $value['code'] );
						$flag .= '<a st-data-tooltip title="' .  $alng['name'] . '" href="' . get_edit_post_link($value['post_id']) . '" target="_self"><img src="' . STRAKER_PLUGIN_ABSOLUTE_PATH . '/assets/img/flags/' . $value['code'] . '.png" style="vertical-align:middle"></a>&nbsp;&nbsp';
					};
					return $flag;
				} else {
					return ' ';
				}

			default:
				return print_r($item, true); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Returns the names of columns that should be hidden from the list table.
	 *
	 * @return array    The database columns that should not be shown in the table.
	 */
	public function get_hidden_columns()
	{
		return array('post_modified');
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
			'post_type'  => array('post_type', true),
			'post_date'  => array('post_date', false),
		);
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 * @access public
	 */
	public function no_items()
	{
		_e( 'No content is assigned to your source language. Please assign a source language to the pages you wish to translate under <strong>Settings > Language Management</strong>.', $this->text_domain );
	}

	private function get_target_language(){

	    $straker_languages = get_option('straker_languages');
	    return $straker_languages['tl'];
    }

	public function extra_tablenav($which = '')
	{
		if ($which == "top") {
			$post_filter	= isset($_REQUEST['post-type-filter']) ? $_REQUEST['post-type-filter'] : "";
			$post_status	    = isset($_REQUEST['post-status-filter']) ? $_REQUEST['post-status-filter'] : "";
			$post_is_translated	    = isset($_REQUEST['post-is_translated-filter']) ? $_REQUEST['post-is_translated-filter'] : "";
			?>
			<div class="alignleft actions bulkactions">
				<select name="post-type-filter" id="post-type-filter">
					<option value="all"><?php echo __('Filter by Post Types', $this->text_domain); ?></option>
						<?php
							foreach ( $this->all_post_types as $key ) {
								$selectd_type = ( $key == $post_filter ) ? 'selected' : '';
								$display_val 	= get_post_type_object( $key );
								echo '<option value="'.$key.'" '.$selectd_type.'>'.$display_val->label.'</option>';
							}
						?>
				</select>
                <select name="post-status-filter" id="post-status-filter">
                    <option value="all"><?php echo __('Filter by Post Status', $this->text_domain); ?></option>
                    <?php
                    foreach ( $this->all_posts_status as $post_key ) {
                        $selected_status =  ( $post_key == $post_status ) ? 'selected' : '';
                        $post_status_text = ($post_key == 'publish')? 'Published' : ucfirst($post_key);
                        echo '<option value="'.$post_key.'" '.$selected_status.'>'.$post_status_text.'</option>';
                    }
                    ?>
                </select>
                <?php
                $checkboxes = '';
                $tl_language_values = '';

                foreach ($this->get_target_language() as $tl_code){
                    $alng = Straker_Language::straker_language_meta('code', $tl_code);
                    $checked = in_array($tl_code,explode(",",$post_is_translated))? 'checked' : '';
                    $tl_language_values .= ($checked == 'checked')? $alng['name'].', ' : false ;
                    $checkboxes .=  '<li><input type="checkbox" value="'.$tl_code.'" '.$checked.' />'.$alng['name'].'</li>';
                }
                ?>

                <div class="multi-select">
                    <input id="language-filter" type="text" placeholder="Filter by Language" value="<?php echo substr(trim($tl_language_values), 0, -1) ?>" readonly="readonly">
                    <div class="drop-down hide">
                        <ul>
                            <li><input type="checkbox" value="all" <?php echo (strpos($post_is_translated,'all')>-1)? "checked" : false ?>>Select All</li>
                            <hr>
                            <?php echo $checkboxes ?>
                        </ul>
                    </div>
                </div>

                <button id="st-translations-filter"class="button action">Filter</button>
			</div>
			<?php
		}
	}

	/**
	 * displaying checkbox for bulk action.
	 *
	 * @return array    The database columns that can be used for sorting the table.
	 */
	public function column_cb($item)
	{

		if( false !== self::$translation_cart_option ) {
			if( in_array( $item->ID, explode( ',', self::$translation_cart_option ) ) ) {
			// if( false !== strpos( self::$translation_cart_option, (string)$item->ID ) ) {
				return sprintf(
					'<div st-data-tooltip class="st-cart-img st-txt-center" title="%s"><a href="%s"><img class="st-cart-img" src="%s" /></a></div>',
					__( ucfirst( $item->post_type ) .' already in the translation cart.', $this->text_domain ),
					admin_url('admin.php?page=st-translation-cart'),
					STRAKER_PLUGIN_ABSOLUTE_PATH . '/admin/img/st-cart.png'
				);
			} else {
				return sprintf(
					'<input type="checkbox" name="%1$s[]" value="%2$s" id="st-order-%2$s" class= "trans_chkbox" />',
					/*$1%s*/$this->_args['singular'], //Let's simply repurpose the table's singular label ("post_page")
					/*$2%s*/ $item->ID//The value of the checkbox should be the record's id
				);
			}
		} else {
			return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" id="st-order-%2$s" class= "trans_chkbox" />',
			/*$1%s*/$this->_args['singular'], //Let's simply repurpose the table's singular label ("post_page")
			/*$2%s*/ $item->ID//The value of the checkbox should be the record's id
			);
		}
	}

	/**
	 * Custom renderer for the post_title field.
	 *
	 * @param $item     array   The database row being printed out.
	 * @return string   The text or HTML that should be shown for the column.
	 */
	public function column_post_title($item)
	{

		$post_id = $item->ID;

		$post = get_post($post_id);

		if ($post) {
			//$url = 'post.php?post=' . $post_id . '&action=edit';
			return '<a href="' . get_edit_post_link($post->ID) . '" target="_blank">' . $post->post_title . '</a>';
		}

		return __('No post', $this->text_domain);
	}

	/**
	 * Defines the database columns shown in the table and a
	 * header for each column. The order of the columns in the
	 * table define the order in which they are rendered in the list table.
	 *
	 * @return array    The database columns and their headers for the table.
	 */
	public function get_columns()
	{

		$columns = array(
			'cb'          => 'checkbox', //Render a checkbox instead of text
			'post_title'  => __('Title', $this->text_domain),
			'post_type'   => __('Type', $this->text_domain),
			'post_locale' => __('Language', $this->text_domain),
			'post_date'   => __('Date Published', $this->text_domain),
			'post_status' => __('Status', $this->text_domain),
			'meta_value'  => __('Translation', $this->text_domain),
		);

		return $columns;

	}

	public function print_column_headers($with_id = true)
	{
		list($columns, $hidden, $sortable, $primary) = $this->get_column_info();

		$current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$current_url = remove_query_arg('paged', $current_url);

		if (isset($_GET['orderby'])) {
			$current_orderby = sanitize_text_field($_GET['orderby']);
		} else {
			$current_orderby = '';
		}

		if (isset($_GET['order']) && 'desc' == $_GET['order']) {
			$current_order = 'desc';
		} else {
			$current_order = 'asc';
		}

		if (!empty($columns['cb'])) {
			static $cb_counter = 1;
			$columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __('Select All') . '</label>'
				. '<input id="stCheckAllCheckBox" class="" type="checkbox" st-data-tooltip title="'. __("Select/Deselect All Visible", $this->text_domain).'" />';
			$cb_counter++;
		}

		foreach ($columns as $column_key => $column_display_name) {
			$class = array('manage-column', "column-$column_key");

			if (in_array($column_key, $hidden)) {
				$class[] = 'hidden';
			}

			if ('cb' == $column_key) {
				$class[] = 'check-column';
			} elseif (in_array($column_key, array('posts', 'comments', 'links'))) {
				$class[] = 'num';
			}

			if ($column_key === $primary) {
				$class[] = 'column-primary';
			}

			if (isset($sortable[$column_key])) {
				list($orderby, $desc_first) = $sortable[$column_key];

				if ($current_orderby == $orderby) {
					$order   = 'asc' == $current_order ? 'desc' : 'asc';
					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order   = $desc_first ? 'desc' : 'asc';
					$class[] = 'sortable';
					$class[] = $desc_first ? 'asc' : 'desc';
				}

				$column_display_name = '<a href="' . esc_url(add_query_arg(compact('orderby', 'order'), $current_url)) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
			}

			$tag   = ('cb' === $column_key) ? 'td' : 'th';
			$scope = ('th' === $tag) ? 'scope="col"' : '';
			$id    = $with_id ? "id='$column_key'" : '';

			if (!empty($class)) {
				$class = "class='" . join(' ', $class) . "'";
			}

			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}


	/**
	 * Populates the class fields for displaying the list of post and pages.
	 */
	public function prepare_items()
	{

		$post_status	= $this->all_posts_status ;
		$columns      = $this->get_columns();
		$hidden       = $this->get_hidden_columns();
		$sortable     = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		// Pagination
		$page_per_page = 100;

		$query_args = array(
							'post_status'    => $post_status,
                            'meta_key'       => Straker_Translations_Config::straker_meta_locale,
							'meta_value'     => $this->default_lang['code'],
                            'post_type'      => $this->all_post_types
						);

		if(isset($_REQUEST['s'])){

		    $query_args['s'] = sanitize_text_field($_REQUEST['s']);
        }

        if(isset($_REQUEST['post-type-filter']) && $_REQUEST['post-type-filter'] != 'all'){

            $query_args['post_type'] = sanitize_text_field($_REQUEST['post-type-filter']);
        }

        if(isset($_REQUEST['post-status-filter']) && $_REQUEST['post-status-filter'] != 'all' ){

            $query_args['post_status'] = sanitize_text_field($_REQUEST['post-status-filter']);
        }

        if(isset($_REQUEST['post-is_translated-filter']) && $_REQUEST['post-is_translated-filter'] != 'all'){

            $tl_languages = explode(',',$_REQUEST['post-is_translated-filter']);

            $query_args['meta_query']['relation'] = 'OR';

            foreach ($tl_languages as $language){

                $query_args['meta_query'][] = array(
                        'key' => Straker_Translations_Config::straker_meta_target,
                        'compare'=>'LIKE',
                        'value' => $language
                );
            }
        }

        $query       = new WP_Query($query_args);
        $total_items = $query->found_posts;
		$offset = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged'] - 1) * $page_per_page) : 0;
		$page = 1;

		if (isset($_REQUEST['paged'])) {
			$page = $_REQUEST['paged'];
		}

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $page_per_page,
				'total_pages' => ceil($total_items / $page_per_page),
			)
		);
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
							'orderby'        => $order_by,
							'order'          => $order,
							'posts_per_page' => $page_per_page,
							'paged'          => $page,
							'offset'         => $offset,
						);
		// Do the SQL query and populate items
        $get_posts       = new WP_Query(array_merge($query_args,$extra_args));
        $this->items = $get_posts->posts;
	}
}
