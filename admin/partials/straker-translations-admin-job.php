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
	<?php include_once 'messages/sandbox-message.php'; ?>
	<h1><?php esc_attr_e('Job Details', $this->plugin_name); ?></h1>
	<?php include_once 'messages/general-message.php'; ?>
	<?php
    if (!empty($_GET['jk'])) {
			$job_key = sanitize_text_field($_GET['jk']);
      foreach ($this->straker_get_jobs($job_key) as $key => $value) {
        foreach ($value as $data) {
					$source_langguage = Straker_Language::straker_language_meta( 'code', $data['sl'] );
					?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label>
								<?php esc_attr_e('Job Reference', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php echo $data['tj_number']; ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								<?php esc_attr_e('Source Language', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<div class="st-lang">
								<fieldset>
									<item>
										<?php ?>
										<label style="background: url('<?php echo $this->flags_path . $source_langguage['code']; ?>.png') left no-repeat;">
											<?php  echo $source_langguage['name']; ?>
											<?php if ($source_langguage['name'] != $source_langguage['native_name']) { ?>
											<small class="dd-desc"><?php echo " - ".$source_langguage['native_name']; ?>
											<?php } ?>
										</label>
									</item>
								</fieldset>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label><?php esc_attr_e('Target Language', $this->plugin_name); ?>
						</th>
						<td>
							<div class="st-lang">
								<fieldset>
								<?php
                $langs = str_getcsv($data['tl']);
                foreach ($langs as $lang) {
                    $lang_meta = Straker_Language::straker_language_meta('code', $lang);
                    echo '<item>';
                    echo '<label style="background: url('.$this->flags_path . $lang.'.png) left no-repeat;">'.$lang_meta['name'].' <small class="dd-desc"> - '.$lang_meta['native_name'].'</small></label>';
                    echo '</item>';
                }	?>
								</fieldset>
							</div>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								<?php esc_attr_e('Date Quoted', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php echo $data['created_at']; ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label>
								<?php esc_attr_e('Job Status', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php echo $this->straker_api_status($data['status'], $data['quotation']); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
				if ($data['status'] == 'COMPLETED') { ?>
					<p class="st-p-font">
						<?php esc_attr_e('Download Translation', $this->plugin_name); ?>
					</p>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<th class="" scope="col"><?php esc_attr_e('Target Language', $this->plugin_name);?></th>
							<th class="" scope="col"><?php esc_attr_e('XML File', $this->plugin_name);?></th>
						</thead>
						<tbody>
								<?php
									$trans_data =  get_option(Straker_Translations_Config::straker_option_translation_resx.$data['tj_number']);
									if(!$trans_data){
										$trans_data = $this->straker_download_resx($data['translated_file'],$data['tj_number'], $data['job_key'],$this->straker_default_language['name']);
									}
								?>
								<tr>
									<td>
										<?php
											$langs = str_getcsv($data['tl']);
											foreach ($langs as $lang) {
												$lang_meta = Straker_Language::straker_language_meta('code', $lang);
						        		echo '<a st-data-tooltip title="'.str_replace('_', ' ', $lang).'" href="#" ><img src="' . $this->flags_path . $lang . '.png" style="vertical-align:middle"></a>&nbsp;&nbsp'.$lang_meta['name'].'<br />';
											}
										?>
									</td>
									<td>
										<?php foreach ($trans_data["xml_files"] as $tKey => $tVal) { ?>
										<a class="" href="<?php echo wp_get_attachment_url( $tVal );; ?>" download="<?php basename( get_attached_file( $tVal ) ); ?>"><?php echo basename( get_attached_file( $tVal ) ); ?></a><br />
										<?php } ?>
									</td>
									<!--
									<td>
											<a class="" href="<?php //echo wp_get_attachment_url( $trans_data["csv_file"]); ?>" download="<?php //basename( get_attached_file( $trans_data["csv_file"] ) ); ?>"><?php //echo basename( get_attached_file( $trans_data["csv_file"] ) ); ?></a><br />
									</td> -->
								</tr>
						</tbody>
					</table>
			<?php } ?>
			<?php
			$results = $this->straker_content_query($this->straker_job_links($data['job_key']));
			if ($results->have_posts()) { ?>
			<table class="form-table">
				<tbody>
						<th scope="row">
							<label>
								<?php esc_attr_e('Number of items', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<?php
								$count = $results->post_count;
							  echo $count;
							?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php } ?>
		<?php	if ( ! isset( $_GET['pr'] ) ) { ?>
			<table class="wp-list-table widefat fixed striped posts">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-comments">&nbsp;</th>
						<th scope="col" id="title" class="column-title"><?php esc_attr_e('Title', $this->plugin_name) ?></th>
						<th scope="col" id="content" class="manage-column"><?php esc_attr_e('Type', $this->plugin_name) ?></th>
						<th scope="col" id="content" class="manage-column"><?php esc_attr_e('Content', $this->plugin_name) ?></th>
						<th scope="col" id="categories" class="manage-column"><?php esc_attr_e('Date Published', $this->plugin_name) ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
				<?php
				$count = 0;
				while ($results->have_posts()) {
					$count++;
					$results->the_post(); ?>
						 <tr class="type-post format-standard">
							<td>
								<?php echo $count; ?>
							</td>
							<td class="title column-title page-title">
								<a href="<?php echo get_edit_post_link(); ?>" target="_blank"><?php the_title(); ?></a>
							</td>
							<td>
								<?php echo ucfirst(get_post_type(get_the_ID())); ?>
							</td>
							<td>
								<?php echo mb_substr(get_the_excerpt(),0,70)."..."; ?>
							</td>
							<td>
								<?php echo get_the_date(); ?>
							</td>
						</tr>
					<?php } wp_reset_postdata(); ?>
				</tbody>
			</table>
			<br />
		<?php } ?>
		<?php	if (isset($_GET['pr'])) { ?>
		<p class="st-p-font">
			<?php esc_attr_e( 'Translated Content', $this->plugin_name ); ?>
		</p>
		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<th scope="col" id="title" class="column-title" width=""><?php esc_attr_e('Target Title', $this->plugin_name) ?></th>
					<th scope="col" id="title" class="column-title" width=""><?php esc_attr_e('Source Title', $this->plugin_name) ?></th>
					<th scope="col" id="content" class="manage-column" width="107px"><?php esc_attr_e('Type', $this->plugin_name) ?></th>
					<th scope="col" id="content" class="manage-column" width="665px"><?php esc_attr_e('Content', $this->plugin_name) ?></th>
					<th scope="col" id="content" class="manage-column" width=""><?php esc_attr_e('Date Source Published', $this->plugin_name) ?></th>
				</tr>
			</thead>
			<tbody id="the-list">
				<tbody>
					<?php
          $target_posts = $this->straker_content_query($this->straker_imported_links($data['job_key']));
			    if ($target_posts->have_posts()) {
			      while ( $target_posts->have_posts() ) { $target_posts->the_post();
							$lang_code = get_post_meta( get_the_ID(), Straker_Translations_Config::straker_meta_locale );
							$lang_meta_name = Straker_Language::straker_language_meta( 'code', $lang_code[0] );
							$source_post_id = get_post_meta( get_the_ID(), 'straker_default_'.$lang_meta_name['wp_locale'], true );
							?>
 							<tr class="type-post format-standard">
								<td class="title column-title page-title">
									<?php
									 ?>
									<img style="vertical-align:middle" st-data-tooltip title="<?php echo str_replace('_', ' ', $lang_code[0]) ?>" src='<?php echo $this->flags_path . $lang_code[0]; ?>.png'>&nbsp;&nbsp;<?php echo $lang_meta_name["name"]; ?><br  />
 									<a href="<?php echo get_edit_post_link(); ?>" target="_blank"><?php the_title(); ?></a>
 								</td>
								<td class="title column-title page-title">
									<a href="<?php echo get_edit_post_link( $source_post_id ); ?>" target="_blank"><?php echo get_the_title( $source_post_id ); ?></a>
								</td>
 								<td>
 									<?php echo ucfirst( get_post_type( get_the_ID() ) ); ?>
 								</td>
 								<td>
 									<?php echo mb_substr( get_the_excerpt(), 0, 65 )."..."; ?>
 								</td>
								<td>
									<?php echo get_the_date( '', $source_post_id ); ?>
								</td>
 							</tr>
					<?php } wp_reset_postdata();
    			} ?>
			</tbody>
		</table>
		<br />
		<?php } ?>
		<p>
			<a class="q-cancel-link button button-primary" href="<?php echo admin_url('admin.php?page=st-jobs'); ?>"><?php echo __('Back', $this->plugin_name); ?></a>
			<?php	if ($data['status'] == 'COMPLETED') {
				if ( $this->straker_imported_links( $job_key ) ) { ?>
				<a href="<?php echo admin_url('admin.php?page=st-reimport&jk='.$job_key); ?>" class="button button-primary"><?php esc_attr_e('Import', $this->plugin_name); ?></a>
				<?php } ?>
	 		<?php } ?>
			<?php if ($data['status'] == 'QUEUED') { ?>
	      <form action="<?php echo admin_url('admin-post.php'); ?>" name="straker_cancel_form" id="straker_cancel_form" method="post">
	        <?php wp_nonce_field('straker-translations-cancel-job', 'straker-translations-cancel-job-nonce'); ?>
	        <input type="hidden" name="action" value="straker_cancel_job">
	        <input type="hidden" name="tj" value="<?php echo $data['tj_number']; ?>">
	        <input type="hidden" name="jk" value="<?php echo $job_key; ?>">
	        <input type="submit" name="submit" id="submit" class="button " value="Cancel Job" onclick="return confirm('<?php esc_attr_e('Are you sure you want to cancel this job?', $this->plugin_name); ?>');">
	      </form>
			<?php } ?>
		</p>
	<?php }
  }
} ?>
