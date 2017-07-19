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
$default_lang = $this->straker_default_language;
$added_langs  = $this->straker_added_language;
if ( ! empty( $default_lang ) && ! empty( $added_langs ) ) { ?>
<?php $sMode = Straker_Translations_Config::straker_sandbox_mode(); ?>
<div class="st-hr">
<form method="post" name="general_settings" id="general_settings" action="<?php echo admin_url('admin-post.php'); ?>">
	<?php wp_nonce_field('straker-translations-general-settings', 'straker-translations-general-settings-nonce'); ?>
	<input type="hidden" name="action" value="straker_general_settings">
	<h2><?php esc_attr_e('Sandbox Settings', $this->plugin_name); ?></h2>
			<table class="form-table permalink-structure">
				<tbody>
					<tr>
						<th>
							<label>
								<input name="sandbox_mode" id="sandbox_mode_code" type="radio" value="true" <?php if ($sMode === 'true') { echo 'checked="checked"'; } ?>> <?php esc_attr_e('Sandbox (Testing)', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php esc_attr_e('Setting the plugin to Sandbox will allow you to test the workflow and features of the plugin without creating live jobs. Your content will be pseudo-translated (all text will be reversed) and not provided by a human. Please make sure you test the plugin using this mode.', $this->plugin_name); ?>
						</td>
					</tr>
					<tr>
						<th>
							<label>
								<input name="sandbox_mode" id="sandbox_mode_domain" type="radio" value="false" <?php if ($sMode === 'false') { echo 'checked="checked"'; } ?>> <?php esc_attr_e('Live', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php esc_attr_e('Setting the plugin to Live will allow you to create real live jobs. You will receive a quote which you will need to purchase, and your content will be translated by a human translator.', $this->plugin_name); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<?php if (Straker_Translations_Config::straker_sandbox_mode() === 'true') { ?>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save General Settings', $this->plugin_name); ?>" onclick="return confirm('<?php esc_attr_e('You can delete test jobs before changing the mode to Live. Please note, that Delete Test Jobs will also delete all pages and posts created by Sandbox jobs.', $this->plugin_name); ?>');">
				<?php } else { ?>
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save General Settings', $this->plugin_name); ?>">
				<?php } ?>
			</p>
		</form>
		<?php if (Straker_Translations_Config::straker_sandbox_mode() === 'true') { ?>
		<form method="post" name="test_delete" id="test_delete" action="<?php echo admin_url('admin-post.php'); ?>">
			<p class="submit">
				<input type="hidden" name="action" value="straker_test_delete">
				<?php wp_nonce_field('straker-translations-test-delete', 'straker-translations-test-delete-nonce'); ?>
				<input type="submit" name="submit" class="button button-primary" value="Delete Test Jobs" onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete all test jobs?', $this->plugin_name); ?>');">
			</p>
			<p class="description">
				<?php esc_attr_e('Delete Test Jobs will also delete all pages and posts created by Sandbox jobs.', $this->plugin_name); ?>
			</p>
		</form>
		<?php
	}
} else {
    wp_redirect(admin_url('admin.php?page=st-settings&tab=language_settings&ac=lang_setting&msg=failed'));
    exit();
} ?>
