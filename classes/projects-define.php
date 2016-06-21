<?php
/*
 *   Singleton classes
 */
namespace Project_Dashboard;

use Fieldmanager_Datepicker;
use Fieldmanager_Group;
use Fieldmanager_TextField;

class Projects_Define {
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
		add_action( 'init', array( $this, 'define_post_type' ) );
	}

	/**
	 * Sets up the custom post type: projects
	 */
	public function define_post_type() {

		if ( ! isset( self::$_instance ) ) {

			$slug     = 'pd_project';
			$singular = 'Project';
			$plural   = 'Projects';
			$rewrite  = array( 'slug' => 'projects/project' );

			$fm = new Fieldmanager_Group( array(
				'name'           => 'pd_project',
				'serialize_data' => false,
				'children'       => array(
					'id_external'      => new \Fieldmanager_TextField( array(
						'label'          => __( 'External ID', 'project-dashboard' ),
						'serialize_data' => false,
					) ),
					'api_key'      => new \Fieldmanager_TextField( array(
						'label'          => __( 'API Key', 'project-dashboard' ),
						'serialize_data' => false,
					) ),
					'start_date'       => new \Fieldmanager_Datepicker( array(
						'label'          => __( 'Start Date', 'pmc-goldderby' ),
						'serialize_data' => false,
					) ),
					'end_date'         => new \Fieldmanager_Datepicker( array(
						'label'          => __( 'End Date', 'pmc-goldderby' ),
						'serialize_data' => false,
					) ),
					'project_managers' => new \Fieldmanager_Autocomplete( array(
						'add_more_label' => __( 'Add Project Manager', 'project-dashboard' ),
						'limit'          => 0,
						'label'          => __( 'Project Managers', 'project-dashboard' ),
						'datasource'     => new \Fieldmanager_Datasource_User,
						'serialize_data' => false,
					) ),
					'project_members'  => new \Fieldmanager_Autocomplete( array(
						'add_more_label' => __( 'Add Project Members', 'project-dashboard' ),
						'limit'          => 0,
						'label'          => __( 'Project Members', 'project-dashboard' ),
						'datasource'     => new \Fieldmanager_Datasource_User,
						'serialize_data' => false,
					) ),
				)
			) );

			$labels = array(
				'name'               => _x( $plural, 'post type general name', 'project-dashboard' ),
				'singular_name'      => _x( $singular, 'post type singular name', 'project-dashboard' ),
				'menu_name'          => _x( $plural, 'admin menu', 'project-dashboard' ),
				'name_admin_bar'     => _x( $singular, 'add new on admin bar', 'project-dashboard' ),
				'add_new'            => _x( 'Add New', $slug, 'project-dashboard' ),
				'add_new_item'       => __( 'Create a ' . $singular, 'project-dashboard' ),
				'new_item'           => __( 'New ' . $singular, 'project-dashboard' ),
				'edit_item'          => __( 'Edit Your ' . $singular, 'project-dashboard' ),
				'view_item'          => __( 'View ' . $singular, 'project-dashboard' ),
				'all_items'          => __( '' . $plural, 'project-dashboard' ),
				'search_items'       => __( 'Search ' . $plural, 'project-dashboard' ),
				'parent_item_colon'  => __( 'Parent ' . $plural . ':', 'project-dashboard' ),
				'not_found'          => __( 'No ' . strtolower( $plural ) . ' found.', 'project-dashboard' ),
				'not_found_in_trash' => __( 'No ' . strtolower( $plural ) . ' found in Trash.', 'project-dashboard' ),
				'title_label'        => __( 'Name Your ' . $singular, 'project-dashboard' ),
				'description_label'  => __( 'Description', 'project-dashboard' ),
				'edit_submit_label'  => __( 'Update Settings', 'project-dashboard' ),
			);
			$args   = array(
				'labels'                => $labels,
				'public'                => false,
				'menu_position'         => 10,
				'supports'              => array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'revisions'
				),
				'menu_icon'             => 'dashicons-groups',
				'has_archive'           => true,
				'rewrite'               => $rewrite,
				'show_ui'               => true,
				'show_in_menu'          => 'project_dashboard',
				'show_in_admin_bar'     => true,
				'field_manager'         => $fm,
			);

			\register_post_type( $slug, $args );

			$fm->add_meta_box( __( 'Other Information', 'project-dashboard' ), $slug );

		}
	}
}
