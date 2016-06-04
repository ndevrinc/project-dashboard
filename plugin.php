<?php
/**
 * Plugin Name:     Client Portal
 * Description:     Client portal pages for dashboard
 * Author:          Ndevr, Inc.
 * Author URI:      https://ndevr.io
 * Text Domain:     client-portal
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Client_Portal
 */

require_once( __DIR__ . "/classes/install.php" );
require_once( __DIR__ . "/classes/projects-define.php" );
require_once( __DIR__ . "/classes/projects.php" );

Client_Portal\Install::get_instance();
Client_Portal\Projects_Define::get_instance();
