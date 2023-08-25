<?php
/*
 * Plugin Name:       OpenWeather Map
 * Plugin URI:        https://#
 * Description:       Show a 5-day weather forecast for your area of residence.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CSSIgniter
 * Author URI:        https://cssigniter.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
  * Text Domain:      ciweather
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC') ) {
	die;
}

DEFINE ('CIWEATHER_URL', plugin_dir_url( __FILE__ ));

function ciweather_settings_pages() {
	add_submenu_page(
		'options-general.php',
		__( 'OpenWeather Map', 'ciweather' ),
		__( 'Weather settings', 'ciweather' ),
		'manage_options',
		'ciweather-settings',
		'ciweather_settings_subpage_markup',
	);
}
add_action('admin_menu', 'ciweather_settings_pages' );

function ciweather_settings_subpage_markup() {

	if ( ! current_user_can( 'manage_options' ) ){
		return;
	}
	?>

	<div class="wrap">
		<h1><?php esc_html_e( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Weather info.', 'ciweather' ); ?></p>
	</div>

<?php

}

// Add link to settings page
function ciweather_add_settings_link( $links ) {
    $settings_link = '<a href"admin.php?page=ciweather">' . __( 'Settings', 'ciweather' ) . '</a>';
    array_push( $links, $settings_link );
    return $links;
}

$filter_name = "plugin_action_links_" . plugin_basename( __FILE__ );
add_filter( $filter_name, 'ciweather_add_settings_link');

