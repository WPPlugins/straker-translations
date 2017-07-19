<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 *
 * @package    Straker_Translations
 * @subpackage Straker_Translations/includes
 */

class Straker_Language_List extends WP_Widget
{

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */

		protected $ST_LINK;
		protected $default_language;
		protected $added_language;

		public function __construct()
		{
			$this->ST_LINK          = new Straker_Link();
			$this->default_language = Straker_Language::get_default_language();
			$this->added_language   = Straker_Language::get_added_language();

			$name = "Straker Language Switcher &lt;ul&gt;";

			$widget_ops  = array('description' => 'This is a language switcher for the Straker Translations plugin.');
			$control_ops = "";

			parent::__construct(
				false,
				$name,
				$widget_ops,
				$control_ops
			);

			$this->str = "Straker Widget";

		}

		public function widget($args, $instance)
		{
				// Before widget
				echo (isset($before_widget) ? $before_widget : '');

				$list = '<ul style="list-style: none;"';
				if ($instance['horizontal'] && $instance['horizontal'] == 'on') {
						$list .= " id='langlist'";
				}
				if ($instance['custom_css'] && $instance['custom_css'] !== '') {
						$list .= " class='" . $instance['custom_css'] . "'";
				}
				$list .= ">";
				$list .= "<li><a href='" . esc_url($this->ST_LINK->straker_default_home()) . "'>";
				if ($instance['flag'] && $instance['flag'] == 'on') {
						$list .= "<img src='" . STRAKER_PLUGIN_ABSOLUTE_PATH . '/assets/img/flags/' . $this->default_language['code'] . ".png' alt='" . $this->default_language['native_name'] . "' style='vertical-align: text-top;' /> ";
				}
				if ($instance['lang'] && $instance['lang'] == 'on') {
						$list .= $this->default_language['native_name'];
				}
				$list .= "</a></li>";
				foreach ($this->added_language as $value) {
						if (in_array($value['native_name'], $instance['available'])) {
								$list .= "<li><a href='" . esc_url($this->ST_LINK->straker_locale_home($locale = $value['wp_locale'])) . "'>";
								if ($instance['flag'] == 'on') {
										$list .= "<img src='" . STRAKER_PLUGIN_ABSOLUTE_PATH . '/assets/img/flags/' . $value['code'] . ".png' alt='" . $this->default_language['native_name'] . "' style='vertical-align: text-top;' /> ";
								}
								if ($instance['lang'] == 'on') {
										$list .= $value['native_name'];
								}
								$list .= "</a></li>";
						}
				}
				$list .= "</ul>";
				echo $list;
				// After widget code
				echo (isset($after_widget) ? $after_widget : '');
		}

		public function form( $instance ) {

			$defaults = array(
					'custom_css' => '',
					'flag'       => 'off',
					'lang'       => 'off',
					'horizontal' => 'off',
					'available'  => array(),
				);
				
				$instance = wp_parse_args((array) $instance, $defaults );
				echo '<br />' . __('Available Languages:', $this->plugin_name );
				foreach ($this->added_language as $key => $value) {
						?>
			<p>
			<input class="checkbox" type="checkbox" id="<?php echo $value['native_name']; ?>" name="<?php echo $this->get_field_name('available'); ?>[]" value="<?php echo $value['native_name']; ?>" <?php if ($instance['available']) {
								checked(in_array($value['native_name'], $instance['available']));}?> />
			<label for=""><?php echo $value['name']." - ".$value['native_name']; ?></label>
			</p>
			<?php }?>
			<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['flag'], 'on');?> id="<?php echo $this->get_field_id('flag'); ?>" name="<?php echo $this->get_field_name('flag'); ?>" />
			<label for="<?php echo $this->get_field_id('flag'); ?>"><?php esc_attr_e('Display flag', $this->plugin_name);?></label>
			</p>
			<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['lang'], 'on');?> id="<?php echo $this->get_field_id('lang'); ?>" name="<?php echo $this->get_field_name('lang'); ?>" />
			<label for="<?php echo $this->get_field_id('lang'); ?>"><?php esc_attr_e('Display language', $this->plugin_name);?></label>
			</p>
			<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['horizontal'], 'on');?> id="<?php echo $this->get_field_id('horizontal'); ?>" name="<?php echo $this->get_field_name('horizontal'); ?>" />
			<label for="<?php echo $this->get_field_id('horizontal'); ?>"><?php esc_attr_e('Dipslay horizontal', $this->plugin_name);?></label>
			</p>
			<p>
			<label for="<?php echo $this->get_field_id('custom_css'); ?>"><?php esc_attr_e('CSS Class for', $this->plugin_name);?> &lt;ul&gt;</label>
			<input class="widefat"  id="<?php echo $this->get_field_id('custom_css'); ?>" name="<?php echo $this->get_field_name('custom_css'); ?>" type="text" value="<?php echo esc_attr($instance['custom_css']); ?>" />
			</p>
			<?php
}

		public function update($new_instance, $old_instance)
		{

			$instance               = $old_instance;
			$instance['custom_css'] = strip_tags( $new_instance['custom_css'] );
			// The update for the variable of the checkbox

			$instance['flag']       = isset( $new_instance['flag'] ) ? esc_attr( $new_instance['flag'] ) : 'off';
			$instance['lang']       = isset( $new_instance['lang'] ) ? esc_attr( $new_instance['lang'] ) : 'off';
			$instance['horizontal'] = isset( $new_instance['horizontal'] ) ? esc_attr( $new_instance['horizontal'] ) : 'off';
			$instance['available']  = isset( $new_instance['available'] ) ? $new_instance['available']  : array();
			return $instance;
		}

}

?>
