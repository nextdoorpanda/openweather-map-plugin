<?php
/**
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
 **/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

DEFINE( 'CIOPENWEATHER_URL', plugin_dir_url( __FILE__ ) );


// Register and enqueue scripts.
function ciopenweather_register_scripts() {

	wp_register_script( 'ci-openweather-js', CIOPENWEATHER_URL . '/assets/js/ci-openweather.js', array(), 1.0, true );
	wp_register_style( 'ci-openweather-css', CIOPENWEATHER_URL . '/assets/css/ci-openweather.css', array(), 1.0 );

	$options = array(
		'api_key'  => get_option( 'ci-openweather_api_key', '' ),
		'location' => get_option( 'ci-openweather_location', 'Athens, GR' ),
		'unit'     => get_option( 'ci-openweather_unit', 'metric' ),
	);

	// Pass DB values to JS script.
	wp_localize_script( 'ci-openweather-js', 'weatherOptionsValues', $options );

}
add_action( 'wp_enqueue_scripts', 'ciopenweather_register_scripts' );


function ciopenweather_settings_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	add_options_page( __( 'OpenWeather Map', 'ci-openweather' ), __( 'Weather settings', 'ci-openweather' ), 'manage_options', 'ciopenweather-settings', 'ciopenweather_settings_markup' );
}
add_action( 'admin_menu', 'ciopenweather_settings_page' );

function ciopenweather_settings_markup() {

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
	$ciopenweather_page         = 'ci-openweather';

	register_setting( $ciopenweather_option_group, 'ci-openweather_api_key' );
	register_setting( $ciopenweather_option_group, 'ci-openweather_location' );
	register_setting( $ciopenweather_option_group, 'ci-openweather_unit' );

	// Create section of Page.
	add_settings_section( 'ci-openweather_main_section', __( 'Settings', 'ci-openweather' ), 'ciopenweather_main_section_text_output', $ciopenweather_page );

	// Add fields to section.
	add_settings_field( 'ci-openweather_api_key', __( 'OpenWeather API Key', 'ci-openweather' ), 'ciopenweather_api_key_option_input', $ciopenweather_page, 'ci-openweather_main_section' );
	add_settings_field( 'ci-openweather_location', __( 'Your Location', 'ci-openweather' ), 'ciopenweather_location_option_input', $ciopenweather_page, 'ci-openweather_main_section' );
	add_settings_field( 'ci-openweather_unit', __( 'Unit', 'ci-openweather' ), 'ciopenweather_unit_option_input', $ciopenweather_page, 'ci-openweather_main_section' );
}

function ciopenweather_main_section_text_output() {
	echo sprintf(
		'<p>%s</p>',
		wp_kses( __( 'Please enter your preferred settings.', 'ci-openweather' ), array() )
	);
}

function ciopenweather_api_key_option_input() {
	$api_key = get_option( 'ci-openweather_api_key' );
	echo sprintf(
		'<input id="openweather-api_key" name="ci-openweather_api_key" value="%s" type="text" autocomplete="off" class="widefat" required>',
		esc_attr( $api_key )
	);
	echo sprintf(
		'<p>%s</p>',
		wp_kses( __( 'Enter your <strong>API key</strong>.', 'ci-openweather' ), array( 'strong' => array() ) )
	);
}

function ciopenweather_location_option_input() {
	$location = get_option( 'ci-openweather_location' );
	echo sprintf(
		'<input id="openweather-location" name="ci-openweather_location" value="%s" type="text" autocomplete="off" class="widefat" required>',
		esc_attr( $location )
	);
	echo sprintf(
		'<p>%s</p>',
		wp_kses( __( 'Enter your <strong>location</strong>. (e.g. "Athens, GR")', 'ci-openweather' ), array( 'strong' => array() ) )
	);
}

function ciopenweather_unit_option_input() {
	$unit = get_option( 'ci-openweather_unit' );
	?>
	<label>
		<select name="ci-openweather_unit">
			<?php
				echo sprintf( '<option value="imperial" %s>%s</option>',
					selected( $unit, 'imperial' ),
					wp_kses( __( 'Imperial (&deg;F)', 'ci-openweather' ), array() )
				);
				echo sprintf( '<option value="metric" %s>%s</option>',
					selected( $unit, 'metric' ),
					wp_kses( __( 'Celsius (&deg;C)', 'ci-openweather' ), array() )
				);
				echo sprintf( '<option value="standard" %s>%s</option>',
					selected( $unit, 'standard' ),
					wp_kses( __( 'Kelvin (K)', 'ci-openweather' ), array() )
				);
			?>
		</select>
	</label>
	<?php
	echo sprintf(
		'<p>%s</p>',
		wp_kses( __( 'Enter the <strong>unit of measurement</strong>.', 'ci-openweather' ), array( 'strong' => array() ) )
	);
}



// Add link to settings page.
function ciopenweather_add_settings_link( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( add_query_arg( array( 'page' => 'ciopenweather-settings' ), admin_url( 'options-general.php' ) ) ),
		wp_kses( __( 'Settings', 'ci-openweather' ), array() )
	);

	$links[] = $settings_link;
	return $links;
}

$filter_name = 'plugin_action_links_' . plugin_basename( __FILE__ );
add_filter( $filter_name, 'ciopenweather_add_settings_link' );


// Register shortcode.
add_shortcode( 'ci-openweather', 'shortcode_ciopenweather' );

function shortcode_ciopenweather( $content ) {

	if ( ! get_option( 'ci-openweather_api_key' ) ) {
		return;
	}

	wp_enqueue_script( 'ci-openweather-js' );
	wp_enqueue_style( 'ci-openweather-css' );

	$weather_output =
		'<div class="openweather-content-wrap">
			<div class="openweather-content d-flex justify-content-center align-items-center">
                <div class="container-fluid">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <div class="openweather-component d-flex justify-content-center align-items-center">
                                <img class="openweather-icon" src="" alt=""/>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="openweather-component d-flex flex-column justify-content-center align-items-center">
                                <div class="openweather-location"></div>
                                <div class="openweather-details text-nowrap"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>';

	return $content . $weather_output;
}