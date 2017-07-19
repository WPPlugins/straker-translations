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
	<?php
		$default_lang = $this->straker_default_language;
		$added_langs  = $this->straker_added_language;
		$rewrite_opt	= Straker_Translations_Config::straker_rewrite_type();
		if ( empty( $default_lang ) && empty( $added_langs ) ) {
			wp_redirect(admin_url('admin.php?page=st-settings&tab=language_settings&ac=lang_setting&msg=failed'));
			exit();
		} elseif ( empty( $rewrite_opt) ) {
			wp_redirect(admin_url('admin.php?page=st-settings&tab=url_settings&ac=url_setting&msg=failed'));
			exit();
		} else
		{
	?>
	<form method="post" name="shortcode_settings" id="st_shortcode_settings" action="<?php echo admin_url('admin-post.php'); ?>">
		<?php wp_nonce_field('straker-translations-shortcode-settings', 'straker-translations-shortcode-settings-nonce');?>
		<input type="hidden" name="action" value="straker_shortcode_settings">
		<input type="hidden" id="sl" name="sl" value="">
		<?php if ( ! empty( $added_langs ) )
		{
			$shortcode_option = get_option(Straker_Translations_Config::straker_option_shortcode);
			if(empty($shortcode_option)){
				$shortcode_option = "";
			}
			$avaialable_langs = isset($shortcode_option["available_langs"]) && !empty($shortcode_option["available_langs"])?$shortcode_option["available_langs"]:"";
			$display_flags 		= isset($shortcode_option["display_flags"])?$shortcode_option["display_flags"]:"";
			$display_langs 		= isset($shortcode_option["display_langs"])?$shortcode_option["display_langs"]:"";
			$display_horiz 		= isset($shortcode_option["display_horiz"])?$shortcode_option["display_horiz"]:"";
			?>
		<div class="st-shortcode-set">
			<p><?php esc_attr_e("To create a language switcher short code, select the relevant information below, click 'Generate Shortcode' and enter the code on the page(s) required.", $this->plugin_name); ?> <br /><br />
			<?php if( !empty($shortcode_option)){	?>
				<code id="st-shortcode">[straker_translations languages="<?php
						foreach($avaialable_langs as $alng)
						{
							if(end($avaialable_langs) !== $alng)
							{
								echo $alng.",";
							}else
							{ echo $alng;
							}
						}?>"
						<?php
						if($display_flags =='on' )
						{
							echo 'display_flag="'.$display_flags.'"';
						}
						if($display_langs =='on')
						{
							echo ' display_language="'.$display_langs.'"';
						}
						if($display_horiz =='on')
						{
							echo ' horizontal="'.$display_horiz.'"';
						} ?>]</code>&nbsp;&nbsp;<a href="#" style="box-shadow: none !important; text-decoration: none;" class="st-cb-cp" data-clipboard-action="copy" data-clipboard-target="#st-shortcode" st-data-tooltip title="Copy to Clipboard"><span class="dashicons dashicons-clipboard"></span></a>
						&nbsp;&nbsp;&nbsp;<span id="st-copied" style="display:none;"><?php echo __('Copied!', $this->plugin_name ); ?></span>
			<?php } ?>
			</p>
			<h2><?php esc_attr_e('Available languages', $this->plugin_name); ?></h2>
			<p id="tagline-description" class="description"></p><br />
			<input type="checkbox" name="tl[]" id="tl[]" value="<?php if(empty($avaialable_langs)){echo $this->straker_default_language['code'];}else{echo $this->straker_default_language['code'];} ?>" <?php if(!empty($avaialable_langs)){ if(in_array($this->straker_default_language['code'], $avaialable_langs)){ echo 'checked'; }}else{echo 'checked';} ?>/>
			<label class="st-label" style="background: url('<?php echo $this->flags_path . $this->straker_default_language['code']; ?>.png') left no-repeat;padding-left: 35px;">
				<?php echo esc_html($this->straker_default_language['name']); ?>
				<?php if ($this->straker_default_language['name'] != $this->straker_default_language['native_name']) { ?>
				<small class="dd-desc"><?php echo " - ".esc_html($this->straker_default_language['native_name']); ?>
				<?php } ?>
			</label><br /><br />
				<?php foreach ($this->straker_added_language as $value) {?>
					<input type="checkbox" name="tl[]" id="tl[]" value="<?php echo $value['code']; ?>" <?php

					if(empty($shortcode_option))
					{
						echo 'checked';
					}

					if($avaialable_langs)
					{
						foreach($avaialable_langs as $dis_langs)
						{
							if($dis_langs == $value['code'])
							{
								echo 'checked';
							}
						}
					}
					?> />
					<label class="st-label" style="background: url(<?php echo $this->flags_path . $value['code'].'.png'; ?>) left no-repeat; padding-left: 35px;"><?php echo $value['name']; ?>
						<?php if ($value['native_name'] != $value['name']) { ?>
										<small class="dd-desc"><?php echo "- ".esc_html($value['native_name']); ?></small>
						 		<?php	} ?>
					</label>
					<br /><br />
				<?php } ?>
		</div>
		<div class="st-shortcode-set">
			<h2><?php esc_attr_e('General Settings', $this->plugin_name); ?></h2>
			<input type="checkbox" name="display_flags" value="on" <?php if(isset($display_flags) && $display_flags == "on"){echo 'checked';} ?> class="display_flag_lang" />
			<label><?php esc_attr_e('Display Flag', $this->plugin_name); ?></label>
			<span class="gray-note st-shortcode-note"><?php esc_attr_e("Show the flag as an icon.", $this->plugin_name); ?></span><br / >
			<input type="checkbox" name="display_langs" value="on" <?php if(isset($display_langs) && $display_langs == "on"){echo 'checked';} ?>  class="display_flag_lang" />
			<label><?php esc_attr_e('Display Language', $this->plugin_name); ?></label>
			<span class="gray-note st-shortcode-note"><?php esc_attr_e("Show the language name written out.", $this->plugin_name); ?></span><br / >
			<input type="checkbox" name="display_horizontal" value="on" <?php if(isset($display_horiz) && $display_horiz == "on"){echo 'checked';} ?> />
			<label><?php esc_attr_e('Display Horizontal', $this->plugin_name); ?></label>
			<span class="gray-note st-shortcode-note"><?php esc_attr_e("Show the options horizontally (not in a list).", $this->plugin_name); ?></span>
		</div>
		<?php } ?>
		<?php submit_button(__('Generate Shortcode', $this->plugin_name), 'primary', 'submit', true); ?>
	</form>
	<?php } ?>
</div>
