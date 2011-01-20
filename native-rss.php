<?php
/*
Plugin Name: Native RSS
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/native-rss
Description: This plugin will change the language tag of the blogfeeds from "en" to the native language of your WP-installation by default. You can however, change the feed-language in the settings, e.g. if your blog is running in french, but you publish in dutch. Nothing specific will change in the feed but your blog will be found easier by people using the language, you are actually writing in. Also it helps search engines to list your site correcly, if you provide the feed as a sitemap. 
Version: 1.0
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

/* Geting the language from the settings or the default language from the blog */

// import laguage files

load_plugin_textdomain('native-rss', false , basename(dirname(__FILE__)).'/languages');

// init

add_action('admin_init', 'native_rss_init');

function native_rss_init() {
	
	register_setting( 'rss_language', 'rss_language', 'validate' );
	
	add_settings_section('native_rss_setting', __('Language settings', 'native-rss'), 'display_section', 'new_rss_language');
	
	add_settings_field('feed_language', __('Language:', 'native-rss'), 'display_field', 'new_rss_language', 'native_rss_setting');

}

function display_section() {
	
	echo '<p>'.__('Please give the two-letter ISO code of your language.', 'native-rss').'</p>';

}

function display_field() {
	
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

add_action('admin_menu', 'rss_admin_menu');

function rss_admin_menu() {
	
	add_options_page('Native RSS', 'Native RSS', 'administrator', 'set-rss-language', 'display_page');
	
}

// Calling the options page

function display_page() {
	
	?>
    
    <div>
    <h2>Native RSS</h2>
    
	<?php _e('Customize the &lt;language&gt; tag of your feeds.', 'native-rss'); ?>
    
    <form action="options.php" method="post">
	
	<?php settings_fields('rss_language'); ?>
	<?php do_settings_sections('new_rss_language'); ?>
    
    <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
    </form></div>
	
	<?php
}

function validate($input) {
	
	$newinput = trim($input);
	
	$language = get_option('rss_language');
	
	if(!preg_match('/^[a-z]{2}$/i', $newinput)) {
		
		$newinput = $language;
		
	}

return $newinput;

}


?>