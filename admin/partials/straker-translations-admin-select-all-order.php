<?php

/**
 * Provide a admin area view for the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 */

?>
<?php

    // This file contains the source, target languages and form code
	include_once 'straker-translations-admin-order-conf.php';

    if (isset( $_POST['st_trans_all'] ) ) {
        $tokens = array();
        $args =	array();
        $post_types		= $this->straker_posts_types;
        $post_status	= $this->straker_posts_status;
	    $selected_types	= isset( $_POST['post_types'] ) ? (array) $_POST['post_types'] : array();
		$selected_types	= array_map( 'esc_attr', $selected_types );
		$selected_status = isset( $_POST['post_status'] ) ? (array) $_POST['post_status'] : array();
		$selected_status = array_map( 'esc_attr', $selected_status );
        $args = array(
            'orderby'	=> 'post_title',
            'order' => 'ASC',
            'posts_per_page' => '-1',
            'post_status'	=> $selected_status,
            'post_type' => $selected_types,
            'meta_key' => Straker_Translations_Config::straker_meta_locale,
            'meta_value'	=> $this->straker_default_language['code']
		);
        $results = new WP_Query( $args );
        
        if( $results->have_posts() ) {

            ?>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label>
                                <?php esc_attr_e('Number of items', $this->plugin_name); ?>
                            </label>
                        </th>
                        <td>
                            <label class="st-total-items">
                                <?php echo $results->post_count; ?>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
                $post_stats = array();
                while ( $results->have_posts() ) {
                    
                    $results->the_post();
                    $tokens[] = get_the_ID();
                    
                    printf( '<input type="hidden" name="post_page[]" id="post_page-%d" value="%d" /> ', esc_html( get_the_ID() ),  esc_html( get_the_ID() ) );
                }
                wp_reset_postdata();
            ?>
            <table style=" border-bottom : 1px solid #000 !important; padding-bottom: 15px;">
            <?php
                foreach ( $selected_types as $key => $value ) {
                    $posts = get_posts(
                        array(
                            'post_type' => $value,
                            'posts_per_page' => '-1',
                            'post_status' => $selected_status,
                            'meta_query' => array(
                                array(
                                    'key' => Straker_Translations_Config::straker_meta_locale,
                                    'value'	=> $this->straker_default_language['code']
                                )
                            )
                        )
                    );
                    $post_obj_type	= get_post_type_object( $value );
                    $post_count = count( $posts );
                    $post_stats[]	=	$post_count;
                ?>
                <tr>
                    <?php 
                        printf( '<td class="st-order-stats"><label class="st-total-%s">%s</label></td>', $value, $post_count );
                        printf( '<td class="st-order-stats"><label>%s selected </label></td>', $post_obj_type->labels->name );
                    ?>
                </tr>
                <?php  } ?>
            </table>
            <?php
                printf( '<span class="st-order-stats"><label class="st-total-selected">%d</label> Total Selected </span>', array_sum( $post_stats ) );

                $testListTable = new Straker_Translation_Order_Page_List_Table_Ajax( $this->plugin_name );
                $testListTable->set_ids( $tokens );
                $testListTable->set_types( $selected_types );
                $testListTable->prepare_items();
                
                printf( '<input type="hidden" name="status_query_args" id="st_wp_posts_ids" value="%s" />', implode( ",", $tokens ) );
                printf( '<input type="hidden" name="types_query_args" id="st_wp_query_types" value="%s" />', implode( ",", $selected_types ) );
                printf( '<input type="hidden" name="page" value="%s" />', $_REQUEST['page'] );
                $testListTable->display();

            } else {
                wp_redirect( admin_url( 'admin.php?page=st-translation&msg=failed&ac=empty_translation' ) );
                exit();
            }

        } else {
            wp_redirect( admin_url( 'admin.php?page=st-translation&msg=failed&ac=empty_translation' ) );
            exit();
        }
        ?>
		<p class="submit">
			<a class="q-cancel-link button button-primary" href="<?php echo admin_url('admin.php?page=st-translation'); ?>"><?php echo __('Back', $this->plugin_name); ?></a>&nbsp;&nbsp;
			<?php if ( $results->have_posts() ) { ?>
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Get a Quote', $this->plugin_name); ?>" />
			<?php } ?>
		</p>
	</form>
</div>
