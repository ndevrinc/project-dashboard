<?php
/*
 *   Singleton classes
 */
namespace Client_Portal;

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
	 * @return  Foo A single instance of this class.
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

			$slug     = 'cp_project';
			$singular = 'Project';
			$plural   = 'Projects';
			$rewrite  = array( 'slug' => 'clients/projects' );

			$fm = new Fieldmanager_Group( array(
				'name'           => 'custom_projects',
				'serialize_data' => false,
				'children'       => array(
					'id_external'      => new \Fieldmanager_TextField( array(
						'label'          => __( 'External ID', 'client-portal' ),
						'serialize_data' => false,
					) ),
					'api_key'      => new \Fieldmanager_TextField( array(
						'label'          => __( 'API Key', 'client-portal' ),
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
						'add_more_label' => __( 'Add Project Manager', 'client-portal' ),
						'limit'          => 0,
						'label'          => __( 'Project Managers', 'client-portal' ),
						'datasource'     => new \Fieldmanager_Datasource_User,
						'serialize_data' => false,
					) ),
					'project_members'  => new \Fieldmanager_Autocomplete( array(
						'add_more_label' => __( 'Add Project Members', 'client-portal' ),
						'limit'          => 0,
						'label'          => __( 'Project Members', 'client-portal' ),
						'datasource'     => new \Fieldmanager_Datasource_User,
						'serialize_data' => false,
					) ),
				)
			) );

			$labels = array(
				'name'               => _x( $plural, 'post type general name', 'client-portal' ),
				'singular_name'      => _x( $singular, 'post type singular name', 'client-portal' ),
				'menu_name'          => _x( $plural, 'admin menu', 'client-portal' ),
				'name_admin_bar'     => _x( $singular, 'add new on admin bar', 'client-portal' ),
				'add_new'            => _x( 'Add New', $slug, 'client-portal' ),
				'add_new_item'       => __( 'Create a ' . $singular, 'client-portal' ),
				'new_item'           => __( 'New ' . $singular, 'client-portal' ),
				'edit_item'          => __( 'Edit Your ' . $singular, 'client-portal' ),
				'view_item'          => __( 'View ' . $singular, 'client-portal' ),
				'all_items'          => __( 'All ' . $plural, 'client-portal' ),
				'search_items'       => __( 'Search ' . $plural, 'client-portal' ),
				'parent_item_colon'  => __( 'Parent ' . $plural . ':', 'client-portal' ),
				'not_found'          => __( 'No ' . strtolower( $plural ) . ' found.', 'client-portal' ),
				'not_found_in_trash' => __( 'No ' . strtolower( $plural ) . ' found in Trash.', 'client-portal' ),
				'title_label'        => __( 'Name Your ' . $singular, 'client-portal' ),
				'description_label'  => __( 'Description', 'client-portal' ),
				'edit_submit_label'  => __( 'Update Settings', 'client-portal' ),
			);
			$args   = array(
				'labels'                => $labels,
				'public'                => false,
				'menu_position'         => 1,
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
				'show_in_menu'          => true,
				'show_in_admin_bar'     => true,
				'field_manager'         => $fm,
			);

			\register_post_type( $slug, $args );

			$fm->add_meta_box( __( 'Other Information', 'client-portal' ), $slug );

		}
	}
}
