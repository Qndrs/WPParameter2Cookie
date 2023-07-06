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
Version: 2.4
*/
// Add a panel to the administration area, settings panel
add_action('admin_menu', 'wp_param_to_cookie_add_admin_panel');

function wp_param_to_cookie_add_admin_panel() {
	add_options_page(
		'WP Param to Cookie Settings',
		'WP Param to Cookie',
		'manage_options',
		'wp_param_to_cookie_settings',
		'wp_param_to_cookie_render_admin_panel',
		'dashicons-admin-generic'
	);
}

// Render the admin panel
function wp_param_to_cookie_render_admin_panel() {
	// Fetch data from the wp_param_to_cookie_data table
	$data = wp_param_to_cookie_get_data();
	?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
			<?php settings_fields('wp_param_to_cookie_settings_group'); ?>
			<?php do_settings_sections('wp_param_to_cookie_settings_group'); ?>
            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <p>Shortcode: <span style="font-weight: bolder">[wp_param_to_cookie ]</span><br>
                            Shortcode functies zijn:<br>
                            Een read parameter om ingestelde cookie(s) te lezen en weer te geven als json. [wp_param_to_cookie read="on"]<br>
                            Een rapportage shortcode parameter functie. De parameters report = "on" (default off) and format = "txt" | "json" (default json) Bijvoorbeeld: [wp_param_to_cookie report="on" format="txt"]</p>
                    </th>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%">
                        <label for="wp_param_to_cookie_variable">Welke parameter(s) moeten in een cookie? Kommagescheiden lijst wanneer meer dan 1.</label>
                    </th>
                    <td>
                        <input type="text" id="wp_param_to_cookie_variable" name="wp_param_to_cookie_variable" value="<?php echo esc_attr(get_option('wp_param_to_cookie_variable')); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row" style="width: 30%">
                        <label for="wp_param_to_cookie_time">Hoe lang moet het cookie bewaard blijven? In seconden. (3600 = 1 uur)</label>
                    </th>
                    <td>
                        <input type="number" id="wp_param_to_cookie_time" name="wp_param_to_cookie_time" value="<?php echo esc_attr(get_option('wp_param_to_cookie_time')); ?>">
                    </td>
                </tr>
            </table>
			<?php submit_button(); ?>
        </form>
    </div>
	<?php
	// Add a new section to display the data in a table
	?>
    <h2>Data from wp_param_to_cookie_data table</h2>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Handle export data button click
            $('#export-button').click(function() {
                $.ajax({
                    url: ajaxurl,  // this is a variable that WordPress has already defined for us
                    data: { 'action': 'export_data' },
                    success:function(data) {
                        // Create a blob and a link to download it
                        var blob = new Blob([data], {type: 'application/json'});
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "data.json";
                        link.click();
                    }
                });
            });
            // Handle delete all data button click
            $('#delete-button').click(function() {
                if (confirm('Are you sure you want to delete all data?')) {
                    $.ajax({
                        url: ajaxurl,
                        data: { 'action': 'delete_all_data' },
                        success:function(data) {
                            alert(data);
                            location.reload();  // Reload page to update the data table
                        }
                    });
                }
            });

        });
    </script>

    <button id="export-button" class="button button-primary">Export Data als JSON</button>
    <button id="delete-button" class="button button-primary">Delete alle records</button>
    <table id="wp_param_to_cookie_data_table" class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th>Cookie Name</th>
            <th>Cookie Value</th>
            <th>IP Address</th>
            <th>Hostname</th>
            <th>Date Created</th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($data as $row) : ?>
            <tr>
                <td><?php echo $row['cookie_name']; ?></td>
                <td><?php echo $row['cookie_value']; ?></td>
                <td><?php echo $row['ip_address']; ?></td>
                <td><?php echo $row['hostname']; ?></td>
                <td><?php echo $row['date_created']; ?></td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
	<?php
}

// Register settings
add_action('admin_init', 'wp_param_to_cookie_register_settings');

function wp_param_to_cookie_register_settings() {
	register_setting(
		'wp_param_to_cookie_settings_group',
		'wp_param_to_cookie_variable',
		array(
			'type'              => 'string',
			'description'       => 'Parameter list',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => false,
		)
	);
	register_setting(
		'wp_param_to_cookie_settings_group',
		'wp_param_to_cookie_time',
		array(
			'type'              => 'integer',
			'description'       => 'Cookie expiry time',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => false,
		)
	);
}
// make sure the cookie setting is done before output is send
add_action( 'wp_loaded', 'wp_param_to_cookie_check_shortcode' );
// check if shortcode is in page
function wp_param_to_cookie_check_shortcode() {
//     if (is_singular() && has_shortcode($post->post_content, 'wp_param_to_cookie')) {
	wp_param_to_cookie_function('off');
//     }
// disabled check since Avada blocks the post_content check. TODO: fix in next version
}

// create a database table when the plugin is activated
function wp_param_to_cookie_create_db_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wp_param_to_cookie_data';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cookie_name VARCHAR(255) NOT NULL,
        cookie_value VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        hostname VARCHAR(255) NOT NULL,
        date_created DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

// Create the table when the plugin is activated
register_activation_hook(__FILE__, 'wp_param_to_cookie_create_db_table');

