<?php
/*
Plugin Name: WP Parameter naar Cookie
Plugin URI: https://trnspt.nl
Description: Een eenvoudige plugin die de aangegeven parameter(s) opslaat in een cookie. De pagina die de parameter(s) ontvangt heeft een shortcode nodig. De instellingen staan onder het WordPress instellingen menu.
Requires at least: WP 6
Author: Qndrs
Author URI: qndrs.training
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Version: 1.0
*/

// Add a panel to the administration area, settings panel
add_action('admin_menu', 'wp_param_to_cookie_add_admin_panel');

function wp_param_to_cookie_add_admin_panel() {
    add_options_page(
        'WP Param to Cookie Settings',
        'WP Param to Cookie',
        'manage_options',
        'wp_param_to_cookie-settings',
        'wp_param_to_cookie_render_admin_panel',
        'dashicons-admin-generic'
    );
}

// Render the admin panel
function wp_param_to_cookie_render_admin_panel() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('wp_param_to_cookie-settings-group'); ?>
            <?php do_settings_sections('wp_param_to_cookie-settings-group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row" style="width: 30%">
                        <label for="wp_param_to_cookie-variable">Welke parameter(s) moeten in een cookie? Kommagescheiden lijst wanneer meer dan 1.<br>Shortcode: [wp_param_to_cookie ]</label>
                    </th>
                    <td>
                        <input type="text" id="wp_param_to_cookie-variable" name="wp_param_to_cookie_variable" value="<?php echo esc_attr(get_option('wp_param_to_cookie_variable')); ?>">
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'wp_param_to_cookie_register_settings');

function wp_param_to_cookie_register_settings() {
    register_setting(
        'wp_param_to_cookie-settings-group',
        'wp_param_to_cookie_variable',
        'sanitize_text_field'
    );
}
// make sure the cookie setting is done before output is send
add_action( 'init', 'wp_param_to_cookie_function' );
// function that explodes the param string and walks through it
function wp_param_to_cookie_function() {
    $wp_param = get_option('wp_param_to_cookie_variable');
    $a_wp_param = explode(',', $wp_param) ;
    foreach($a_wp_param as $wp_param_key => $wp_param_value) {
        if (
            isset($_REQUEST[$wp_param_value])
            and !empty($_REQUEST[$wp_param_value])
        ) {
            setcookie(
                $wp_param_value
                , $_REQUEST[$wp_param_value]
                , time() + 60 * 60 * 24 // what is a good time? TODO: setting in panel
                , COOKIEPATH, COOKIE_DOMAIN
            );
        }
    }
}

function wp_param_to_cookie_shortcode_function() {
    $result = wp_param_to_cookie_function(); 
    return $result; 
}
add_shortcode( 'wp_param_to_cookie', 'wp_param_to_cookie_shortcode_function' ); 