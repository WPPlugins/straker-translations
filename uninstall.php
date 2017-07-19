<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 */

// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

function uninstall_straker_translations()
{
	global $wpdb;
	$table_options  = $wpdb->prefix . 'options';
	$results_option = $wpdb->get_results("SELECT `option_name` FROM $table_options WHERE `option_name` LIKE 'straker_%'");
	if ($results_option) {
		foreach ($results_option as $key) {
			delete_option($key->option_name);
		}
	}

	$table_postmeta   = $wpdb->prefix . 'postmeta';
	$results_postmeta = $wpdb->get_results("SELECT `meta_key` FROM $table_postmeta WHERE `meta_key` LIKE 'straker_%'");
	if ($results_postmeta) {
		foreach ($results_postmeta as $key) {
			delete_post_meta_by_key($key->meta_key);
		}
	}
}

uninstall_straker_translations();