// Read the table data for display in a table
function wp_param_to_cookie_get_data() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wp_param_to_cookie_data';
	$results = $wpdb->get_results("SELECT DISTINCT cookie_name, cookie_value, ip_address, hostname, date_created FROM $table_name", ARRAY_A);
	return $results;
}


// function that explodes the param string and walks through it
function wp_param_to_cookie_function($readonly = 'off'): Array {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wp_param_to_cookie_data';

	$wp_param = get_option('wp_param_to_cookie_variable');
	$a_wp_param = explode(',', $wp_param);
	$wp_param_time = get_option('wp_param_to_cookie_time');
	$a_cookiesset = array();
//    if($readonly == 'off') { // TODO: possible switch moment
	// Get IP address and hostname
	$ip_address = $_SERVER['REMOTE_ADDR'];
	$hostname   = gethostbyaddr( $ip_address );

	foreach ( $a_wp_param as $wp_param_key => $wp_param_value ) {
		if (
			isset( $_REQUEST[ $wp_param_value ] )
			and ! empty( $_REQUEST[ $wp_param_value ] )
		) {
			setcookie(
				$wp_param_value,
				$_REQUEST[ $wp_param_value ],
				time() + (int) $wp_param_time,
				COOKIEPATH, COOKIE_DOMAIN
			);
			setcookie("koekje", "gezet", time()+3600,COOKIEPATH, COOKIE_DOMAIN);
			$a_cookiesset[ $wp_param_value ] = $_REQUEST[ $wp_param_value ];

			// Store cookie in the database
			$wpdb->insert(
				$table_name,
				array(
					'cookie_name'  => $wp_param_value,
					'cookie_value' => $_REQUEST[ $wp_param_value ],
					'ip_address'   => $ip_address,
					'hostname'     => $hostname,
				)
				,
				array( '%s', '%s', '%s', '%s' )
			);
		}
	}
//    }
	return $a_cookiesset;
}


function wp_param_to_cookie_read_function():Array {
	$wp_param = get_option('wp_param_to_cookie_variable');
	$a_wp_param = explode(',', $wp_param) ;
	$a_cookiesset = array();
	foreach($a_wp_param as $wp_param_key => $wp_param_value) {
		if (
			isset($_COOKIE[$wp_param_value])
			and !empty($_COOKIE[$wp_param_value])
		) {
			$a_cookiesset[$wp_param_value] = $_COOKIE[$wp_param_value] ;
		}
	}
	return $a_cookiesset ;
}

function wp_param_to_cookie_shortcode_function($atts):String {
	$message = '';
	$atts = shortcode_atts( array(
		'report' => 'off',
		'format' => 'json',
		'read'   => 'off'
	), $atts );
	$report = $atts['report'] ; // on or off. Default: off
	$format = $atts['format'] ; // txt or json. Default: json
	$read   = $atts['read'] ; // displays the possible set cookies by the plugin. Overwrites report option.
	$result = wp_param_to_cookie_function('on'); //
	if($report == 'on' AND $format == 'txt'){
		foreach ($result as $key => $value){
			$message .= 'Cookie name: ' . $key . ' with value: ' . $value . '<br>' ;
		}
	} elseif($report == 'on' AND $format == 'json') {
		$message .= '<pre>' . json_encode($result) . '</pre>';
	}
	// just read the cookie(s)
	if($read == 'on'){
		$result = wp_param_to_cookie_read_function();
		$message = json_encode($result);
	}
	return $message;
}
add_shortcode( 'wp_param_to_cookie', 'wp_param_to_cookie_shortcode_function' );

// enqueue the reporting table sorting scripts to WordPress
function wp_param_to_cookie_enqueue_scripts($hook) {
	if ('settings_page_wp_param_to_cookie_settings' !== $hook) {
		return;
	}

	// Enqueue sorttable.js
	wp_enqueue_script('sorttable', plugin_dir_url(__FILE__) . 'sorttable.js');

	// Enqueue wp_param_to_cookie_admin.js
	wp_enqueue_script('wp_param_to_cookie_admin', plugin_dir_url(__FILE__) . 'wp_param_to_cookie_admin.js', array('sorttable'), '1.0.0', true);
}

add_action('admin_enqueue_scripts', 'wp_param_to_cookie_enqueue_scripts');

// export and delete
// AJAX action to export data
add_action('wp_ajax_export_data', 'export_data');

function export_data() {
	global $wpdb;

	// Fetch data from the wp_param_to_cookie_data table
	$table_name = $wpdb->prefix . 'wp_param_to_cookie_data';
	$data = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

	// Convert data to JSON
	$json = json_encode($data);

	// Send JSON data
	echo $json;

	wp_die(); // this is required to terminate immediately and return a proper response
}

// AJAX action to delete all data
add_action('wp_ajax_delete_all_data', 'delete_all_data');

function delete_all_data() {
	global $wpdb;

	// Fetch data from the wp_param_to_cookie_data table
	$table_name = $wpdb->prefix . 'wp_param_to_cookie_data';
	$wpdb->query("TRUNCATE TABLE $table_name");

	echo 'Data deleted';

	wp_die(); // this is required to terminate immediately and return a proper response
}

