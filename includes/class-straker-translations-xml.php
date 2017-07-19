<?php

/**
 *
 */
class Straker_XML extends SimpleXMLElement
{
		/**
		 * generate xml file for posts and pages.
		 *
		 * @since  1.0.0
		 */
		public function straker_generate_resx( $xml, $post_query, $yoast_seo, $acf_plugin )
		{
				$query = new WP_Query();
				$query = $post_query;

				if ($query->have_posts())
				{
						while ($query->have_posts())
						{
								global $post;
								$query->the_post();
								$id              = $post->ID;
								$title           = $post->post_title;
								$excerpt         = $post->post_excerpt;
								$content         = $post->post_content;
								$post_type       = $post->post_type;
								$permalink       = get_page_link( $id );
								$post_name       = $post->post_name;
								$post_categories = implode( ',', wp_get_post_categories( $id ) );

								$title_node = $xml->addChild( 'data' );
								$title_node->addAttribute( 'name', 'title_' . $id );
								$title_node->addAttribute( 'content_context', 'Title' );
								$title_node->addAttribute( 'content_context_url', $permalink );
								$title_node->addAttribute( 'post_id', $id );
								$title_node->addAttribute( 'post_type', $post_type );
								if ( $post_type == 'page' )
								{
									$page_parent = wp_get_post_parent_id( $id );
									if ( $page_parent )
									{
										$title_node->addAttribute('page_parent', $page_parent );
									}
									$page_template = get_post_meta( $id, '_wp_page_template', true );
									$title_node->addAttribute( 'page_template', $page_template );

								}
								$title_node->addAttribute( 'post_name', $post_name );
								$title_node->addAttribute( 'post_categories', $post_categories );
								$title_value = $title_node->addChild( 'value' );
								$title_value->add_CData( $title );

								$content_node = $xml->addChild('data');
								$content_node->addAttribute('name', 'content_' . $id);
								$content_node->addAttribute('content_context', 'Content');
								$content_node->addAttribute('content_context_url', $permalink);
								$content_node->addAttribute('post_categories', $post_categories);
								$content_node->addAttribute('post_id', $id);
								$content_node->addAttribute('post_type', $post_type);
								$content_node->addAttribute('post_name', $post_name);
								$content_value = $content_node->addChild('value');
								$content_value->add_CData($content);

								// Yoast SEO plugin value
								if ( $yoast_seo ) {

									if ( sizeof( Straker_Plugin::straker_wpseo_check( $id ) ) > 0 )
									{
											$wpseo = Straker_Plugin::straker_wpseo_check( $id );
											if( array_key_exists( 'seo_title', $wpseo ) )
											{
													$content_node = $xml->addChild('data');
													$content_node->addAttribute('name', 'yoast_wpseo_title_' . $id);
													$content_node->addAttribute('content_context', 'yoast_wpseo_title');
													$content_node->addAttribute('post_id', $id);
													$content_node->addAttribute('meta_key', '_yoast_wpseo_title');
													$content_value = $content_node->addChild('value');
													$content_value->add_CData($wpseo['seo_title']);
											}
											if(array_key_exists('seo_description',$wpseo))
											{
													$content_node = $xml->addChild('data');
													$content_node->addAttribute('name', 'yoast_wpseo_metadesc_' . $id);
													$content_node->addAttribute('content_context', 'yoast_wpseo_metadesc');
													$content_node->addAttribute('post_id', $id);
													$content_node->addAttribute('meta_key', '_yoast_wpseo_metadesc');
													$content_value = $content_node->addChild('value');
													$content_value->add_CData($wpseo['seo_description']);
											}
											if ( array_key_exists( 'seo_focus', $wpseo ) )
											{
													$content_node = $xml->addChild('data');
													$content_node->addAttribute('name', 'yoast_wpseo_focuskw_' . $id);
													$content_node->addAttribute('content_context', 'yoast_wpseo_focuskw');
													$content_node->addAttribute('post_id', $id);
													$content_node->addAttribute('meta_key', '_yoast_wpseo_focuskw');
													$content_value = $content_node->addChild('value');
													$content_value->add_CData($wpseo['seo_focus']);
											}
									 }
								}

								// Adavnced Custom Field plugin value
								if ( $acf_plugin )
								{
									$acf_plugin_check = Straker_Plugin::straker_acf_plugin_check( $id );
									if ( sizeof( $acf_plugin_check ) > 0 )
									{
										foreach ( $acf_plugin_check as $key => $value )
										{
											if ( isset( $value['acf_field'] ) )
											{
												if ( ! isset( $value['translate'] ) )
												{
													$content_node = $xml->addChild( 'data' );
													$content_node->addAttribute( 'name', $value['field_name'].'_'.$id );
													$content_node->addAttribute( 'acf_field', $value['acf_field'] );
													$content_node->addAttribute( 'content_context', $value['field_key'] );
													$content_node->addAttribute( 'meta_key', $value['field_name'] );
													$content_node->addAttribute( 'post_id', $id );
													$content_node->addAttribute( 'acf_data', $value['acf_data'] );
													$content_value = $content_node->addChild( 'value' );
													$content_value->add_CData( $value['field_value'] );
												} elseif ( isset( $value['translate'] ) )
												{
													$content_node = $xml->addChild( 'data' );
													$content_node->addAttribute( 'name', $value['field_name'].'_'.$id );
													$content_node->addAttribute( 'acf_field', $value['acf_field'] );
													$content_node->addAttribute( 'content_context', $value['field_key'] );
													$content_node->addAttribute( 'meta_key', $value['field_name'] );
													$content_node->addAttribute( 'translate', $value['translate'] );
													$content_node->addAttribute( 'acf_data', $value['acf_data'] );
													$content_node->addAttribute( 'post_id', $id );
												}
											} else
											{
												foreach ( $value as $sub_key => $sub_value ) {
													if ( ! isset( $sub_value['translate'] ) && isset( $sub_value['acf_is_repeater'] ) )
													{
														$content_node = $xml->addChild( 'data' );
														$content_node->addAttribute( 'name', $sub_value['field_name'].'_'.$id );
														$content_node->addAttribute( 'content_context', $sub_value['field_key'] );
														$content_node->addAttribute( 'acf_is_repeater', $sub_value['acf_is_repeater'] );
														$content_node->addAttribute( 'repeater_name', $sub_value['repeater_name'] );
														$content_node->addAttribute( 'repeater_fields', $sub_value['repeater_fields'] );
														$content_node->addAttribute( 'meta_key', $sub_value['field_name'] );
														$content_node->addAttribute( 'acf_data', $sub_value['acf_data'] );
														$content_node->addAttribute( 'post_id', $id );
														$content_value = $content_node->addChild( 'value' );
														$content_value->add_CData( $sub_value['field_value'] );
													} else if ( isset( $sub_value['translate'] ) && isset( $sub_value['acf_is_repeater'] ) )
													{
														$content_node = $xml->addChild( 'data' );
														$content_node->addAttribute( 'name', $sub_value['field_name'].'_'.$id );
														$content_node->addAttribute( 'content_context', $sub_value['field_key'] );
														$content_node->addAttribute( 'acf_is_repeater', $sub_value['acf_is_repeater'] );
														$content_node->addAttribute( 'repeater_name', $sub_value['repeater_name'] );
														$content_node->addAttribute( 'repeater_fields', $sub_value['repeater_fields'] );
														$content_node->addAttribute( 'translate', $sub_value['translate'] );
														$content_node->addAttribute( 'meta_key', $sub_value['field_name'] );
														$content_node->addAttribute( 'post_id', $id );
														$content_node->addAttribute( 'acf_data', $sub_value['acf_data'] );
													} else if ( ! isset( $sub_value['translate'] ) && isset( $sub_value['acf_is_fc'] ) )
													{
														$content_node = $xml->addChild( 'data' );
														$content_node->addAttribute( 'name', $sub_value['field_name'].'_'.$id );
														$content_node->addAttribute( 'content_context', $sub_value['field_key'] );
														$content_node->addAttribute( 'acf_is_fc', $sub_value['acf_is_fc'] );
														$content_node->addAttribute( 'fc_name', $sub_value['fc_name'] );
														$content_node->addAttribute( 'meta_key', $sub_value['field_name'] );
														$content_node->addAttribute( 'acf_data', $sub_value['acf_data'] );
														$content_node->addAttribute( 'post_id', $id );
														$content_value = $content_node->addChild( 'value' );
														$content_value->add_CData( $sub_value['field_value'] );
													} else if ( isset( $sub_value['acf_is_fc'] ) && $sub_value['acf_is_fc'] == 'yes' && isset ( $sub_value['translate'] ) )
													{
														$content_node = $xml->addChild( 'data' );
														$content_node->addAttribute( 'name', $sub_value['field_name'].'_'.$id );
														$content_node->addAttribute( 'content_context', $sub_value['field_key'] );
														$content_node->addAttribute( 'acf_is_fc', $sub_value['acf_is_fc'] );
														$content_node->addAttribute( 'fc_name', $sub_value['fc_name'] );
														$content_node->addAttribute( 'translate', $sub_value['translate'] );
														$content_node->addAttribute( 'meta_key', $sub_value['field_name'] );
														$content_node->addAttribute( 'post_id', $id );
														$content_node->addAttribute( 'acf_data', $sub_value['acf_data'] );

													} else if ( isset( $sub_value['acf_is_fc_repeater'] ) && $sub_value['acf_is_fc_repeater'] == 'yes' && ! isset ( $sub_value['translate'] ) )
													{
														$content_node = $xml->addChild( 'data' );
														$content_node->addAttribute( 'name', $sub_value['field_name'].'_'.$id );
														$content_node->addAttribute( 'content_context', $sub_value['field_key'] );
														$content_node->addAttribute( 'fc_name', $sub_value['fc_name'] );
														$content_node->addAttribute( 'repeater_name', $sub_value['repeater_name'] );
														$content_node->addAttribute( 'repeater_fields', $sub_value['repeater_fields'] );
														$content_node->addAttribute( 'repeater_field_key', $sub_value['repeater_field_key'] );
														$content_node->addAttribute( 'acf_is_fc_repeater', $sub_value['acf_is_fc_repeater'] );
														$content_node->addAttribute( 'meta_key', $sub_value['field_name'] );
														$content_node->addAttribute( 'acf_data', $sub_value['acf_data'] );
														$content_node->addAttribute( 'post_id', $id );
														$content_value = $content_node->addChild( 'value' );
														$content_value->add_CData( $sub_value['field_value'] );

													} else if ( isset( $sub_value['acf_is_fc_repeater'] ) && $sub_value['acf_is_fc_repeater'] == 'yes' && isset ( $sub_value['translate'] ) )
													{
														$content_node = $xml->addChild( 'data' );
														$content_node->addAttribute( 'name', $sub_value['field_name'].'_'.$id );
														$content_node->addAttribute( 'content_context', $sub_value['field_key'] );
														$content_node->addAttribute( 'fc_name', $sub_value['fc_name'] );
														$content_node->addAttribute( 'repeater_name', $sub_value['repeater_name'] );
														$content_node->addAttribute( 'repeater_fields', $sub_value['repeater_fields'] );
														$content_node->addAttribute( 'repeater_field_key', $sub_value['repeater_field_key'] );
														$content_node->addAttribute( 'acf_is_fc_repeater', $sub_value['acf_is_fc_repeater'] );
														$content_node->addAttribute( 'meta_key', $sub_value['field_name'] );
														$content_node->addAttribute( 'acf_data', $sub_value['acf_data'] );
														$content_node->addAttribute( 'post_id', $id );
													}
												}
											}
									 	}
								 	}
								}
							}
							wp_reset_postdata();
						}
				$resx = $xml->asXML();
				return $resx;
		}

