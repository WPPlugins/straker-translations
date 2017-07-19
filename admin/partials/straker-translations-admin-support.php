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
	<?php
  if (isset($_GET['msg'])) {
  	if ($_GET['msg'] == 'success') { ?>
			<div id="message" class="updated notice is-dismissible">
				<p><?php _e('We have received your email and will get back to you as soon as possible.', $this->plugin_name); ?></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_attr_e('Dismiss this notice.', $this->plugin_name); ?></span></button>
			</div>
		<?php
    } elseif ($_GET['msg'] == 'failed') { ?>
			<div class='error'>
				<p><?php esc_attr_e(Straker_Translations_Config::straker_support_message, $this->plugin_name); ?></p>
			</div>
		<?php }
    } ?>
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1><br />
	<a href="http://help.strakertranslations.com/wordpress/" target="_blank"><?php echo esc_attr_e('Click here to read our Help Documentation.', $this->plugin_name); ?></a>
	<div class="st-hr">
		<p>
			<?php _e('Please fill out the form below to send us a message or send an email to ', $this->plugin_name); ?><a href="mailto:extensions@strakertranslations.com">extensions@strakertranslations.com</a>
		</p>
		<?php $current_user = wp_get_current_user(); ?>
		<form action="<?php echo admin_url('admin-post.php'); ?>" name="straker_support_form" id="straker_support_form" method="post">
			<?php wp_nonce_field('straker-translations-support', 'straker-translations-support-nonce'); ?>
			<input type="hidden" name="action" value="straker_support">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name;?>-name">
								<?php esc_attr_e('Name ', $this->plugin_name);?> <span class="description">*</span>
							</label>
						</th>
						<td>
							<input type="text" id="name" name="name" class="regular-text" value="<?php echo esc_attr($current_user->user_firstname).' '.esc_attr($current_user->user_lastname); ?>" required="" />
						</td>
					</tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name;?>-email_address">
								<?php esc_attr_e('Email Address ', $this->plugin_name);?> <span class="description">*</span>
							</label>
						</th>
						<td>
							<input type="email" id="email_address" name="email_address" class="regular-text" value="<?php echo esc_attr($current_user->user_email); ?>" required="" />
						</td>
					</tr>
					<?php if ($this->straker_jobs) { ?>
						<tr>
							<th scope="row">
								<label for="<?php echo $this->plugin_name; ?>-job_id">
									<?php esc_attr_e('Job Number ', $this->plugin_name); ?>
								</label>
							</th>
							<td>
								<?php
									$straker_jobs = $this->straker_jobs;
									if ( ! empty( $this->straker_jobs ) ) { ?>
								<select name="job_id" id="job_id">
									<option value="">Select Job Number</option>
									<?php
									foreach ($this->straker_jobs as $key => $value) {
										if (is_array($value)) {
        							foreach ($value as $data) { ?>
											<option value="<?php echo esc_attr($data['tj_number']); ?>"><?php echo esc_html($data['tj_number']); ?></option>
									<?php }
								 		}
    							} ?>
								</select>

								<p id="tagline-description" class="description"><?php esc_attr_e('Please provide your job number if known, it will help us to more quickly assist you.', $this->plugin_name); ?></p>
								<?php } ?>
							</td>
						</tr>
					<?php }
					if (!$this->straker_auth === false) {
					?>

					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name;?>-support_detail">
								<?php esc_attr_e('Category ', $this->plugin_name);?><span class="description">*</span>
							</label>
						</th>
						<td>
							<select name="category" required="">
								<option value=""><?php esc_attr_e('Select Support Type ', $this->plugin_name); ?></option>
								<option value="delivery"><?php esc_attr_e('Delivery', $this->plugin_name);?></option>
								<option value="quality"><?php esc_attr_e('Quality', $this->plugin_name);?></option>
								<option value="payment"><?php esc_attr_e('Payment issues', $this->plugin_name);?></option>
								<option value="job"><?php esc_attr_e('Job pricing & timing', $this->plugin_name);?></option>
								<option value="technical"><?php esc_attr_e('Technical issues', $this->plugin_name);?></option>
								<option value="invoice"><?php esc_attr_e('Invoicing', $this->plugin_name);?></option>
								<option value="others"><?php esc_attr_e('Others', $this->plugin_name);?></option>
							</select>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name;?>-support_detail">
								<?php esc_attr_e('Details ', $this->plugin_name);?><span class="description">*</span>
							</label>
						</th>
						<td>
							<textarea type="textarea" id="detail" name="detail" class="code" rows="10" cols="40" required></textarea>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<?php submit_button(__('Submit', $this->plugin_name), 'primary', 'submit', true); ?>
			</p>
		</form>
		<p class="alignright">
			<small>Plugin Version <?php echo $this->straker_get_version(); ?></small>
		</p>
	</div>
