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
<div class="wrap">
	<?php include_once 'messages/sandbox-message.php'; ?>
	<h1><?php esc_attr_e('Re Import Content', $this->plugin_name); ?></h1>
	<div id="st-msg" class="below-h2"></div>
	<div class=st-hr>
		<?php
			if (!empty($_GET['jk'])) {
				$job_key = sanitize_text_field($_GET['jk']);
      ?>
			<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" id="re_import_content" name="re_import_content" >
				<p class="search-box">
					<input type="hidden" id="target_post_id" name="target_post_id" value="" />
				</p>
				<p class="st-p-font">
					<?php esc_attr_e('Import Content Manually', $this->plugin_name); ?>
				</p>
				<table class="wp-list-table widefat fixed striped post_pages">
					<thead>
						<tr>
							<th class="col" id="post_status" scope="col"><?php esc_attr_e('Title', $this->plugin_name); ?></th>
							<th class="col" id="meta_value" scope="col"><?php esc_attr_e('Type', $this->plugin_name); ?></th>
							<th class="col" id="content" scope="col"><?php esc_attr_e('Content', $this->plugin_name) ?></th>
							<th class="col" id="content" scope="col"><?php esc_attr_e('Upload XML', $this->plugin_name) ?></th>
						</tr>
					</thead>
					<tbody>
					<?php $new_posts = $this->straker_content_query($this->straker_imported_links($job_key));
          	if ($new_posts->have_posts()) {
            	while ($new_posts->have_posts()) {
              	$new_posts->the_post(); ?>
					      	<tr class="type-post format-standard">
										<td class="title column-title page-title">
											<?php $lang_code = get_post_meta( get_the_ID(), Straker_Translations_Config::straker_meta_locale ); $lang_meta_name = Straker_Language::straker_language_meta('code', $lang_code[0]);?>
											<img style="vertical-align:middle" src='<?php echo $this->flags_path . $lang_code[0]; ?>.png'>&nbsp;&nbsp;<?php echo $lang_meta_name["name"];?><br />
		 									<a href="<?php echo esc_url(get_edit_post_link()); ?>" target="_blank"><?php the_title(); ?></a>
		 								</td>
					      		<td><?php echo esc_html(ucfirst(get_post_type())); ?></td>
					      		<td><?php echo esc_html(mb_substr(get_the_excerpt(),0,25))."..."; ?></td>
					      		<td>
											<input type="file" name="resx-uploader" id="<?php echo esc_attr(get_the_ID()); ?>" accept="text/xml" class="resx-uploader" />
										</td>
					      	</tr>
							<?php 
							} 
							wp_reset_postdata(); 
						} ?>
					 </tbody>
				</table>
				<br />
				<p class="st-p-font">
					<?php esc_attr_e('Import Content From API', $this->plugin_name); ?>
				</p>
				<form action="<?php echo admin_url('admin-post.php'); ?>" name="straker_import_translation_form" id="straker_import_translation_form" method="post">
					<input type="hidden" name="action" value="straker_import_translation">
					<input type="hidden" name="jk" value="<?php echo esc_attr($job_key); ?>">
					<input type="hidden" name="re_import" value="true">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Re Import All Content From API', $this->plugin_name); ?>" onclick="return confirm('<?php esc_attr_e('Are you sure you want to re-import this job?  It will overwrite existing pages and posts with new content.', $this->plugin_name); ?>');">
				</form>
				<p class="submit">
					<a class="q-cancel-link button button-primary" href="<?php echo admin_url('admin.php?page=st-jobs&jk='.$job_key.'&pr=ready'); ?>"><?php echo __('Back', $this->plugin_name); ?></a>&nbsp;&nbsp;
				</p>
			</form>
			<?php } ?>
	</div>
</div>
