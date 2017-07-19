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
	$rewrite_opt	= Straker_Translations_Config::straker_rewrite_type();
	if ( Straker_Translations_Config::straker_sandbox_mode() === 'true' && $this->straker_auth !== false && $this->straker_added_language && ! empty( $rewrite_opt ) ) { ?>
		<div class="update-nag notice">
			<p>
				<strong><?php _e( $this->straker_message('test_mode'), $this->plugin_name ); ?></strong>
			</p>
			<p>
				<?php _e( $this->straker_message('test_text'), $this->plugin_name ); ?>&nbsp;
				<a href="<?php echo esc_url(admin_url( 'admin.php?page=st-settings#st-sandbox-settings-box'));?>"><?php _e( ' Settings.', $this->plugin_name ); ?></a>
			</p>
		</div>
	<?php } ?>
