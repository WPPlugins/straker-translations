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
	<p>
		<?php echo __( 'Use this page to manage original and source language of translated pages. Find the source pages of translated content.', $this->plugin_name ); ?>
	</p>
	<?php
			$default_lang = $this->straker_default_language;
			$added_langs  = $this->straker_added_language;
			$rewrite_opt	= Straker_Translations_Config::straker_rewrite_type();
      if ( empty( $default_lang ) && empty( $added_langs )) {
        wp_redirect(admin_url('admin.php?page=st-settings&tab=language_settings&ac=lang_setting&msg=failed'));
        exit();
      } elseif ( empty( $rewrite_opt ) ) {
				wp_redirect(admin_url('admin.php?page=st-settings&tab=url_settings&ac=url_setting&msg=failed'));
				exit();
			} else
			{
				?>
				<p id="errr-msg" style="display:none;">
					<label class="st-error"></label>
				</p>
				<?php
				$list_table = new List_Table_Language_Management($this->plugin_name);
	      $list_table->prepare_items();
			  $list_table->display();
      }
   ?>
</div>
