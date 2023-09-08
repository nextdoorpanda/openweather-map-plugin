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
 * Text Domain:      ci-openweather
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

DEFINE( 'CIOPENWEATHER_URL', plugin_dir_url( __FILE__ ) );


// Register and enqueue scripts.
function ciopenweather_enqueue_scripts()
{

	wp_register_script( 'ci-openweather-js', CIOPENWEATHER_URL . '/assets/js/ci-openweather.js', array(), false, true );

	wp_enqueue_script('ci-openweather-js');

}
add_action( 'wp_enqueue_scripts', 'ciopenweather_enqueue_scripts' );


function ciopenweather_settings_page() {
	add_options_page( 'OpenWeather Map', 'Weather settings', 'manage_options', 'ciopenweather-settings', 'ciopenweather_settings_markup' );
}
add_action( 'admin_menu', 'ciopenweather_settings_page' );

function ciopenweather_settings_markup() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>

	<div class="wrap">
		<h2><?php _e( get_admin_page_title(), 'ci-openweather' ) ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'ci-openweather' ); ?>
			<?php do_settings_sections( 'ci-openweather' ); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'ci-openweather' ); ?>" class="button button-primary" />
		</form>
	</div>

<?php

}

	add_action( 'admin_init', 'ciopenweather_admin_init' );
	function ciopenweather_admin_init(){
		// Create Settings
		$ciopenweather_option_group = 'ci-openweather';

		$ciopenweather_api_key_option = 'ci-openweather_api_key';
		register_setting( $ciopenweather_option_group, $ciopenweather_api_key_option );

		$ciopenweather_location_option = 'ci-openweather_location';
		register_setting( $ciopenweather_option_group, $ciopenweather_location_option );

		$ciopenweather_unit_option = 'ci-openweather_unit';
		register_setting( $ciopenweather_option_group, $ciopenweather_unit_option );

		// Create section of Page
		$ciopenweather_settings_section = 'ci-openweather_main_section';
		$ciopenweather_page = 'ci-openweather';
		add_settings_section( $ciopenweather_settings_section, __( 'Settings', 'ci-openweather' ), 'ciopenweather_main_section_text_output', $ciopenweather_page );

		// Add fields to section
		add_settings_field( $ciopenweather_api_key_option, __('OpenWeather API Key', 'ci-openweather' ), 'ciopenweather_api_key_option_input', $ciopenweather_page, $ciopenweather_settings_section );

		add_settings_field($ciopenweather_location_option, __('Your Location', 'ci-openweather'), 'ciopenweather_location_option_input', $ciopenweather_page, $ciopenweather_settings_section );

		add_settings_field($ciopenweather_unit_option, __('Unit', 'ci-openweather'), 'ciopenweather_unit_option_input', $ciopenweather_page, $ciopenweather_settings_section );
	}

function ciopenweather_main_section_text_output() {
	_e( '<p>Please enter your preferred settings.</p>', 'ci-openweather' );
}

function ciopenweather_api_key_option_input() {
	echo '<input id="openweather-api_key" name="openweather-api-key" value="" type="text" autocomplete="off" class="widefat">';
	echo "<p>" . wp_kses( __( 'Enter your <strong>API key</strong>.', 'ci-openweather' ), array( 'strong' => array() ) ) . "</p>";
}

function ciopenweather_location_option_input() {
	echo '<input id="openweather-location" name="openweather-location" value="" type="text" autocomplete="off" class="widefat">';
	echo "<p>" . wp_kses( __( 'Enter your <strong>location</strong>.', 'ci-openweather' ), array( 'strong' => array() ) ) . "</p>";
}

function ciopenweather_unit_option_input() {
	echo '<input id="openweather-unit" name="openweather-unit" value="" type="text" autocomplete="off" class="widefat">';
	echo "<p>" . wp_kses( __( 'Enter the <strong>unit of measurement</strong>.', 'ci-openweather' ), array( 'strong' => array() ) ) . "</p>";
}



// Add link to settings page.
function ciopenweather_add_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=ciopenweather-settings">' . __( 'Settings', 'ci-openweather' ) . '</a>';
	array_push( $links, $settings_link );
	return $links;
}

$filter_name = 'plugin_action_links_' . plugin_basename( __FILE__ );
add_filter( $filter_name, 'ciopenweather_add_settings_link' );


// Register shortcode.
add_shortcode( 'ci-openweather', 'shortcode_ciopenweather' );

function shortcode_ciopenweather( $atts = [], $content ) {
	$weather_output = '<div class="weather-output"></div>';
	$content .= $weather_output;
	return $content;
}