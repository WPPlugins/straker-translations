<?php

/**
 * Provide a redirect to Straker My Account page
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
	$api_sig = $this->straker_api_signature();
	$my_link = $this->straker_api('myaccount/token').'?api_sig='.$api_sig.'&p=job.new';
?>
<div class="wrap">
		<h1><?php esc_attr_e( 'Sending Files and Assets for Translation', $this->plugin_name ); ?></h1>
		<p class="ma-btn mj-btn">
			<a class="button button-primary" st-data-tooltip title="<?php echo esc_attr_e('Click to send us assets via your My Account dashboard.', $this->plugin_name);?>" href="<?php echo $my_link; ?>" target="_blank"><?php esc_attr_e( 'My Account', $this->plugin_name ); ?>&nbsp;<span class="dashicons dashicons-migrate pt-3"></a>
		</p>
		<div class="st-hr">
			<p><?php esc_attr_e( "To send the following file types, please use the 'My Account' button at the top of the page to go through to your secure Straker Translations Dashboard:", $this->plugin_name ); ?></p>

			<ul class="st-ul">
				<li><?php esc_attr_e( 'Video files (.mpg, .mov, .wmv, and more)', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'Wordpress Templates (.po)', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'CSV files (.csv)', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'Image files (.ai, .ps, .jpg, .png)', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'Spreadsheets (.xls, .xlsx)', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'Documents & Presentations (.doc, .docx, .ppt, .pptx)', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'PDFs (.pdf) - where possible send the original file type for the highest quality result, e.g. InDesign', $this->plugin_name ); ?></li>
				<li><?php esc_attr_e( 'Structured content (.xlf, .xlif, .xml, .resx)', $this->plugin_name ); ?></li>

			</ul>
		</div>

</div>
