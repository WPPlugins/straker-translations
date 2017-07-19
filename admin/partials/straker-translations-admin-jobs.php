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
<div class="wrap">
	<?php
	if (isset($_GET['jk'])) {
		if ( !empty($_GET['jk']) && !empty($_GET['v']) == 'quote') {
			include_once 'straker-translations-admin-quote.php';
		} elseif ( ! empty($_GET['jk']) ) {
			include_once 'straker-translations-admin-job.php';
		}
	} else {
	?>
	<?php include_once 'messages/sandbox-message.php'; ?>
	<h1><?php esc_attr_e( 'My Jobs', $this->plugin_name ); ?></h1>
	<?php
	if($this->straker_auth === false)  {

		include_once 'straker-translations-admin-register-button.php';

	} else { ?>
		<div class=st-hr>
			<?php
				$api_sig	  = $this->straker_api_signature();
				$my_link	  = $this->straker_api('myaccount/token').'?api_sig='.$api_sig.'&p=job.list';
				$default_lang = $this->straker_default_language;
				$added_langs  = $this->straker_added_language;
				$rewrite_opt  = Straker_Translations_Config::straker_rewrite_type();
				$straker_api_jobs =  $this->straker_return_api_jobs();
			?>
			<div class="st-set-btns">
				<br />
				<p class="ma-btn mj-btn">
					<a class="button button-primary" st-data-tooltip title="<?php echo esc_attr_e('Click here to see your extended profile, active jobs and billing information.', $this->plugin_name);?>" href="<?php echo esc_url($my_link); ?>" target="_blank"><?php esc_attr_e( 'My Account', $this->plugin_name ); ?>&nbsp;<span class="dashicons dashicons-migrate pt-3"></span></a>
				</p>
				<?php
				$send_content_url = '';
				$error_msg_url = '';
				if ( empty( $default_lang ) && empty( $added_langs ) )
				{
					$send_content_url = true;
					$error_msg_url = 'lang';

				} elseif ( ! empty( $default_lang ) && ! empty( $added_langs ) && $rewrite_opt == '' )
				{
					$send_content_url = true;
					$error_msg_url = 'url';
				} else
				{
					$send_content_url = false;
				}

					if( $send_content_url && ! empty( $send_content_url ) ){ ?>
						<p class="mj-btn">
							<a class="button button-primary" id="st-lang-url-err-msg" href="#" st-data-tooltip title="<?php echo esc_attr_e('Click here to get a quote for pages, posts and other editable web content.', $this->plugin_name);?>"><?php esc_attr_e( 'Send Web Content', $this->plugin_name ); ?></a>
						</p>
				<?php }else{ ?>
				<p class="mj-btn">
					<a class="button button-primary" href="<?php echo admin_url('admin.php?page=st-translation'); ?>" st-data-tooltip title="<?php echo esc_attr_e('Click here to get a quote for pages, posts and other editable web content.', $this->plugin_name);?>"><?php esc_attr_e( 'Send Web Content', $this->plugin_name ); ?></a>
				</p>
				<?php } ?>
				<p class="mj-btn cen-btn">
					<a class="button button-primary" href="<?php echo admin_url('admin.php?page=st-tm'); ?>" st-data-tooltip title="<?php echo esc_attr_e('Click here to get a quote for images, .po files, or content in other file formats.', $this->plugin_name);?>"><?php esc_attr_e( 'Send Files/Assets', $this->plugin_name ); ?></a>
				</p>
				<div class="lang-error">
					<p class="description">
						<?php echo esc_attr_e( 'Setup has not been completed, please ', $this->plugin_name ); ?>
						<a href="<?php if ( $error_msg_url == 'lang' ) { echo admin_url('admin.php?page=st-settings&tab=language_settings&ac=lang_setting&msg=failed'); } elseif ( $error_msg_url == 'url' ) { echo admin_url('admin.php?page=st-settings&tab=url_settings&ac=url_setting&msg=failed'); }  ?>">
							<?php echo esc_attr_e( 'click here', $this->plugin_name ); ?>
						</a>
						<?php echo esc_attr_e( ' to select your languages and url preferences.', $this->plugin_name ); ?>
					</p>
				</div>
			</div>
	</div>
		<?php
				$completed_jobs = array();
				if( is_array( $straker_api_jobs ) ) {
					foreach ( $straker_api_jobs as $key => $value) {
						if ( is_array( $value ) ) {
							foreach ($value as $data) {
								if(!$this->straker_imported_links($data["job_key"]) && $data["status"] === 'COMPLETED')
								{
									$completed_jobs[$data['job_key']] = $data['job_key'];
								}
							}
						}
					}
				}
			?>
			<div class=st-hr>
				<p><h3><?php esc_attr_e('List Jobs:', $this->plugin_name);?></h3></p>
				<?php	
					include_once 'messages/general-message.php';			
					$api_jobs_status = $this->straker_get_total_jobs(); 
				?>
				<div class="st-tabs">
				<ul class="st-tab-links">
					<li class="<?php if(!isset($api_jobs_status['completed'])){echo 'st-active';} ?>"><a href="#tab1" <?php if(isset($api_jobs_status['awaiting_quote'])){?>	class="blue" st-jobs-noti-bubble="<?php echo count($api_jobs_status['awaiting_quote']); ?>" <?php } ?>><?php echo esc_attr_e('Quote Requested', $this->plugin_name);?></a></li>
					<li><a href="#tab2" <?php if(isset($api_jobs_status['ready'])){?>	class="blue" st-jobs-noti-bubble="<?php echo count($api_jobs_status['ready']); ?>" <?php } ?>><?php esc_attr_e('Order Now', $this->plugin_name);?></a></li>
					<li><a href="#tab3" <?php if(isset($api_jobs_status['in_progress'])){?>	class="blue" st-jobs-noti-bubble="<?php echo count($api_jobs_status['in_progress']); ?>" <?php } ?>><?php esc_attr_e('In Progress', $this->plugin_name);?></a></li>
					<li class="<?php if(isset($api_jobs_status['completed'])){echo 'st-active'; }?>"><a href="#tab4" <?php if(!empty($completed_jobs)){?>	class="blue" st-jobs-noti-bubble="<?php echo count($completed_jobs); ?>" <?php } ?>><?php esc_attr_e('Completed', $this->plugin_name);?></a></li>
				</ul>
				<a href="#" class="st-refresh-a" onclick="location.reload(true); return false;">
					<img width="32" height="32" src="<?php echo plugins_url( 'img/st-refresh-button-icon.png', dirname(__FILE__) ); ?>" />
				</a>
				<div class="st-tab-content">
					<div id="tab1" class="st-tab <?php if(!isset($api_jobs_status['completed'])){echo 'st-active';} ?>">
						<?php if( isset( $api_jobs_status['awaiting_quote'] ) && is_array( $straker_api_jobs ) ){ ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<th class="" scope="col"><?php esc_attr_e('Job Reference', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Source Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Target Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Number of items', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Status', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Date Submitted', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e(' ', $this->plugin_name);?></th>
							</thead>
							<tbody>
								<?php
								foreach ( $straker_api_jobs as $key => $value ) {
									foreach ($value as $data) {
										if($data["status"] === 'QUEUED' && $data['quotation'] === ''){ ?>
										<tr>
											<td class=""><a href="admin.php?page=st-jobs&jk=<?php echo $data['job_key'] ?>" ><?php echo esc_html($data['tj_number']); ?></a></td>
											<td class="">
												<?php
												$lang_meta = Straker_Language::straker_language_meta('code', $data['sl']);
												echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. $lang_meta['name'] . '</label>';
												?>
											</td>
											<td>
												<?php
												$langs = str_getcsv($data['tl']);
												foreach ($langs as $lang) {
													$lang_meta = Straker_Language::straker_language_meta('code', $lang);
													echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. esc_html($lang_meta['name']) . '</label><br><br>';
												}
												?>
											</td>
											<td style="text-align:center;">
												<?php
												$post_ids = explode(",",$data['token']);
												echo count($post_ids);
												?>
											</td>
											<td class=""><?php echo $this->straker_api_status($data['status'], $data['quotation']); ?></td>
											<td class=""><?php echo $data['created_at']; ?></td>
											<td class=""><?php echo $this->straker_api_action($data['status'], $data['quotation'], $data['job_key']); ?></td>
										</tr>
								<?php
										}
									}
								}
								?>
							</tbody>
						</table>
						<?php }else{
							echo esc_attr_e('No quotes requested', $this->plugin_name);
						}?>
					</div>
					<div id="tab2" class="st-tab">
					<?php if( isset( $api_jobs_status['ready'] ) && is_array( $straker_api_jobs ) ){ ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<th class="" scope="col"><?php esc_attr_e('Job Reference', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Source Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Target Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Page / Post Title', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Status', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Date Submitted', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e(' ', $this->plugin_name);?></th>
							</thead>
							<tbody>
								<?php
								foreach ( $straker_api_jobs as $key => $value) {
									foreach ($value as $data) {
									if($data["status"] === 'QUEUED' && $data['quotation'] === 'READY'){ ?>
										<tr>
											<td class=""><a href="admin.php?page=st-jobs&jk=<?php echo $data['job_key'] ?>" ><?php echo esc_html($data['tj_number']); ?></a></td>
											<td class="">
												<?php
												$lang_meta = Straker_Language::straker_language_meta('code', $data['sl']);
												echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. $lang_meta['name'] . '</label>';
												?>
											</td>
											<td>
												<?php
												$langs = str_getcsv($data['tl']);
												foreach ($langs as $lang) {
													$lang_meta = Straker_Language::straker_language_meta('code', $lang);
													echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. esc_html($lang_meta['name']) . '</label><br><br>';
												}
												?>
											</td>
											<td class="st-jb title column-title page-title">
												<?php
												$post_ids = explode(",",$data['token']);
												foreach ($post_ids as $id) { ?>
													<a title="<?php echo get_the_title ($id); ?>" href="<?php echo esc_url(get_edit_post_link($id)); ?>" target="_blank"><?php echo wp_trim_words( get_the_title ($id), 2, '...' ); ?></a><br />
												<?php } ?>
											</td>
											<td class=""><?php echo $this->straker_api_status($data['status'], $data['quotation']); ?></td>
											<td class=""><?php echo $data['created_at']; ?></td>
											<td class=""><?php echo $this->straker_api_action($data['status'], $data['quotation'], $data['job_key']); ?></td>
										</tr>
								<?php
										}
									}
								}
								?>
							</tbody>
						</table>
							<?php }else{
								echo esc_attr_e( 'No jobs with Order Now status', $this->plugin_name );
							} ?>
					</div>
					<div id="tab3" class="st-tab">
						<?php if( isset( $api_jobs_status['in_progress'] ) && is_array( $straker_api_jobs ) ){ ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<th class="" scope="col"><?php esc_attr_e('Job Reference', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Source Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Target Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Page / Post Title', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Status', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Date Submitted', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e(' ', $this->plugin_name);?></th>
							</thead>
							<tbody>
								<?php
								foreach ( $straker_api_jobs as $key => $value ) {
									foreach ( $value as $data ) {
										if( $data["status"] === 'IN_PROGRESS' ){ ?>
										<tr>
											<td class=""><a href="admin.php?page=st-jobs&jk=<?php echo $data['job_key'] ?>" ><?php echo esc_html($data['tj_number']); ?></a></td>
											<td class="">
												<?php
												$lang_meta = Straker_Language::straker_language_meta('code', $data['sl']);
												echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. $lang_meta['name'] . '</label>';
												?>
											</td>
											<td>
												<?php
												$langs = str_getcsv($data['tl']);
												foreach ($langs as $lang) {
													$lang_meta = Straker_Language::straker_language_meta('code', $lang);
													echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. esc_html($lang_meta['name']) . '</label><br><br>';
												}
												?>
											</td>
											<td class="st-jb title column-title page-title">
												<?php
												$post_ids = explode(",",$data['token']);
												foreach ($post_ids as $id) { ?>
													<a title="<?php echo get_the_title ($id); ?>" href="<?php echo esc_url(get_edit_post_link($id)); ?>" target="_blank"><?php echo wp_trim_words( get_the_title ($id), 2, '...' ); ?></a><br />
												<?php } ?>
											</td>
											<td class=""><?php echo $this->straker_api_status($data['status'], $data['quotation']); ?></td>
											<td class=""><?php echo $data['created_at']; ?></td>
											<td class=""><?php echo $this->straker_api_action($data['status'], $data['quotation'], $data['job_key']); ?></td>
										</tr>
								<?php
										}
									}
								}
								?>
							</tbody>
						</table>
						<?php }else{
							echo esc_attr_e('No jobs in progress', $this->plugin_name);
						}?>
					</div>
					<div id="tab4" class="st-tab <?php if( isset( $api_jobs_status['completed'] ) ) { echo 'st-active'; }?>">
						<?php if( isset( $api_jobs_status['completed'] ) && is_array( $straker_api_jobs ) ){ ?>
						<table class="wp-list-table widefat fixed striped">
							<thead>
								<th class="" scope="col"><?php esc_attr_e('Job Reference', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Source Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Target Language', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Page / Post Title', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Status', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e('Date Submitted', $this->plugin_name);?></th>
								<th class="" scope="col"><?php esc_attr_e(' ', $this->plugin_name);?></th>
							</thead>
							<tbody>
								<?php
								foreach ( $straker_api_jobs as $key => $value ) {
									foreach ($value as $data) {
										if($data["status"] === 'COMPLETED'){ ?>
										<tr>
											<td class=""><a href="admin.php?page=st-jobs&jk=<?php echo $data['job_key'] ?>" ><?php echo esc_html($data['tj_number']); ?></a></td>
											<td class="">
												<?php
												$lang_meta = Straker_Language::straker_language_meta('code', $data['sl']);
												echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. $lang_meta['name'] . '</label>';
												?>
											</td>
											<td>
												<?php
												$langs = str_getcsv($data['tl']);
												foreach ($langs as $lang) {
													$lang_meta = Straker_Language::straker_language_meta('code', $lang);
													echo '<label class="st-label" style="padding-left:30px; background: url('. $this->flags_path . $lang_meta['code'] .'.png) left no-repeat;">'. esc_html($lang_meta['name']) . '</label><br><br>';
												}
												?>
											</td>
											<td class="st-jb title column-title page-title">
												<?php
												$post_ids = explode(",",$data['token']);
												foreach ($post_ids as $id) { ?>
													<a title="<?php echo get_the_title ($id); ?>" href="<?php echo esc_url(get_edit_post_link($id)); ?>" target="_blank"><?php echo wp_trim_words( get_the_title ($id), 2, '...' ); ?></a><br />
												<?php } ?>
											</td>
											<td class=""><?php echo $this->straker_api_status($data['status'], $data['quotation']); ?></td>
											<td class=""><?php echo $data['created_at']; ?></td>
											<td class=""><?php echo $this->straker_api_action($data['status'], $data['quotation'], $data['job_key']); ?></td>
										</tr>
								<?php
										}
									}
								}
								?>
							</tbody>
						</table>
						<?php }else{
							echo esc_attr_e('No jobs completed', $this->plugin_name);

						}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}
?>
</div>
