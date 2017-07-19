<?php

/**
 * Provide an metabox in posts and pages
 *
 * This file is used to show the metabox.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Language_Translation
 * @subpackage Straker_Language_Translation/admin/partials
 */

?>
<h4><?php esc_attr_e( 'You need an account before you can create a new job.', $this->plugin_name ); ?></h4>

<a class="button button-primary" href="<?php echo admin_url('admin.php?page=st-settings&'); ?> ">
	<?php esc_attr_e('Create Account', $this->plugin_name); ?>
</a>
