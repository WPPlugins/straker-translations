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
<div class="st-hr">
	<form method="post" name="language_settings" id="language_settings" action="<?php echo admin_url('admin-post.php'); ?>">
		<?php wp_nonce_field('straker-translations-language-settings', 'straker-translations-language-settings-nonce');?>
		<input type="hidden" name="action" value="straker_language_settings">
		<input type="hidden" id="sl" name="sl" value="">
		<p id="errr-msg" style="display:none;">
			<label class="st-error"><?php echo __('Please select your languages preferences.', $this->plugin_name); ?></label>
		</p>
		<div class="st-lang">
			<h2><?php esc_attr_e('Source Language', $this->plugin_name); ?></h2>
			<p><?php esc_attr_e('This is the original language of your site.', $this->plugin_name); ?></p>
			<select name="sl" id="langDropdown">
				<option value="" selected> <?php esc_attr_e('Select Source Language', $this->plugin_name); ?></option>
				<?php foreach ($this->straker_languages as $key => $value) { ?>
				<option value="<?php echo $value['code']; ?>" data-imagesrc="<?php echo $this->flags_path . $value['code'] . '.png'; ?>" data-description="<?php echo $value['name']; ?>"
					<?php if ($this->straker_default_language) {
			    	if ($value['code'] == $this->straker_default_language['code']) { echo 'selected'; }
					} ?>> <?php echo esc_html($value['native_name']); ?>
				</option>
			<?php } ?>
			</select>
		</div>
		<?php

		$added_langs  = $this->straker_added_language;
		if ( ! empty( $added_langs ) ) { ?>
		<div class="st-lang">
			<h2><?php esc_attr_e('Target Languages', $this->plugin_name); ?></h2>
			<p>
			<?php esc_attr_e('These are the languages your site will be translated into.', $this->plugin_name); ?>
			</p>
			<fieldset>
				<?php foreach ($this->straker_added_language as $value) { ?>
 			 	<item class="st-tlang">
					<input type="checkbox" name="tl[]" id="tl[]" value="<?php echo $value['code']; ?>" <?php  echo 'checked';?> />
					<label style="background: url(<?php echo $this->flags_path . $value['code'] . '.png'; ?>) left no-repeat;"><?php echo $value['name']; ?>
					<?php if ($value['native_name'] != $value['name']) { ?>
						<small class="dd-desc"><?php echo "- ".esc_html($value['native_name']); ?></small>
					<?php } ?>
					</label>
 				</item>
				<?php } ?>
			</fieldset>
		</div>
		<?php } ?>
		<div class="st-lang">
			<h2><?php esc_attr_e('Add new target languages:', $this->plugin_name); ?></h2>
			<?php if( $this->straker_default_language ) { ?>
				<fieldset>
				<?php foreach ($this->straker_target_language as $key => $value) { ?>
					<?php if( $value['code'] !== $this->straker_default_language['code'] ) { ?>
						<item>
							<input type="checkbox" name="tl[]" id="tl[]" value="<?php echo esc_attr($value['code']); ?>" />
							<label style="background: url(<?php echo $this->flags_path . $value['code'] . '.png'; ?>) left no-repeat;"><?php echo esc_html($value['name']); ?>
								<?php if ($value['native_name'] != $value['name']) { ?>
									<small class="dd-desc"><?php echo "- ".esc_html($value['native_name']); ?></small>
								<?php } ?>
							</label>
						</item>
					<?php } ?>
				<?php } ?>
				</fieldset>
			<?php } else { ?>
				<fieldset>
				<?php foreach ($this->straker_target_language as $key => $value) { ?>
						<item>
							<input type="checkbox" name="tl[]" id="tl[]" value="<?php echo esc_attr($value['code']); ?>" />
							<label style="background: url(<?php echo $this->flags_path . $value['code'] . '.png'; ?>) left no-repeat;"><?php echo esc_html($value['name']); ?>
								<?php if ($value['native_name'] != $value['name']) { ?>
									<small class="dd-desc"><?php echo "- ".esc_html($value['native_name']); ?></small>
								<?php } ?>
							</label>
						</item>
				<?php } ?>
				</fieldset>
			<?php } ?>
			<fieldset>
			<?php foreach ($this->straker_target_language as $key => $value) { ?>
				<?php if( $this->straker_default_language && $value['code'] !== $this->straker_default_language['code']) { ?>
					<item>
						<input type="checkbox" name="tl[]" id="tl[]" value="<?php echo esc_attr($value['code']); ?>" />
						<label style="background: url(<?php echo $this->flags_path . $value['code'] . '.png'; ?>) left no-repeat;"><?php echo esc_html( $value['name'] ); ?>
							<?php if ($value['native_name'] != $value['name']) { ?>
								<small class="dd-desc"><?php echo "- ".esc_html($value['native_name']); ?></small>
							<?php } ?>
						</label>
					</item>
				<?php } ?>
			<?php } ?>
			</fieldset>
		</div>
		<?php submit_button(__('Save Language Settings', $this->plugin_name), 'primary', 'submit', true); ?>
	</form>
</div>