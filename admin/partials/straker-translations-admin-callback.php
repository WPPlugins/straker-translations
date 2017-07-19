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

<?php
	$auth_token = sanitize_text_field($_GET['auth_token']);
	$api_sig = $this->straker_api_signature();
	$p = '';
	if (isset($_GET['p'])) {
    $p = '&p='.sanitize_text_field($_GET['p']);
	}
	$myaccount_auth = $this->straker_api('myaccount/authorize').'?api_sig='.$api_sig.'&auth_token='.$auth_token.$p;
	wp_redirect($myaccount_auth, $status = 302);
	exit;
?>
