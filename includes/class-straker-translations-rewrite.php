<?php

class Straker_Rewrite
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
	 *
	 */

	public function __construct($plugin_name, $version)
	{}

	public function straker_add_rewrite_tags()
	{
		global $wp_rewrite;

		$regex = Straker_Language::shortcode_regex();

		if (empty($regex)) {
			return;
		}

		add_rewrite_tag('%lang%', $regex, 'lang=');
		flush_rewrite_rules();
	}

	public function straker_root_rewrite_rules($root_rewrite)
	{
		global $wp_rewrite;

		$permastruct = trailingslashit($wp_rewrite->root) . '%lang%/';

		$extra = $this->generate_rewrite_rules($permastruct, array(
			'ep_mask' => EP_ROOT));

		return array_merge($extra, $root_rewrite);
	}

	public function straker_page_rewrite_rules($page_rewrite)
	{
		global $wp_rewrite;

		$wp_rewrite->add_rewrite_tag('%pagename%', '(.?.+?)', 'pagename=');
		$permastruct = trailingslashit($wp_rewrite->root) . '%lang%/%pagename%';
		$extra = $this->generate_rewrite_rules($permastruct, array(
			'ep_mask'   => EP_PAGES,
			'walk_dirs' => false));

		return array_merge($extra, $page_rewrite);
	}

	public function straker_post_rewrite_rules($post_rewrite)
	{
		global $wp_rewrite;

		$permastruct = $wp_rewrite->permalink_structure;

		// from wp-admin/includes/misc.php
		$got_rewrite = apply_filters('got_rewrite',
			apache_mod_loaded('mod_rewrite', true));
		$got_url_rewrite = apply_filters('got_url_rewrite',
			$got_rewrite || $GLOBALS['is_nginx'] || iis7_supports_permalinks());

		if (!$got_url_rewrite) {
			$permastruct = preg_replace(
				'#^/index\.php#', '/index.php/%lang%', $permastruct);
		} elseif (is_multisite() && !is_subdomain_install() && is_main_site()) {
			$permastruct = preg_replace(
				'#^/blog#', '/%lang%/blog', $permastruct);
		} else {
			$permastruct = preg_replace(
				'#^/#', '/%lang%/', $permastruct);
		}

		$extra = $this->generate_rewrite_rules($permastruct, array(
			'ep_mask' => EP_PERMALINK,
			'paged'   => false));

		return array_merge($extra, $post_rewrite);
	}

	public function straker_post_tags_rewrite_rules($post_tag_rewrite)
	{
		global $wp_rewrite;

		$permastruct = $wp_rewrite->permalink_structure;
		$wp_rewrite->add_rewrite_tag('%tag%', '(.?.+?)', 'tag=');
		$permastruct = trailingslashit($wp_rewrite->root) . '%lang%/tag/%tag%';
		$extra = $this->generate_rewrite_rules($permastruct, array(
			'ep_mask'   => EP_TAGS,
			'walk_dirs' => false));

		return array_merge($extra, $post_tag_rewrite);
	}

	public function straker_categories_rewrite_rules($post_category_rewrite){
		global $wp_rewrite;

		$permastruct = $wp_rewrite->permalink_structure;
		$wp_rewrite->add_rewrite_tag('%category%', '(.?.+?)', 'category_name=');
		$permastruct = trailingslashit($wp_rewrite->root) . '%lang%/category/%category%';
		$extra = $this->generate_rewrite_rules($permastruct, array(
			'ep_mask'   => EP_TAGS,
			'walk_dirs' => false));

		return array_merge($extra, $post_category_rewrite);
	}

	public function straker_register_custom_post_type_rules($post_type, $args )
	{
		global $wp_rewrite;
		if ( $args->_builtin || ! $args->publicly_queryable ) {
			return;
		}
		if ( false === $args->rewrite ) {
			return;
		}
		$permastruct = Straker_Util::get_post_permalink_structure($post_type);
		$cpost_date  = Straker_Util::get_post_date_front($post_type);
		$regex = Straker_Language::shortcode_regex();
		if (empty($regex)) {
			return;
		}

		add_rewrite_rule( $regex.'/'.$permastruct.'/?$','index.php?lang=$matches[1]&post_type='.$post_type,'top');
		add_rewrite_rule( $regex.'/'.$permastruct.'/([^/]*)/?','index.php?lang=$matches[1]&post_type='.$post_type.'&name=$matches[2]','top');
		add_rewrite_rule( $regex.'/'.$permastruct . '/page/?([0-9]{1,})/?$', 'index.php?lang=$matches[1]&paged=$matches[2]&post_type=' . $post_type, 'top' );

		$slug = $permastruct;

		if ( $args->has_archive ) {
			if ( is_string( $args->has_archive ) ) {
				$slug = $args->has_archive;
			};

			if ( $args->rewrite['with_front'] ) {
				$slug = substr( $wp_rewrite->front, 1 ) . $slug;
			}

			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&feed=$matches[5]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&feed=$matches[5]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&feed=$matches[4]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&feed=$matches[4]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/([0-9]{1,2})/?$', 'index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?lang=$matches[1]&year=$matches[2]&feed=$matches[3]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$', 'index.php?lang=$matches[1]&year=$matches[2]&feed=$matches[3]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/page/?([0-9]{1,})/?$', 'index.php?lang=$matches[1]&year=$matches[2]&paged=$matches[3]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . $cpost_date . '/([0-9]{4})/?$', 'index.php?lang=$matches[1]&year=$matches[2]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . '/author/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?lang=$matches[1]&author_name=$matches[2]&paged=$matches[3]&post_type=' . $post_type, 'top' );
			add_rewrite_rule( $regex.'/'.$slug . '/author/([^/]+)/?$', 'index.php?lang=$matches[1]&author_name=$matches[2]&post_type=' . $post_type, 'top' );

			if ( in_array( 'category', $args->taxonomies ) ) {
				$category_base = get_option( 'category_base' );
				if ( ! $category_base ) {
					$category_base = 'category';
				}
				add_rewrite_rule( $regex.'/'.$slug . '/'. $category_base . '/([^/]+)/page/?([0-9]{1,})/?$', 'index.php?lang=$matches[1]&category_name=$matches[2]&paged=$matches[3]&post_type=' . $post_type, 'top' );
				add_rewrite_rule( $regex.'/'.$slug . '/'. $category_base . '/([^/]+)/?$', 'index.php?lang=$matches[1]&category_name=$matches[2]&post_type=' . $post_type, 'top' );
			}
		}
	}

	public function straker_register_custom_taxonomies_rules($taxonomy, $object_type, $args)
	{
		global $wp_rewrite;
		if ( get_option( 'no_taxonomy_structure' ) ) {
			return;
		}
		if ( $args['_builtin'] ) {
			return;
		}
		if ( false === $args['rewrite'] ) {
			return;
		}
		$regex_langs = Straker_Language::shortcode_regex();
		if (empty($regex_langs)) {
			return;
		}

		$post_types = $args['object_type'];
		foreach ( $post_types as $post_type ) {
			$post_type_obj = get_post_type_object( $post_type );
			if ( ! empty( $post_type_obj->rewrite['slug'] ) ) {
				$slug = $post_type_obj->rewrite['slug'];
			} else {
				$slug = $post_type;
			}

			if ( ! empty( $post_type_obj->has_archive ) && is_string( $post_type_obj->has_archive ) ) {
				$slug = $post_type_obj->has_archive;
			};

			if ( ! empty( $post_type_obj->rewrite['with_front'] ) ) {
				$slug = substr( $wp_rewrite->front, 1 ) . $slug;
			}

			if ( 'category' == $taxonomy ) {
				$taxonomy_slug = ( $cb = get_option( 'category_base' ) ) ? $cb : $taxonomy;
				$taxonomy_key  = 'category_name';
			} else {
				// Edit by [Xiphe]
				if ( isset( $args['rewrite']['slug'] ) ) {
					$taxonomy_slug = $args['rewrite']['slug'];
				} else {
					$taxonomy_slug = $taxonomy;
				}
				// [Xiphe] stop
				$taxonomy_key = $taxonomy;
			}

			$rules = array(
				// feed.
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&feed=\$matches[3]",
				),
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/(feed|rdf|rss|rss2|atom)/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&feed=\$matches[3]",
				),
				// year
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/date/([0-9]{4})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&year=\$matches[3]",
				),
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/date/([0-9]{4})/page/?([0-9]{1,})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&year=\$matches[3]&paged=\$matches[4]",
				),
				// monthnum
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/date/([0-9]{4})/([0-9]{1,2})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&year=\$matches[3]&monthnum=\$matches[4]",
				),
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/date/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&year=\$matches[3]&monthnum=\$matches[4]&paged=\$matches[5]",
				),
				// day
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&year=\$matches[3]&monthnum=\$matches[4]&day=\$matches[5]",
				),
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&year=\$matches[3]&monthnum=\$matches[4]&day=\$matches[5]&paged=\$matches[6]",
				),
				// paging
				array(
					'regex'    => $regex_langs.'/%s/(.+?)/page/?([0-9]{1,})/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]&paged=\$matches[3]",
				),
				// tax archive.
				array(
					'regex' => $regex_langs.'/%s/(.+?)/?$',
					'redirect' => "index.php?lang=\$matches[1]&{$taxonomy_key}=\$matches[2]",
				),
			);
			// no post_type slug.
			foreach ( $rules as $rule ) {
				$regex    = sprintf( $rule['regex'], "{$taxonomy_slug}" );
				$redirect = $rule['redirect'];
				add_rewrite_rule( $regex, $redirect, 'top' );
			}
		}
	}

	public function generate_rewrite_rules($permalink_structure, $args = '')
	{
		global $wp_rewrite;

		$defaults = array(
			'ep_mask'     => EP_NONE,
			'paged'       => true,
			'feed'        => true,
			'forcomments' => true,
			'walk_dirs'   => true,
			'endpoints'   => true);

		$args = wp_parse_args($args, $defaults);

		extract($args, EXTR_SKIP);

		$feedregex2     = '(' . implode('|', $wp_rewrite->feeds) . ')/?$';
		$feedregex      = $wp_rewrite->feed_base . '/' . $feedregex2;
		$trackbackregex = 'trackback/?$';
		$pageregex      = $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$';
		$commentregex   = 'comment-page-([0-9]{1,})/?$';
		$embedregex     = 'embed/?$';

		if ($endpoints) {
			$ep_query_append = array();

			foreach ((array) $wp_rewrite->endpoints as $endpoint) {
				$epmatch                   = $endpoint[1] . '(/(.*))?/?$';
				$epquery                   = '&' . $endpoint[1] . '=';
				$ep_query_append[$epmatch] = array($endpoint[0], $epquery);
			}
		}

		$front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
		preg_match_all('/%.+?%/', $permalink_structure, $tokens);
		$num_tokens     = count($tokens[0]);
		$index          = $wp_rewrite->index;
		$feedindex      = $index;
		$trackbackindex = $index;
		$embedindex     = $index;

		for ($i = 0; $i < $num_tokens; ++$i) {
			if (0 < $i) {
				$queries[$i] = $queries[$i - 1] . '&';
			} else {
				$queries[$i] = '';
			}

			$query_token =
			str_replace($wp_rewrite->rewritecode, $wp_rewrite->queryreplace, $tokens[0][$i])
			. $wp_rewrite->preg_index($i + 1);

			$queries[$i] .= $query_token;
		}

		$structure = $permalink_structure;

		if ($front != '/') {
			$structure = str_replace($front, '', $structure);
		}

		$structure = trim($structure, '/');

		$dirs     = $walk_dirs ? explode('/', $structure) : array($structure);
		$num_dirs = count($dirs);

		$front = preg_replace('|^/+|', '', $front);

		$post_rewrite = array();
		$struct       = $front;

		for ($j = 0; $j < $num_dirs; ++$j) {
			$struct .= $dirs[$j] . '/';
			$struct   = ltrim($struct, '/');
			$match    = str_replace($wp_rewrite->rewritecode, $wp_rewrite->rewritereplace, $struct);
			$num_toks = preg_match_all('/%.+?%/', $struct, $toks);

			$query = (isset($queries) && is_array($queries) && !empty($num_toks))
			? $queries[$num_toks - 1] : '';

			switch ($dirs[$j]) {
				case '%year%':
					$ep_mask_specific = EP_YEAR;
					break;
				case '%monthnum%':
					$ep_mask_specific = EP_MONTH;
					break;
				case '%day%':
					$ep_mask_specific = EP_DAY;
					break;
				default:
					$ep_mask_specific = EP_NONE;
			}

			$pagematch = $match . $pageregex;
			$pagequery = $index . '?' . $query
			. '&paged=' . $wp_rewrite->preg_index($num_toks + 1);

			$commentmatch = $match . $commentregex;
			$commentquery = $index . '?' . $query
			. '&cpage=' . $wp_rewrite->preg_index($num_toks + 1);

			if (get_option('page_on_front')) {
				$rootcommentmatch = $match . $commentregex;
				$rootcommentquery = $index . '?' . $query
				. '&page_id=' . get_option('page_on_front')
				. '&cpage=' . $wp_rewrite->preg_index($num_toks + 1);
			}

			$feedmatch = $match . $feedregex;
			$feedquery = $feedindex . '?' . $query
			. '&feed=' . $wp_rewrite->preg_index($num_toks + 1);

			$feedmatch2 = $match . $feedregex2;
			$feedquery2 = $feedindex . '?' . $query
			. '&feed=' . $wp_rewrite->preg_index($num_toks + 1);

			if ($forcomments) {
				$feedquery .= '&withcomments=1';
				$feedquery2 .= '&withcomments=1';
			}

			$rewrite = array();

			if ($feed) {
				$rewrite = array($feedmatch => $feedquery, $feedmatch2 => $feedquery2);
			}

			if ($paged) {
				$rewrite = array_merge($rewrite, array($pagematch => $pagequery));
			}

			if (EP_PAGES & $ep_mask || EP_PERMALINK & $ep_mask) {
				$rewrite = array_merge($rewrite, array($commentmatch => $commentquery));
			} elseif (EP_ROOT & $ep_mask && get_option('page_on_front')) {
				$rewrite = array_merge($rewrite, array($rootcommentmatch => $rootcommentquery));
			}

			if ($endpoints) {
				foreach ((array) $ep_query_append as $regex => $ep) {
					if ($ep[0] & $ep_mask || $ep[0] & $ep_mask_specific) {
						$rewrite[$match . $regex] = $index . '?' . $query
						. $ep[1] . $wp_rewrite->preg_index($num_toks + 2);
					}
				}
			}

			if ($num_toks) {
				$post = false;
				$page = false;

				if (strpos($struct, '%postname%') !== false
					|| strpos($struct, '%post_id%') !== false
					|| strpos($struct, '%pagename%') !== false
					|| (strpos($struct, '%year%') !== false
						&& strpos($struct, '%monthnum%') !== false
						&& strpos($struct, '%day%') !== false
						&& strpos($struct, '%hour%') !== false
						&& strpos($struct, '%minute%') !== false
						&& strpos($struct, '%second%') !== false)) {
					$post = true;

					if (strpos($struct, '%pagename%') !== false) {
						$page = true;
					}
				}

				if (!$post) {
					foreach (get_post_types(array('_builtin' => false)) as $ptype) {
						if (strpos($struct, "%$ptype%") !== false) {
							$post = true;
							$page = is_post_type_hierarchical($ptype);
							break;
						}
					}
				}

				if ($post) {
					$trackbackmatch = $match . $trackbackregex;
					$trackbackquery = $trackbackindex . '?' . $query . '&tb=1';

					$embedmatch = $match . $embedregex;
					$embedquery = $embedindex . '?' . $query . '&embed=true';

					$match        = rtrim($match, '/');
					$submatchbase = preg_replace('/\(([^?].+?)\)/', '(?:$1)', $match);

					$sub1        = $submatchbase . '/([^/]+)/';
					$sub1tb      = $sub1 . $trackbackregex;
					$sub1feed    = $sub1 . $feedregex;
					$sub1feed2   = $sub1 . $feedregex2;
					$sub1comment = $sub1 . $commentregex;
					$sub1embed   = $sub1 . $embedregex;

					$sub2        = $submatchbase . '/attachment/([^/]+)/';
					$sub2tb      = $sub2 . $trackbackregex;
					$sub2feed    = $sub2 . $feedregex;
					$sub2feed2   = $sub2 . $feedregex2;
					$sub2comment = $sub2 . $commentregex;
					$sub2embed   = $sub2 . $embedregex;

					$subquery        = $index . '?attachment=' . $wp_rewrite->preg_index(1);
					$subtbquery      = $subquery . '&tb=1';
					$subfeedquery    = $subquery . '&feed=' . $wp_rewrite->preg_index(2);
					$subcommentquery = $subquery . '&cpage=' . $wp_rewrite->preg_index(2);
					$subembedquery   = $subquery . '&embed=true';

					if (!empty($endpoints)) {
						foreach ((array) $ep_query_append as $regex => $ep) {
							if ($ep[0] & EP_ATTACHMENT) {
								$rewrite[$sub1 . $regex] =
								$subquery . $ep[1] . $wp_rewrite->preg_index(2);
								$rewrite[$sub2 . $regex] =
								$subquery . $ep[1] . $wp_rewrite->preg_index(2);
							}
						}
					}

					$sub1 .= '?$';
					$sub2 .= '?$';

					$match = $match . '(/[0-9]+)?/?$';
					$query = $index . '?' . $query
					. '&page=' . $wp_rewrite->preg_index($num_toks + 1);
				} else {
					$match .= '?$';
					$query = $index . '?' . $query;
				}

				$rewrite = array_merge($rewrite, array($match => $query));

				if ($post) {
					$rewrite = array_merge(array($trackbackmatch => $trackbackquery), $rewrite);

					$rewrite = array_merge(array($embedmatch => $embedquery), $rewrite);

					if (!$page) {
						$rewrite = array_merge($rewrite, array(
							$sub1        => $subquery,
							$sub1tb      => $subtbquery,
							$sub1feed    => $subfeedquery,
							$sub1feed2   => $subfeedquery,
							$sub1comment => $subcommentquery,
							$sub1embed   => $subembedquery));
					}

					$rewrite = array_merge(array(
						$sub2        => $subquery,
						$sub2tb      => $subtbquery,
						$sub2feed    => $subfeedquery,
						$sub2feed2   => $subfeedquery,
						$sub2comment => $subcommentquery,
						$sub2embed   => $subembedquery), $rewrite);
				}
			}

			$post_rewrite = array_merge($rewrite, $post_rewrite);
		}

		return $post_rewrite;
	}

	/**
	 *
	 */
	public function straker_frontpage_rewrite()
	{

		$rewrite = Straker_Translations_Config::straker_rewrite_type();
		$locale = get_locale();
		if ($locale != $GLOBALS['straker_default']) {
			// front page is set to page
			if ('page' == get_option('show_on_front')) {
				// redirect front page for language front page
				if ($this->is_locale_home()) {
						$locale_home = $this->straker_frontpage_url($locale);
						wp_redirect($locale_home);
						exit;
				}
			}
		}
	}

	public function straker_frontpage_url($locale)
	{

		$locale_home  = home_url();
		$frontpage_id = get_option('page_on_front');
		$key          = Straker_Translations_Config::straker_meta_default . $locale;
		$redirect     = Straker_Util::get_meta_by_key_value($key, $frontpage_id);	
		if ($redirect) {
			$locale_home = get_permalink($redirect);
		}

		return $locale_home;
	}

	public function is_locale_home($request_uri = '')
	{
		if (!$request_uri) {
			$request_uri = $_SERVER['REQUEST_URI'];
		}
		$rewrite   = Straker_Translations_Config::straker_rewrite_type();
		// redirect front page for code setting rewrite
		if ($rewrite === 'code') {
			$regex       = Straker_Language::shortcode_regex();
			$request_uri = str_replace('/', '', $request_uri, $count);
			// only for a subdirectory access
			if ($count === 2) {
				if (preg_match($regex, trailingslashit($request_uri), $matches)) {
					return true;
				}
			}
		}
		if ($rewrite === 'domain') {
			$request_uri = str_replace('/', '', $request_uri, $count);
			if (strlen($request_uri) === 0) {
				return true;
			}
		}
		return false;
	}

	// function straker_blogpage_rewrite() {

}
