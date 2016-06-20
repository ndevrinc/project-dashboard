<?php
/*
 *   Singleton classes
 */
namespace Project_Dashboard;

class Settings_Page {
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
	 * @return A single instance of this class.
	 */
	public static function get_instance() {

		if ( NULL == self::$instance ) {
			self::$instance = new self;
			self::$instance->setup_actions();
		}

		return self::$instance;

	} // end get_instance;

	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'get_page_hook' ) );

	}

	// Adds the Settings sub menu
	public function get_page_hook() {
		add_menu_page(
			__( 'Project Dashboard', 'project-dashboard' ),
			__( 'Project Dashboard', 'project-dashboard' ),
			'manage_options',
			'project_dashboard',
			'',
			'dashicons-chart-pie',
			3
		);
		add_submenu_page(
			'project_dashboard',
			__( 'Settings', 'project-dashboard' ),
			__( 'Settings', 'project-dashboard' ),
			'manage_options',
			'pd_settings',
			array( $this, 'project_dashboard_page_callback' )
		);
	}

	/**
	 *
	 */
	public function project_dashboard_page_callback() { ?>
		<h1><?php esc_html_e( 'Project Dashboard Settings', 'project-dashboard' );?></h1>
		<div>TO Come......</div>

	<?php
	}

	/**
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'builds' => array(
				'enabled' => true,
			)
		);
	}
	/**
	 * Setups up the Project Dashboard Settings form
	 *
	 * @return \Fieldmanager_Group
	 */
	public function register_project_dashboard_fields() {

		$ctas_fields = new \Fieldmanager_Group( esc_html__( 'Project Dashboard Settings', 'project-dashboard' ), array(
			'name'     => 'project_dashboard_fields',
			'sortable' => false,
			'children' => array(
				'builds' => new \Fieldmanager_Group( esc_html__( 'Builds', 'project-dashboard' ), array(
					'name'           => 'builds',
					'sortable'       => false,
					'limit'          => 1,
					'extra_elements' => 0,
					'children'       => array(
						'enabled' => new \Fieldmanager_Checkbox( esc_html__( 'Enable Builds', 'project-dashboard' ), array(
							'default_value' => true,
						) ),
					)
				) ),
			)

		) );

		// Allow the submodules or other plugins to add to the settings fields
		$ctas_fields = apply_filters( 'project_dashboard_settings', $ctas_fields );

		return $ctas_fields;
	}



}
