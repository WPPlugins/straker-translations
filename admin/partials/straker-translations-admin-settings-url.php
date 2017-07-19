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

if( ! empty( $default_lang ) && ! empty( $added_langs ) ) { ?>
	<?php $sRewrite = Straker_Translations_Config::straker_rewrite_type(); ?>
	<div class="st-hr">
		<form method="post" name="url_settings" id="url_settings" action="<?php echo admin_url('admin-post.php'); ?>">
			<?php wp_nonce_field('straker-translations-url-settings', 'straker-translations-url-settings-nonce');?>
			<input type="hidden" name="action" value="straker_url_settings">
			<h2><?php esc_attr_e('Select a URL setting', $this->plugin_name); ?></h2>
			<p><?php esc_attr_e('This is how your translated website URLs will be displayed.', $this->plugin_name); ?></p>
			<p id="errr-msg" style="display:none;">
				<label class="st-error"><?php echo __('Please select a url settings.', $this->plugin_name); ?></label>
			</p>
			<table class="form-table permalink-structure">
				<tbody>
					<tr>
						<th>
							<label>
								<input name="rewrite_type" id="rewrite_type_code" type="radio" value="code" <?php if ($sRewrite === 'code') { echo 'checked="checked"'; } ?> required > <?php esc_attr_e('Language code', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php esc_attr_e('Custom structure using language code in the URL.', $this->plugin_name); ?><br />
							<span class="gray-note"><?php esc_attr_e('Example: '.home_url().'/zh-cn', $this->plugin_name); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label>
								<input name="rewrite_type" id="rewrite_type_domain" type="radio" value="domain" <?php if ($sRewrite === 'domain') { echo 'checked="checked"'; } ?>> <?php esc_attr_e('Domain name', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php esc_attr_e('Please provide the url you require.', $this->plugin_name); ?>
							<span class="gray-note"><?php esc_attr_e('Selecting Domain name requires a change to wp-config.php file and DNS settings.', $this->plugin_name); ?></span>
						</td>
					</tr>
					<tr>
						<th>
							<label>
								<input name="rewrite_type" id="rewrite_type_none" type="radio" value="none" <?php if ($sRewrite === 'none') { echo 'checked="checked"'; } ?>> <?php esc_attr_e('Files only', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php esc_attr_e('No additional URLs created for translated content.', $this->plugin_name); ?>
							<span class="gray-note"><?php esc_attr_e('Best for those who only need the translation provided as a file without pages automatically created. This is useful for clients using WPML or similar plugins.', $this->plugin_name); ?></span>
						</td>
					</tr>
				</tbody>
			</table>

			<h2><?php esc_attr_e('URLs for Domain name setting', $this->plugin_name); ?></h2>
			<div class="st-lang">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label class="st-label" style="background: url(<?php echo $this->flags_path . $this->straker_default_language['code'].'.png'; ?>) left no-repeat;">
									<?php echo esc_html($this->straker_default_language['name']); ?>
									<span class="dd-desc st-slng"><?php esc_attr_e('(Source language)', $this->plugin_name); ?></span>
								</label>
							</th>
							<td>
								<code><?php echo esc_url(get_site_url()); ?></code>
							</td>
						</tr>
						<?php
						switch ($sRewrite) {
							case 'code':
								$url_view = 'hidden';
								$structure_view = '';
								break;
							case 'domain':
								$url_view = '';
								$structure_view = 'hidden';
								break;
							case 'none':
								$url_view = 'hidden';
								$structure_view = 'hidden';
								break;
							default:
								$url_view = 'hidden';
								$structure_view = 'hidden';
								break;
						}
						?>
						<?php	foreach ($this->straker_added_language as $value) { ?>
						<tr>
							<th scope="row">
								<label class="st-label" style="background: url(<?php echo $this->flags_path . $value['code'].'.png'; ?>) left no-repeat;">
									<?php echo esc_html($value['name']); ?>
								</label>
							</th>
							<td>
								<code class="st-url-structure" <?php echo $structure_view; ?>><?php echo $this->straker_url_structure($value['short_code']); ?></code>
								<input type="hidden" name="lang[]" value="<?php echo $value['code']; ?>">
								<input type="text" name="url[]" id="url" class="regular-text st-tl-lang st-text-field" value="<?php echo esc_url($this->straker_urls[$value['code']]); ?>" <?php echo $url_view; ?>>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		<?php submit_button(__('Save URL Settings', $this->plugin_name), 'primary', 'submit', true); ?>
	</form>
<?php } else { wp_redirect( admin_url( 'admin.php?page=st-settings&tab=language_settings&ac=lang_setting&msg=failed' ) ); } ?>
