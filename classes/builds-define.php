<?php
/*
 *   Singleton classes
 */
namespace Project_Dashboard;

use Fieldmanager_Datepicker;
use Fieldmanager_Group;
use Fieldmanager_TextField;

class Builds_Define {
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

			$slug     = 'pd_build';
			$singular = 'Build';
			$plural   = 'Builds';
			$rewrite  = array( 'slug' => 'builds/build' );

			$fm = new Fieldmanager_Group( array(
				'name'           => 'pd_build',
				'serialize_data' => false,
				'children'       => array(
					'project' => new \Fieldmanager_TextField( array(
						'label'          => __( 'Project ID', 'project-dashboard' ),
						'serialize_data' => false,
					) ),
					'meta'    => new \Fieldmanager_Group( array(
						'name'           => 'meta',
						'serialize_data' => true,
						'children'       => array(
							'status'      => new \Fieldmanager_TextField( array(
								'label' => __( 'Status', 'project-dashboard' ),
							) ),
							'ci_name'     => new \Fieldmanager_TextField( array(
								'label' => __( 'CI Name', 'project-dashboard' ),
							) ),
							'ci_message'  => new \Fieldmanager_TextField( array(
								'label' => __( 'CI Message', 'project-dashboard' ),
							) ),
							'username'    => new \Fieldmanager_TextField( array(
								'label' => __( 'Committer Username', 'project-dashboard' ),
							) ),
							'environment' => new \Fieldmanager_TextField( array(
								'label' => __( 'Environment', 'project-dashboard' ),
							) ),
							'time'        => new \Fieldmanager_TextField( array(
								'label' => __( 'Build Time', 'project-dashboard' ),
							) ),
							'pr_url'      => new \Fieldmanager_TextField( array(
								'label' => __( 'Pull Request URL', 'project-dashboard' ),
							) ),
						)
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
				'all_items'          => __( 'All ' . $plural, 'project-dashboard' ),
				'search_items'       => __( 'Search ' . $plural, 'project-dashboard' ),
				'parent_item_colon'  => __( 'Parent ' . $plural . ':', 'project-dashboard' ),
				'not_found'          => __( 'No ' . strtolower( $plural ) . ' found.', 'project-dashboard' ),
				'not_found_in_trash' => __( 'No ' . strtolower( $plural ) . ' found in Trash.', 'project-dashboard' ),
				'title_label'        => __( 'Name Your ' . $singular, 'project-dashboard' ),
				'description_label'  => __( 'Description', 'project-dashboard' ),
				'edit_submit_label'  => __( 'Update Settings', 'project-dashboard' ),
			);
			$args   = array(
				'labels'            => $labels,
				'public'            => false,
				'menu_position'     => 1,
				'supports'          => array(
					'title'
				),
				'has_archive'       => false,
				'rewrite'           => $rewrite,
				'show_ui'           => true,
				'show_in_menu'      => 'project_dashboard',
				'show_in_admin_bar' => false,
				'field_manager'     => $fm,
			);

			\register_post_type( $slug, $args );

			$fm->add_meta_box( __( 'Other Information', 'project-dashboard' ), $slug );

		}
	}
}