		public function straker_import_resx( $body, $lang_code, $meta_locale, $meta_default, $short_code, $re_import )
		{
			$resx          = simplexml_load_string( $body ) or wp_redirect(admin_url('admin.php?page=st-jobs&ac=job_import&msg=failed'));
			$imported_post = array();

			foreach ( $resx->children() as $data )
			{
				$post_id      = 0;
				$new_id				= 0;
				$post_title   = '';
				$post_content = '';

				if ( $data['content_context'] == 'Title' )
				{
					$post_title      = $data->value;
					$post_id         = $data['post_id'];
					$post_type       = $data['post_type'];
					$post_name       = $data['post_name'];
					$post_categories = explode( ',', $data['post_categories'] );

					$post_name         = $post_name . '-' . $short_code;
					$content_id        = 'content_' . $post_id;
					$content_node      = $resx->xpath( '/root/data[@name="' . $content_id . '"]' );
					$translation_exist = Straker_Util::get_translated_post_meta( (string) $post_id, $meta_default );

					foreach ( $content_node as $content )
					{
						if ( ! $re_import )
						{
							if ( is_numeric( $translation_exist ) ) {
								// Insert post as revision
								$new_post = array(
									'post_title'    => wp_strip_all_tags( $post_title ),
									'post_content'  => $content->value,
									'post_type'     => 'revision',
									'post_status'   => 'inherit',
									'post_name'     => $translation_exist.'-revision-v1',
									'post_parent'		=> $translation_exist,
									'guid'					=> trailingslashit ( get_option( 'siteurl' ) ).$translation_exist.'-revision-v1'
								);
								$new_id = wp_insert_post( $new_post );
							} else {
								// Insert the post
								$new_post = array(
										'post_title'    => wp_strip_all_tags( $post_title ),
										'post_content'  => $content->value,
										'post_type'     => $post_type,
										'post_status'   => 'pending',
										'post_category' => $post_categories,
										'post_name'     => $post_name,
								);
								$new_id = wp_insert_post( $new_post );
								if ( (string) $post_type == 'page') {
									$page_template = (string) $data['page_template'];
										update_post_meta( $new_id, '_wp_page_template', $page_template );
								}
							}
						} else {
							// Update the post
							$imported_post_id = Straker_Util::get_meta_by_key_value( $meta_default, $post_id );
							$new_post         = array(
									'ID'           => $imported_post_id,
									'post_title'   => wp_strip_all_tags( $post_title ),
									'post_content' => $content->value,
									'post_name'    => $post_name,
							);
							$new_id = wp_update_post( $new_post );
						}

						if( ! $re_import && ! is_numeric( $translation_exist ) )
						{
							// Insert or Update the locale information of post
							if ( ! add_post_meta( $new_id, $meta_locale, $lang_code ) )
							{
									update_post_meta( $new_id, $meta_locale, $lang_code );
							}
							// Insert or Update the original post information
							if ( ! add_post_meta( $new_id, $meta_default, (string) $post_id ) )
							{
									update_post_meta( $new_id, $meta_default, (string) $post_id );
							}
						}

						// Yoast SEO plugin value if exists
						$seo_title_id   = 'yoast_wpseo_title_' . $post_id;
						$seo_title_node = $resx->xpath( '/root/data[@name="' . $seo_title_id . '"]' );
						foreach ( $seo_title_node as $content )
						{
							$seo_title       = (string)$content->value;
							$meta_key      	 = (string)$content['meta_key'];
							if ( ! add_post_meta( $new_id, $meta_key, $seo_title ) )
							{
								update_post_meta( $new_id, $meta_key, $seo_title );
							}
						}

						$seo_des_id   = 'yoast_wpseo_metadesc_' . $post_id;
						$seo_des_node = $resx->xpath('/root/data[@name="' . $seo_des_id . '"]');
						foreach ($seo_des_node as $content)
						{
							$seo_des       	 = (string)$content->value;
							$meta_key      	 = (string)$content['meta_key'];
							if ( ! add_post_meta( $new_id, $meta_key, $seo_des ) )
							{
									update_post_meta( $new_id, $meta_key, $seo_des );
							}
						}

						$seo_focus_id   = 'yoast_wpseo_focuskw_' . $post_id;
						$seo_focus_node = $resx->xpath('/root/data[@name="' . $seo_focus_id . '"]');
						foreach ($seo_focus_node as $content)
						{
							$seo_focus       = (string)$content->value;
							$meta_key      	 = (string)$content['meta_key'];
							if ( ! add_post_meta( $new_id, $meta_key, $seo_focus ) )
							{
									update_post_meta( $new_id, $meta_key, $seo_focus );
							}
						}

						// Importing Transled Advanced Custom Fields if exists
						$acf_translated_fields = $resx->xpath( '/root/data[@acf_data]' );
						$source_post_id	= (string) $data['post_id'];
						foreach ( $acf_translated_fields as $acf_content )
						{
							$post_acf_data = (string) $acf_content['acf_data'];
							if ( $post_acf_data == 'yes' )
							{
								$acf_repeater		= (string) $acf_content['acf_is_repeater'];
								$translate_cont	=	(string) $acf_content['translate'];
								$is_acf_field 	= (string) $acf_content['acf_field'];
								$acf_is_fc			= (string) $acf_content['acf_is_fc'];
								$acf_is_fc_rep	=	(string) $acf_content['acf_is_fc_repeater'];
								if ( isset( $is_acf_field ) && $is_acf_field == 'yes' && ! isset( $acf_content['translate'] ) )
								{
									$acf_field_value = (string) $acf_content->value;
									$meta_key      	 = (string) $acf_content['meta_key'];
									$acf_field_key   = (string) $acf_content['content_context'];
									update_post_meta( $new_id, $meta_key, $acf_field_value );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );

								} elseif ( isset( $is_acf_field ) && $is_acf_field == 'yes' && isset( $acf_content['translate'] ) )
								{
									$meta_key 			= (string) $acf_content['meta_key'];
									$acf_field_key  = (string) $acf_content['content_context'];
									update_post_meta( $new_id, $meta_key, get_post_meta( $source_post_id, $meta_key, true ) );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );
								} elseif ( isset( $acf_repeater ) && $acf_repeater == 'yes' && ! isset( $acf_content['translate'] ) )
								{
									$acf_repeater_name 		= (string) $acf_content['repeater_name'];
									$acf_field_value			= (string) $acf_content->value;
									$acf_field_name				= (string) $acf_content['meta_key'];
									$acf_field_key				= (string) $acf_content['content_context'];
									$acf_reapeater_values	= (string) $acf_content['repeater_fields'];
									update_post_meta( $new_id, $acf_field_name, $acf_field_value );
									update_post_meta( $new_id, $acf_repeater_name, $acf_reapeater_values );
									update_post_meta( $new_id, '_'.$acf_field_name, $acf_field_key );
								} elseif ( isset( $acf_repeater ) && $acf_repeater == 'yes' && isset( $acf_content['translate'] ) )
								{
									$meta_key 						= (string) $acf_content['meta_key'];
									$acf_field_key				= (string) $acf_content['content_context'];
									$acf_reapeater_values	= (string) $acf_content['repeater_fields'];
									$acf_repeater_name		= (string) $acf_content['repeater_name'];
									update_post_meta( $new_id, $meta_key, get_post_meta( $source_post_id, $meta_key, true ) );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );
									update_post_meta( $new_id, $acf_repeater_name, $acf_reapeater_values );
								} elseif ( isset( $acf_is_fc ) && $acf_is_fc == 'yes' && ! isset( $acf_content['translate'] ) )
								{
									$meta_key 				= (string) $acf_content['meta_key'];
									$acf_field_key		= (string) $acf_content['content_context'];
									$acf_fc_name			=	(string) $acf_content['fc_name'];
									$acf_field_value	= (string) $acf_content->value;
									update_post_meta( $new_id, $meta_key, $acf_field_value );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );
									update_post_meta( $new_id, $acf_fc_name, get_post_meta( $source_post_id, $acf_fc_name, true ) );
									update_post_meta( $new_id, '_'.$acf_fc_name, get_post_meta( $source_post_id, '_'.$acf_fc_name, true ) );
								} elseif ( isset( $acf_is_fc ) && $acf_is_fc == 'yes' && isset( $acf_content['translate'] ) ) {
									$meta_key 				= (string) $acf_content['meta_key'];
									$acf_field_key		= (string) $acf_content['content_context'];
									$acf_fc_name			=	(string) $acf_content['fc_name'];
									update_post_meta( $new_id, $meta_key, get_post_meta( $source_post_id, $meta_key, true ) );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );
									update_post_meta( $new_id, $acf_fc_name, get_post_meta( $source_post_id, $acf_fc_name, true ) );
									update_post_meta( $new_id, '_'.$acf_fc_name, get_post_meta( $source_post_id, '_'.$acf_fc_name, true ) );
								} elseif ( isset( $acf_is_fc_rep ) && $acf_is_fc_rep == 'yes' && ! isset( $acf_content['translate'] ) ) {
									$meta_key 						= (string) $acf_content['meta_key'];
									$acf_field_key				= (string) $acf_content['content_context'];
									$acf_fc_name					=	(string) $acf_content['fc_name'];
									$acf_field_value			= (string) $acf_content->value;
									$acf_fc_rep_key				=	(string) $acf_content['repeater_field_key'];
									$acf_fc_rep_name			=	(string) $acf_content['repeater_name'];
									$acf_fc_rep_filds_num	=	(string) $acf_content['repeater_fields'];

									update_post_meta( $new_id, $meta_key, $acf_field_value );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );
									update_post_meta( $new_id, $acf_fc_name, get_post_meta( $source_post_id, $acf_fc_name, true ) );
									update_post_meta( $new_id, '_'.$acf_fc_name, get_post_meta( $source_post_id, '_'.$acf_fc_name, true ) );
									update_post_meta( $new_id, $acf_fc_rep_name, $acf_fc_rep_filds_num );
									update_post_meta( $new_id, '_'.$acf_fc_rep_name, $acf_fc_rep_key );
								} elseif ( isset( $acf_is_fc_rep ) && $acf_is_fc_rep == 'yes' && isset( $acf_content['translate'] ) ) {
									$meta_key 						= (string) $acf_content['meta_key'];
									$acf_field_key				= (string) $acf_content['content_context'];
									$acf_fc_name					=	(string) $acf_content['fc_name'];
									$acf_fc_rep_key				=	(string) $acf_content['repeater_field_key'];
									$acf_fc_rep_name			=	(string) $acf_content['repeater_name'];
									$acf_fc_rep_filds_num	=	(string) $acf_content['repeater_fields'];

									update_post_meta( $new_id, $meta_key, $acf_field_value );
									update_post_meta( $new_id, '_'.$meta_key, $acf_field_key );
									update_post_meta( $new_id, $acf_fc_name, get_post_meta( $source_post_id, $acf_fc_name, true ) );
									update_post_meta( $new_id, '_'.$acf_fc_name, get_post_meta( $source_post_id, '_'.$acf_fc_name, true ) );
									update_post_meta( $new_id, $acf_fc_rep_name, $acf_fc_rep_filds_num );
									update_post_meta( $new_id, '_'.$acf_fc_rep_name, $acf_fc_rep_key );
								}
							}
						}
						if( is_numeric( $translation_exist ) ) {
							update_post_meta( $translation_exist, Straker_Translations_Config::straker_translated_revision_id, $new_id ) ;
							array_push( $imported_post, $translation_exist );
						} else {
							array_push( $imported_post, $new_id );
						}
					}
				}
			}
			return $imported_post;
		}

		public function straker_re_import_resx( $body, $selected_posts )
		{
			$resx             = simplexml_load_file( $body ) or die('Error: Cannot get resx file');
			$imported_post_id = $selected_posts;

			foreach ($resx->children() as $data) {
				$post_id      = 0;
				$post_title   = '';
				$post_content = '';

				if ( $data['content_context'] == 'Title' ) {
					$post_title   = $data->value;
					$post_id      = $data['post_id'];
					$post_type    = $data['post_type'];
					$content_id   = 'content_' . $post_id;
					$post_name    = $data['post_name'];
					$content_node = $resx->xpath('/root/data[@name="' . $content_id . '"]');
					$trans_post_meta = get_post_meta( $imported_post_id, "straker_locale", true );
					$source_post_id	 = get_post_meta( $imported_post_id, "straker_default_".Straker_Language::get_single_shortcode( $trans_post_meta ), true );
					foreach ( $content_node as $content )
					{
						if ( $source_post_id == $post_id ) {
							// Update the post
							$new_post = array(
									'ID'           => $imported_post_id,
									'post_title'   => wp_strip_all_tags( $post_title ),
									'post_content' => $content->value,
									'post_name'    => $post_name,
							);
							$repalced_resp = wp_update_post( $new_post );
							if ( $repalced_resp )
							{
									return true;
							} else {
									return false;
							}
						}
					}
				}
			}
			return false;
		}

		public function add_CData($cdata_text)
		{
				$node = dom_import_simplexml($this);
				$no   = $node->ownerDocument;
				$node->appendChild($no->createCDATASection($cdata_text));
		}

}
