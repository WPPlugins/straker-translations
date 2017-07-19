<?php
/**
 * @link              https://www.strakertranslations.com
 * @since             1.0.0
 * @package           Straker_Translations
 *
 * @wordpress-plugin
 * Plugin Name:       Straker Translations
 * Plugin URI:        https://help.strakertranslations.com/wordpress/
 * Description:       Straker Translations WordPress Plugin makes it easy to build multilingual sites.
 * Version:           1.0.2
 * Author:            Straker Translations
 * Author URI:        https://www.strakertranslations.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       straker-translations
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'STRAKER_PLUGIN_RELATIVE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

define( 'STRAKER_PLUGIN_ABSOLUTE_PATH', untrailingslashit( plugins_url( '', __FILE__  ) ) );

define( 'STRAKER_PLUGIN_FILE', plugin_basename( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-straker-translations-activator.php
 */
function activate_straker_translations()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-straker-translations-activator.php';
	Straker_Translations_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-straker-translations-deactivator.php
 */
function deactivate_straker_translations()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-straker-translations-deactivator.php';
	Straker_Translations_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_straker_translations');
register_deactivation_hook(__FILE__, 'deactivate_straker_translations');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-straker-translations.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_straker_translations()
{
	global $straker;
	$straker = new Straker_Translations();
	$straker->run();
	return $straker;
}
run_straker_translations();
