<?php

/**
 * Provide an metabox in posts and pages.
 *
 * This file is used to show the metabox.
 *
 * @link       https://www.strakertranslations.com
 * @since      1.0.0
 */
?>

  <div class="wrap">
    <?php
    global $wpdb;
    global $post;

    $table_post = $wpdb->prefix.'posts';

      $attachements_ids = $this->straker_getImgs_ids($post->ID);

      if ( ! empty( $attachements_ids ) ) {
          wp_nonce_field($this->plugin_name, 'straker_assets_meta_box_nonce');
          ?>
        <table class="wp-list-table widefat fixed striped">
           <thead>
             <th class="manage-column" scope="col"><?php echo esc_attr_e('Browse Localized File', $this->plugin_name); ?></th>
             <th class="manage-column" scope="col"><?php echo esc_attr_e('Image File', $this->plugin_name); ?></th>
             <th class="manage-column" scope="col"><?php echo esc_attr_e('Attachement Type', $this->plugin_name); ?></th>
           </thead>
           <tbody>
            <?php
            foreach ($attachements_ids as $key => $value) {
              $non_localized_images = $wpdb->get_row($wpdb->prepare("SELECT ID,post_title,guid,post_mime_type FROM $table_post WHERE ID = %d ", $key));
              if ($non_localized_images) {
                  ?>
                    <tr>
                       <td><input type="file" name="localized_files[]" id="localized_files_id" accept="image/*" />
                         <td>
                         <img id="image-preview "src="<?php echo esc_url($non_localized_images->guid); ?>" width="100" height="100" style="max-height: 100px; width: 100px;">
                           <input type="hidden" name="s_img[]" value="<?php echo esc_attr($value); ?>" />
                           <input type="hidden" name="imgs_ids[]" value="<?php echo esc_attr($key); ?>" />
                         </td>
                         <td><?php echo esc_html($non_localized_images->post_mime_type); ?>
                        </td>
                    </tr>
                  <?php
              }
            }
            ?>
            </tbody>
      </table>
      <?php
      } ?>
</div>
