<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Language_Translation
 * @subpackage Straker_Language_Translation/admin/partials
 */
?>
<?php
	if ( isset( $_GET['msg'] ) and ! empty( $_GET['msg'] ) and isset( $_GET['ac'] ) and ! empty( $_GET['ac'] ) ) {

		$ac_msg = sanitize_text_field( $_GET['ac'] );

		if ( $_GET['msg'] == 'success') {
				$job = '';//.(isset($_GET['jobid']) ? $_GET['jobid'] : '');
			 ?>
			<div id="message" class="updated notice is-dismissible">
				<p><?php _e( $this->straker_message( $ac_msg, $job ), $this->plugin_name ); ?></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_attr_e( 'Dismiss this notice.', $this->plugin_name ); ?></span></button>
			</div>
		<?php
		} elseif ( $_GET['msg'] == 'failed') {
			if( ! empty ( $ac_msg ) ) { ?>
				<div class='error'>
					<p>
						<?php _e( $this->straker_message( $ac_msg ), $this->plugin_name ); ?>
					</p>
				</div>
		<?php	} else { ?>
			<div class='error'>
				<p><?php esc_attr_e( Straker_Translations_Config::straker_support_message, $this->plugin_name ); ?></p>
			</div>
		<?php
			}
		}
	}
?>
