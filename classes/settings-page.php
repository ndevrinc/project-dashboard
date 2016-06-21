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
	private static $instance = null;

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
			self::$instance->setup_actions();
		}

		return self::$instance;

	} // end get_instance;

	private function setup_actions() {
		add_action( 'admin_menu', array( $this, 'get_page_hook' ) );
		add_action( 'admin_init', array( $this, 'display_settings_fields' ) );
	}

// Adds the Settings sub menu
	public function get_page_hook() {
		add_menu_page(
			__( 'Project Dashboard', 'project-dashboard' ),
			__( 'Project Dashboard', 'project-dashboard' ),
			'manage_options',
			'project_dashboard',
			'',
			'dashicons-analytics',
			3
		);
		add_submenu_page(
			'project_dashboard',
			__( 'Dashboard', 'project-dashboard' ),
			__( 'Dashboard', 'project-dashboard' ),
			'manage_options',
			'pd_display',
			array( $this, 'project_dashboard_page_callback' )
		);
		add_submenu_page(
			'project_dashboard',
			__( 'Settings', 'project-dashboard' ),
			__( 'Settings', 'project-dashboard' ),
			'manage_options',
			'pd_settings',
			array( $this, 'project_dashboard_settings_callback' )
		);
	}

	/**
	 * project_dashboard_page_callback
	 */
	public function project_dashboard_page_callback() { ?>
		<h1><?php esc_html_e( 'Project Dashboard', 'project-dashboard' ); ?></h1>
		<div>TO Come......</div>

		<?php
	}

	/**
	 *    public function project_dashboard_settings_callback
	 */
	public function project_dashboard_settings_callback() {
		if ( ! isset( $_REQUEST['settings-updated'] ) ) {
			$_REQUEST['settings-updated'] = false;
		} ?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Project Dashboard Settings', 'project-dashboard' ); ?></h1>

			<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
				<div class="updated fade"><p>
						<strong><?php _e( 'Project Dashboard Options saved!', 'project-dashboard' ); ?></strong></p>
				</div>
			<?php endif; ?>

			<div id="poststuff">
				<div id="post-body">
					<div id="post-body-content">
						<form method="post" action="options.php">
							<?php
							settings_fields( 'pd_section' );
							do_settings_sections( 'project_dashboard' );
							submit_button();
							?>
						</form>
					</div> <!-- end post-body-content -->
				</div> <!-- end post-body -->
			</div> <!-- end poststuff -->
		</div>

		<?php
	}

	/**
	 * @return void
	 */
	function display_settings_fields() {
		add_settings_section( 'pd_section', esc_html__( 'Build Settings', 'project-dashboard' ), null, 'project_dashboard' );

		add_settings_field( 'project_dashboard_fields', esc_html__( 'Builds', 'project-dashboard' ), array(
			$this,
			'builds_form'
		), 'project_dashboard', 'pd_section' );

		register_setting( 'pd_section', 'project_dashboard_fields', array( $this, 'sanitize_fields' ) );
	}

	public function sanitize_fields() {
		if ( isset( $_REQUEST['project_dashboard_fields'] ) ) {
			$_REQUEST['project_dashboard_fields']['builds']['enabled'] = ( $_REQUEST['project_dashboard_fields']['builds']['enabled'] == '1' );
			return maybe_serialize( $_REQUEST['project_dashboard_fields'] );
		}
	}

	/**
	 *
	 */
	public function builds_form() {
		$project_dashboard_fields = maybe_unserialize( get_option( 'project_dashboard_fields', $this->get_defaults() ) );
		 ?>
		<select name="project_dashboard_fields[builds][enabled]" id="pd-builds">
			<?php $selected = $project_dashboard_fields['builds']['enabled']; ?>
			<option value="1" <?php selected( $selected, true ); ?> >Enabled</option>
			<option value="0" <?php selected( $selected, false ); ?> >Disabled</option>
		</select><br/>
		<label class="description"
		       for="project_dashboard_fields[builds][enabled]"><?php _e( 'Toggles whether or not to enable builds.', 'project-dashboard' ); ?></label>
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
}