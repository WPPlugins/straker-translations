<?php

function straker_translation_link($link)
{
		$locale = get_locale();
		if ($locale == $GLOBALS['straker_default'])
		{
				return $link;
		} else {
				$postid           = url_to_postid($link);
				$key              = Straker_Translations_Config::straker_meta_default . $locale;
				$translated       = Straker_Util::get_meta_by_key_value($key, $postid);
				$translation_link = get_permalink($translated);
				return $translation_link;
		}
}

function is_straker_frontpage()
{
	$locale 			= get_locale();
	$locale_home  = home_url();
	$frontpage_id = get_option('page_on_front');
	$key          = Straker_Translations_Config::straker_meta_default . $locale;
	$redirect     = Straker_Util::get_meta_by_key_value($key, $frontpage_id);
	if ($redirect) {
		return true;
	}
	return false;
}
