<?php
/*
Plugin Name: Native RSS
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/native-rss
Description: This plugin will change the language tag of the blogfeeds from "en" to the native language of your WP-installation by default. You can however, change the feed-language in the settings, e.g. if your blog is running in french, but you publish in dutch. Nothing specific will change in the feed but your blog will be found easier by people using the language, you are actually writing in. Also it helps search engines to list your site correcly, if you provide the feed as a sitemap. 
Version: 2.0
Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
License: GPL3
*/

/*  Copyright 2011  Waldemar Stoffel  (email : stoffel@atelier-fuenf.de)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


/* Stop direct call */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die("Sorry, you don't have direct access to this page."); }

// import laguage files

load_plugin_textdomain('native-rss', false , basename(dirname(__FILE__)).'/languages');

//Additional links on the plugin page

add_filter('plugin_row_meta', 'nrs_register_links',10,2);

function nrs_register_links($links, $file) {
	
	$base = plugin_basename(__FILE__);
	if ($file == $base) {
		$links[] = '<a href="options-general.php?page=set-feed-language">'.__('Settings','native-rss').'</a>';
		$links[] = '<a href="http://wordpress.org/extend/plugins/native-rss/faq/" target="_blank">'.__('FAQ','native-rss').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GTBQ93W3FCKKC" target="_blank">'.__('Donate','native-rss').'</a>';
	}
	
	return $links;

}

// init

add_action('admin_init', 'native_rss_init');

function native_rss_init() {
	
	register_setting( 'rss_language', 'rss_language', 'nrs_validate' );
	
	add_settings_section('native_rss_setting', __('Language settings', 'native-rss'), 'nrs_display_section', 'new_rss_language');
	
	add_settings_field('feed_language', __('Language:', 'native-rss'), 'nrs_display_field', 'new_rss_language', 'native_rss_setting');

}

function nrs_display_section() {
	
	echo '<p>'.__('Please give the two-letter ISO code of your language.', 'native-rss').'</p>';

}

function nrs_display_field() {
	
	$rss_language = get_option('rss_language');
	
	echo "<input id=\"feed_language\" name=\"rss_language\" size=\"4\" type=\"text\" value=\"{$rss_language}\" />";
	
}

// Setting the RSS <language> tag to the blog's default language on activation and customize if necessary

register_activation_hook(  __FILE__, 'set_language' );

function set_language() {
	
	$new_rss_language = substr(get_bloginfo('language'),0,2 );
	
	update_option('rss_language', $new_rss_language);
	
}

// Setting the RSS <language> tag back to english on deactivation

register_deactivation_hook(  __FILE__, 'unset_language' );

function unset_language() {
	
	update_option('rss_language', 'en');
	
}

// Installing options page

add_action('admin_menu', 'nrs_admin_menu');

function nrs_admin_menu() {
	
	add_options_page('Native RSS', 'Native RSS', 'administrator', 'set-feed-language', 'nrs_options_page');
	
}

// Calling the options page

function nrs_options_page() {
	
	?>
    
    <div>
    <h2>Native RSS</h2>
    
	<?php _e('Customize the &#60;language&#62; tag of your feeds.', 'native-rss'); ?>
    
    <form action="options.php" method="post">
	
	<?php settings_fields('rss_language'); ?>
	<?php do_settings_sections('new_rss_language'); ?>
    
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
	
	<?php
}

function nrs_validate($input) {
	
	$newinput = trim($input);
	
	$language = get_option('rss_language');
	
	if(!preg_match('/^[a-z]{2}$/i', $newinput)) {
		
		$newinput = $language;
		
	}

return $newinput;

}


?>