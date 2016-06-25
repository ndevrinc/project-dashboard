<?php
/**
 * Plugin Name:     Project Dashboard
 * Description:     Project pages for clients and team
 * Author:          Ndevr, Inc.
 * Author URI:      https://ndevr.io
 * Text Domain:     project-dashboard
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Project_Dashboard
 */

require_once( __DIR__ . "/classes/install.php" );
require_once( __DIR__ . "/classes/settings-page.php" );
require_once( __DIR__ . "/classes/projects-define.php" );
require_once( __DIR__ . "/classes/projects.php" );
require_once( __DIR__ . "/classes/builds-define.php" );
//require_once( __DIR__ . "/classes/uninstall.php" );

Project_Dashboard\Install::get_instance();
Project_Dashboard\Projects_Define::get_instance();


add_action( 'rest_api_init', function() {
	/**
	 * Registers the custom endpoint routes
	 */
	require_once( __DIR__ . "/classes/projects-controller.php" );
	$project_controller = new Project_Dashboard\Projects_Controller();
	$project_controller->register_routes();

	// Retrieve settings to determine what libraries / functionality is enabled
	$project_dashboard_settings = Project_Dashboard\Settings_Page::get_instance();
	$project_dashboard_fields = maybe_unserialize( get_option( 'project_dashboard_fields', $project_dashboard_settings->get_defaults() ) );

	// Add Builds functionality only if enabled in settings
	if ( $project_dashboard_fields['builds']['enabled'] ) {
		Project_Dashboard\Builds_Define::get_instance();

		require_once( __DIR__ . "/classes/builds-controller.php" );
		$controller = new Project_Dashboard\Builds_Controller();
		$controller->register_routes();
	}
} );
