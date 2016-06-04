<?php
/*
 *   Singleton classes
 */
namespace Client_Portal;

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
	 * @return  Foo A single instance of this class.
	 */
	public static function get_instance() {

		if ( NULL == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	} // end get_instance;

	private function __construct() {
		register_activation_hook( 'client-portal/plugin.php', array( $this, 'client_builds_install' ) );
	}

	public function dependent_plugin() {
		if ( \is_admin() && \current_user_can( 'activate_plugins' ) && (
				! \is_plugin_active( 'rest-api/plugin.php' ) ||
				! \is_plugin_active( 'fieldmanager/fieldmanager.php' ) )
		) {
			add_action( 'admin_notices', array( $this, 'dependent_plugin_notice' ) );

			deactivate_plugins( plugin_basename( 'client-portal/plugin.php' ) );
			echo 'Sorry, but this plugin requires the WP REST API (v2) and Fieldmanager plugins 
				to be installed and active.';
			@trigger_error(__('Please enable required plugins first', 'client-portal'), E_USER_ERROR);

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}

	public function dependent_plugin_notice() {
		?>
		<?php
	}

	public function client_builds_install() {
		$this->dependent_plugin();
		global $wpdb;
		global $cp_db_version;
		$cp_db_version = get_option( 'cp_db_version', 1 );

		$table_name = $wpdb->prefix . 'cp_builds';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		id_project mediumint(9) NOT NULL,
		ci_name tinytext NOT NULL,
		ci_message varchar(255),
		committer_username tinytext,
		environment tinytext NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00',
		status tinytext,
		build_time int DEFAULT 0,
		frontend_time int DEFAULT 0,
		backend_time int DEFAULT 0,
		browserstack_job_id varchar(1024) DEFAULT '',
		build_url varchar(1024) DEFAULT '',
		pullrequest_url varchar(1024) DEFAULT '',
		UNIQUE KEY id (id)
	) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		add_option( 'cp_db_version', $cp_db_version );
	}
}
