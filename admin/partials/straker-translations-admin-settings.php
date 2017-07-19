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
	<?php if ($this->straker_auth === false) { ?>
	<div class="st-hr">
		<h1><?php echo esc_attr_e('Create Account', $this->plugin_name) ?></h1>
		<?php include_once 'straker-translations-admin-message.php'; ?>
		<?php $current_user = wp_get_current_user(); ?>
		<form name="straker_registration_form" id="straker_registration_form" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
			<?php wp_nonce_field('straker-translations-register', 'straker-translations-register-nonce'); ?>
			<input type="hidden" name="action" value="straker_register">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-first_name">
								<?php esc_attr_e('First Name', $this->plugin_name); ?> <span class="description">*</span>
							</label>
						</th>
						<td>
							<input type="text" id="first_name" name="first_name" class="regular-text" value="<?php echo esc_attr($current_user->user_firstname); ?>" required=""/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-last_name">
								<?php esc_attr_e('Last Name', $this->plugin_name); ?> <span class="description">*</span>
							</label>
						</th>
						<td>
							<input type="text" id="last_name" name="last_name" class="regular-text" value="<?php echo esc_attr($current_user->user_lastname); ?>" required=""/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-email_address">
								<?php esc_attr_e('Email Address', $this->plugin_name); ?> <span class="description">*</span>
							</label>
						</th>
						<td>
							<input type="email" id="email_address" name="email_address" class="regular-text" value="<?php echo esc_attr($current_user->user_email); ?>" required=""/>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-phone_number">
								<?php esc_attr_e('Phone', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<input type="number" id="phone_number" name="phone_number" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-country">
								<?php esc_attr_e('Country', $this->plugin_name); ?> <span class="description">*</span>
							</label>
						</th>
						<td>
							<select name='country' id='country' required="">
								<option value="" selected>Select Country</option>
							<?php
                foreach ($this->straker_countries as $key => $value) {
                    echo '<option value="'.esc_attr($value['code']).'">'.esc_html($value['name']).'</option>';
                }
    					?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-company_name">
								<?php esc_attr_e('Company Name', $this->plugin_name); ?>
							</label>
						</th>
						<td>
							<input type="text" id="company_name" name="company_name" class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="<?php echo $this->plugin_name; ?>-terms-condition">
							</label>
						</th>
						<td>
							<?php esc_attr_e('I have read and accept the ', $this->plugin_name); ?><a href="https://www.strakertranslations.com/terms-conditions/" target="_blank"><?php esc_attr_e('Terms and Conditions', $this->plugin_name); ?>:</a>&nbsp; &nbsp; <input type="checkbox" id="terms_condition" name="terms_condition" required="" />
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button(__('Create Account', $this->plugin_name), 'primary', 'submit', true); ?>
		</form>
	</div>
 	<?php } else { ?>
	<?php include_once 'straker-translations-admin-sandbox-message.php'; ?>
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<?php include_once 'straker-translations-admin-message.php';
    	$active_tab   = isset( $_GET[ 'tab' ] ) ? esc_attr($_GET[ 'tab' ]) : $_GET[ 'tab' ] = 'main_settings';
		$plugin_mode  = ( 'true' == Straker_Translations_Config::straker_sandbox_mode() ) ? 'Sandbox' : 'Live';
		$default_lang = $this->straker_default_language;
		$added_langs  = $this->straker_added_language;
		$rewrite_opt  = Straker_Translations_Config::straker_rewrite_type();
	?>
	<h2 class="nav-tab-wrapper">
		<a <?php if( ! empty( $default_lang ) && ! empty( $added_langs ) && ! empty( $rewrite_opt ) ) { ?> href="<?php echo esc_url(admin_url( 'admin.php?page=st-settings&tab=main_settings'));?>" <?php } ?> class="nav-tab <?php echo $active_tab == 'main_settings' ? 'nav-tab-active' : ''; ?>" <?php if ( empty( $default_lang ) || empty( $rewrite_opt ) ) { ?> st-data-tooltip title="<?php esc_attr_e('Please select your languages and URL preferences first.', $this->plugin_name);?>" <?php } ?>><?php esc_attr_e('Main Settings', $this->plugin_name); ?></a>
		<a <?php if( ! empty( $default_lang ) && ! empty( $added_langs ) && ! empty( $rewrite_opt ) ) { ?> href="<?php echo esc_url(admin_url( 'admin.php?page=st-settings&tab=language_management'));?>" <?php } ?> class="nav-tab <?php echo $active_tab == 'language_management' ? 'nav-tab-active' : ''; ?>" <?php if( empty( $default_lang ) || empty( $rewrite_opt ) ) { ?> st-data-tooltip title="<?php esc_attr_e('Please select your languages and URL preferences first.', $this->plugin_name);?>" <?php } ?>><?php esc_attr_e('Language Management', $this->plugin_name); ?></a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=st-settings&tab=language_settings' ) );?>" class="nav-tab <?php echo $active_tab == 'language_settings' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Language Settings', $this->plugin_name); ?></a>
		<a <?php if( ! empty( $default_lang ) && ! empty( $added_langs ) ) { ?> href="<?php echo esc_url(admin_url( 'admin.php?page=st-settings&tab=url_settings'));?>" <?php } ?> class="nav-tab <?php echo $active_tab == 'url_settings' ? 'nav-tab-active' : '';?>" <?php if( empty( $default_lang ) && empty( $added_langs ) ){ ?> st-data-tooltip title="<?php esc_attr_e('Please select your languages preferences first.', $this->plugin_name);?>" <?php } ?>><?php esc_attr_e('URL Settings', $this->plugin_name); ?></a>
	</h2>
	<?php
		if( $active_tab == 'main_settings' ) {
			include_once 'straker-translations-admin-settings-main.php';
		} elseif( $active_tab == 'url_settings' ) {
			include_once 'straker-translations-admin-settings-url.php';
		} elseif($active_tab == 'language_settings') {
			include_once 'straker-translations-admin-settings-language.php';
		}else{
			include_once 'straker-translations-admin-settings-language-management.php';
		}
	}
	?>
</div>
