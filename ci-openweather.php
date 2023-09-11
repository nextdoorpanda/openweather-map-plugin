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
function ciopenweather_enqueue_scripts() {

	wp_register_script( 'ci-openweather-js', CIOPENWEATHER_URL . '/assets/js/ci-openweather.js', array(), 1.0, true );

	$api_key  = get_option( 'ci-openweather_api_key' );
	$location = get_option( 'ci-openweather_location' );
	$unit     = get_option( 'ci-openweather_unit' );

	// Pass DB values to JS script.
	wp_localize_script(
		'ci-openweather-js',
		'weatherOptionsValues',
		array(
			'api_key'  => $api_key,
			'location' => $location,
			'unit'     => $unit,
		)
	);

	wp_enqueue_script( 'ci-openweather-js' );
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
		<h2><?php esc_html_e( 'OpenWeather Map', 'ci-openweather' ); ?></h2>
		<form action="options.php" method="post">
			<?php settings_fields( 'ci-openweather' ); ?>
			<?php do_settings_sections( 'ci-openweather' ); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'ci-openweather' ); ?>" class="button button-primary" />
		</form>
	</div>

<?php
}

add_action( 'admin_init', 'ciopenweather_admin_init' );
function ciopenweather_admin_init() {
	// Create Settings.
	$ciopenweather_option_group = 'ci-openweather';

	$ciopenweather_api_key_option = 'ci-openweather_api_key';
	register_setting( $ciopenweather_option_group, $ciopenweather_api_key_option );

	$ciopenweather_location_option = 'ci-openweather_location';
	register_setting( $ciopenweather_option_group, $ciopenweather_location_option );

	$ciopenweather_unit_option = 'ci-openweather_unit';
	register_setting( $ciopenweather_option_group, $ciopenweather_unit_option );

	// Create section of Page.
	$ciopenweather_settings_section = 'ci-openweather_main_section';

	$ciopenweather_page = 'ci-openweather';
	add_settings_section( $ciopenweather_settings_section, __( 'Settings', 'ci-openweather' ), 'ciopenweather_main_section_text_output', $ciopenweather_page );

	// Add fields to section.
	add_settings_field( $ciopenweather_api_key_option, __( 'OpenWeather API Key', 'ci-openweather' ), 'ciopenweather_api_key_option_input', $ciopenweather_page, $ciopenweather_settings_section );

	add_settings_field( $ciopenweather_location_option, __( 'Your Location (e.g. "Athens, GR")', 'ci-openweather' ), 'ciopenweather_location_option_input', $ciopenweather_page, $ciopenweather_settings_section );

	add_settings_field( $ciopenweather_unit_option, __( 'Unit', 'ci-openweather' ), 'ciopenweather_unit_option_input', $ciopenweather_page, $ciopenweather_settings_section );
}

function ciopenweather_main_section_text_output() {
	echo '<p>' . esc_html_e( 'Please enter your preferred settings.', 'ci-openweather' ) . '</p>';
}

function ciopenweather_api_key_option_input() {
	$api_key = get_option( 'ci-openweather_api_key' );
	echo '<input id="openweather-api_key" name="ci-openweather_api_key" value="' . esc_attr( $api_key ) . '" type="text" autocomplete="off" class="widefat" required>';
	echo '<p>' . wp_kses( __( 'Enter your <strong>API key</strong>.', 'ci-openweather' ), array( 'strong' => array() ) ) . '</p>';
}

function ciopenweather_location_option_input() {
	$location = get_option( 'ci-openweather_location' );
	echo '<input id="openweather-location" name="ci-openweather_location" value="' . esc_attr( $location ) . '" type="text" autocomplete="off" class="widefat" required>';
	echo '<p>' . wp_kses( __( 'Enter your <strong>location</strong>.', 'ci-openweather' ), array( 'strong' => array() ) ) . '</p>';
}

function ciopenweather_unit_option_input() {
	$unit = get_option( 'ci-openweather_unit' );
	?>
	<label>
		<select name="ci-openweather_unit">
			<option value="imperial" <?php selected( esc_attr( $unit ), 'imperial' ); ?>>Imperial (&deg;F)</option>
			<option value="metric" <?php selected( esc_attr( $unit ), 'metric' ); ?>>Celsius (&deg;C)</option>
			<option value="standard" <?php selected( esc_attr( $unit ), 'standard' ); ?>>Kelvin (K)</option>
		</select>
	</label>
	<?php
	echo '<p>' . wp_kses( __( 'Enter the <strong>unit of measurement</strong>.', 'ci-openweather' ), array( 'strong' => array() ) ) . '</p>';
}



// Add link to settings page.
function ciopenweather_add_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=ciopenweather-settings">' . __( 'Settings', 'ci-openweather' ) . '</a>';

	$links[] = $settings_link;
	return $links;
}

$filter_name = 'plugin_action_links_' . plugin_basename( __FILE__ );
add_filter( $filter_name, 'ciopenweather_add_settings_link' );


// Register shortcode.
add_shortcode( 'ci-openweather', 'shortcode_ciopenweather' );

function shortcode_ciopenweather( $content ) {
	$weather_output = '<div class="weather-output"></div>';

	$content .= $weather_output;

	return $content;
}