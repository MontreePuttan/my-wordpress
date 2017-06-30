<?php
/**
 * Copyright (C) 2014-2015 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wmde_Main_Controller {

	/**
	 * Main Application Controller
	 *
	 * @return Ai1wmde_Main_Controller
	 */
	public function __construct() {
		register_activation_hook( AI1WMDE_PLUGIN_BASENAME, array( $this, 'activation_hook' ) );

		// Activate hooks
		$this->activate_actions()
			 ->activate_filters()
			 ->activate_textdomain();
	}

	/**
	 * Activation hook callback
	 *
	 * @return Object Instance of this class
	 */
	public function activation_hook() {

	}

	/**
	 * Initializes language domain for the plugin
	 *
	 * @return Object Instance of this class
	 */
	private function activate_textdomain() {
		load_plugin_textdomain( AI1WMDE_PLUGIN_NAME, false, false );

		return $this;
	}

	/**
	 * Register plugin menus
	 *
	 * @return void
	 */
	public function admin_menu() {
		// sublevel Export menu
		$export_page_hook_suffix = get_plugin_page_hookname( 'site-migration-export', 'site-migration-export' );
		add_action(
			'admin_print_scripts-' . $export_page_hook_suffix,
			array( $this, 'register_export_scripts_and_styles' )
		);

		// sublevel Import menu
		$import_page_hook_suffix = get_plugin_page_hookname( 'site-migration-import', 'site-migration-export' );
		add_action(
			'admin_print_scripts-' . $import_page_hook_suffix,
			array( $this, 'register_import_scripts_and_styles' )
		);

		// sublevel Settings menu
		$settings_page_hook_suffix = add_submenu_page(
			'site-migration-export',
			__( 'Dropbox Settings', AI1WMDE_PLUGIN_NAME ),
			__( 'Dropbox Settings', AI1WMDE_PLUGIN_NAME ),
			'export',
			'site-migration-dropbox-settings',
			'Ai1wmde_Settings_Controller::index'
		);
		add_action(
			'admin_print_scripts-' . $settings_page_hook_suffix,
			array( $this, 'register_settings_scripts_and_styles' )
		);
	}

	/**
	 * Register scripts and styles for Export Controller
	 *
	 * @return void
	 */
	public function register_export_scripts_and_styles() {
		wp_enqueue_script(
			'ai1wmde-js-export',
			Ai1wm_Template::asset_link( 'javascript/export.min.js', 'AI1WMDE' ),
			array( 'jquery' )
		);
	}

	/**
	 * Register scripts and styles for Import Controller
	 *
	 * @return void
	 */
	public function register_import_scripts_and_styles() {
		wp_enqueue_style(
			'ai1wmde-css-import',
			Ai1wm_Template::asset_link( 'css/import.min.css', 'AI1WMDE' )
		);
		wp_enqueue_script(
			'ai1wmde-js-import',
			Ai1wm_Template::asset_link( 'javascript/import.min.js', 'AI1WMDE' ),
			array( 'jquery' )
		);
		wp_localize_script( 'ai1wmde-js-import', 'ai1wmde_import', array(
			'token' => get_option( 'ai1wmde_dropbox_token' ),
			'ajax'  => array(
				'metadata_url'  => admin_url( 'admin-ajax.php?action=ai1wmde_dropbox_metadata' ),
			),
		) );
	}

	/**
	 * Register scripts and styles for Settings Controller
	 *
	 * @return void
	 */
	public function register_settings_scripts_and_styles() {
		wp_enqueue_script(
			'ai1wmde-js-settings',
			Ai1wm_Template::asset_link( 'javascript/settings.min.js', 'AI1WMDE' ),
			array( 'jquery' )
		);
		wp_enqueue_style(
			'ai1wm-css-export',
			Ai1wm_Template::asset_link( 'css/export.min.css' )
		);
		wp_enqueue_style(
			'ai1wmde-css-settings',
			Ai1wm_Template::asset_link( 'css/settings.min.css', 'AI1WMDE' )
		);
		wp_localize_script( 'ai1wmde-js-settings', 'ai1wmde_settings', array(
			'token' => get_option( 'ai1wmde_dropbox_token' ),
			'ajax'  => array(
				'account_url' => admin_url( 'admin-ajax.php?action=ai1wmde_dropbox_account' ),
			),
		) );
		wp_localize_script( 'ai1wmde-js-settings', 'ai1wm_feedback', array(
			'ajax' => array(
				'url' => admin_url( 'admin-ajax.php?action=ai1wm_feedback' ),
			),
		) );
	}

	/**
	 * Outputs menu icon between head tags
	 *
	 * @return void
	 */
	public function admin_head() {
		?>
		<style type="text/css" media="all">
			.ai1wm-label {
				border: 1px solid #5cb85c;
				background-color: transparent;
				color: #5cb85c;
				cursor: pointer;
				text-transform: uppercase;
				font-weight: 600;
				outline: none;
				transition: background-color 0.2s ease-out;
				padding: .2em .6em;
				font-size: 0.8em;
				border-radius: 5px;
				text-decoration: none !important;
			}

			.ai1wm-label:hover {
				background-color: #5cb85c;
				color: #fff;
			}
		</style>
	<?php
	}

	/**
	 * Add custom cron schedules
	 *
	 * @param  array $schedules List of schedules
	 * @return array
	 */
	public function add_cron_schedules( $schedules ) {
		$schedules['weekly'] = array(
			'display'  => __( 'Weekly', AI1WMDE_PLUGIN_NAME ),
			'interval' => 60 * 60 * 24 * 7,
		);
		$schedules['monthly'] = array(
			'display'  => __( 'Monthly', AI1WMDE_PLUGIN_NAME ),
			'interval' => 60 * 60 * 24 * 7 * 31,
		);

		return $schedules;
	}

	/**
	 * Register listeners for actions
	 *
	 * @return Object Instance of this class
	 */
	private function activate_actions() {
		// Init
		add_action( 'admin_init', array( $this, 'init' ) );

		// Router
		add_action( 'admin_init', array( $this, 'router' ) );

		// Admin header
		add_action( 'admin_head', array( $this, 'admin_head' ) );

		// All in One WP Migration
		add_action( 'plugins_loaded', array( $this, 'ai1wm_loaded' ), 10 );

		// Export and import commands
		add_action( 'plugins_loaded', array( $this, 'ai1wm_commands' ), 20 );

		return $this;
	}

	/**
	 * Register listeners for filters
	 *
	 * @return Object Instance of this class
	 */
	private function activate_filters() {
		// Add custom schedules
		add_filter( 'cron_schedules', array( $this, 'add_cron_schedules' ) );

		return $this;
	}

	/**
	 * Export and import commands
	 *
	 * @return void
	 */
	public function ai1wm_commands() {
		if ( isset( $_REQUEST['dropbox'] ) ) {
			// Add export commands
			add_filter( 'ai1wm_export', 'Ai1wmde_Export_Dropbox::execute', 250 );
			add_filter( 'ai1wm_export', 'Ai1wmde_Export_Upload::execute', 260 );
			add_filter( 'ai1wm_export', 'Ai1wmde_Export_Clean::execute', 270 );

			// Add import commands
			add_filter( 'ai1wm_import', 'Ai1wmde_Import_Dropbox::execute', 20 );
			add_filter( 'ai1wm_import', 'Ai1wmde_Import_Download::execute', 30 );

			// Remove export commands
			remove_filter( 'ai1wm_export', 'Ai1wm_Export_Download::execute', 250 );

			// Remove import commands
			remove_filter( 'ai1wm_import', 'Ai1wm_Import_Upload::execute', 5 );
		}
	}

	/**
	 * Check whether All in one WP Migration has been loaded
	 *
	 * @return void
	 */
	public function ai1wm_loaded() {
		if ( ! defined( 'AI1WM_PLUGIN_NAME' ) ) {
			if ( is_multisite() ) {
				add_action( 'network_admin_notices', array( $this, 'ai1wm_notice' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'ai1wm_notice' ) );
			}
		} else {
			if ( is_multisite() ) {
				add_action( 'network_admin_menu', array( $this, 'admin_menu' ), 20 );
			} else {
				add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
			}

			// Cron settings
			add_action( 'ai1wmde_dropbox_hourly_export', 'Ai1wm_Export_Controller::export' );
			add_action( 'ai1wmde_dropbox_daily_export', 'Ai1wm_Export_Controller::export' );
			add_action( 'ai1wmde_dropbox_weekly_export', 'Ai1wm_Export_Controller::export' );
			add_action( 'ai1wmde_dropbox_monthly_export', 'Ai1wm_Export_Controller::export' );

			// Picker
			add_action( 'ai1wm_import_left_end', 'Ai1wmde_Import_Controller::picker' );

			// Add export button
			add_filter( 'ai1wm_export_dropbox', 'Ai1wmde_Export_Controller::button' );

			// Add import button
			add_filter( 'ai1wm_import_dropbox', 'Ai1wmde_Import_Controller::button' );

			// Add import unlimited
			add_filter( 'ai1wm_max_file_size', array( $this, 'max_file_size' ) );
		}
	}

	/**
	 * Display All in one WP Migration notice
	 *
	 * @return void
	 */
	public function ai1wm_notice() {
		?>
		<div class="error">
			<p>
				<?php
				_e(
					'All in One WP Migration is not activated. Please activate the plugin in order to use Dropbox extension.',
					AI1WMDE_PLUGIN_NAME
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Max file size callback
	 *
	 * @return string
	 */
	public function max_file_size() {
		return AI1WMDE_MAX_FILE_SIZE;
	}

	/**
	 * Register initial parameters
	 *
	 * @return void
	 */
	public function init() {
		// Set Dropbox Token
		if ( isset( $_GET['ai1wmde-token'] ) ) {
			update_option( 'ai1wmde_dropbox_token', $_GET['ai1wmde-token'] );

			// Redirect
			wp_redirect( network_admin_url( 'admin.php?page=site-migration-dropbox-settings' ) );
			exit;
		}

		// Set Purchase ID
		if ( ! get_option( 'ai1wmde_plugin_key' ) ) {
			update_option( 'ai1wmde_plugin_key', AI1WMDE_PURCHASE_ID );
		}
	}

	/**
	 * Register initial router
	 *
	 * @return void
	 */
	public function router() {
		// Export
		if ( current_user_can( 'export' ) ) {
			add_action( 'wp_ajax_ai1wmde_dropbox_account', 'Ai1wmde_Settings_Controller::account' );
		}

		// Import
		if ( current_user_can( 'import' ) ) {
			add_action( 'wp_ajax_ai1wmde_dropbox_metadata', 'Ai1wmde_Import_Controller::metadata' );
		}
	}
}
