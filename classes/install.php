<?php
/*
 *   Singleton classes
 */
namespace Project_Dashboard;

class Install {
	/*--------------------------------------------*
	 * Attributes
	 *--------------------------------------------*/

	/** Refers to a single instance of this class. */
	private static $instance = NULL;

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return  A single instance of this class.
	 */
	public static function get_instance() {

		if ( NULL == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	private function __construct() {
		register_activation_hook( 'project-dashboard/plugin.php', array( $this, 'dependent_plugin' ) );
	}

	public function dependent_plugin() {
		if ( \is_admin() && \current_user_can( 'activate_plugins' ) && (
				! \is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) )
		) {
			add_action( 'admin_notices', array( $this, 'dependent_plugin_notice' ) );

			deactivate_plugins( plugin_basename( 'project-dashboard/plugin.php' ) );
			echo 'Sorry, but this plugin requires the ACF plugin 
				to be installed and active.';
			@trigger_error(__('Please enable required plugins first', 'project-dashboard'), E_USER_ERROR);

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}

}
