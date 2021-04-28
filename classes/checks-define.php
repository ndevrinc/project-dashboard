<?php
/*
 *   Singleton classes
 */
namespace Project_Dashboard;

class Checks_Define {
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

			$slug     = 'pd-check';
			$singular = 'PD Check';
			$plural   = 'PD Checks';
			$rewrite  = array( 'slug' => 'pd-checks/check' );

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
				'labels'            => $labels,
				'public'            => false,
				'menu_position'     => 10,
				'supports'          => array(
					'title'
				),
				'has_archive'       => false,
				'rewrite'           => $rewrite,
				'show_ui'           => true,
				'show_in_menu'      => 'project_dashboard',
				'show_in_admin_bar' => false,
			);

			\register_post_type( $slug, $args );

		}
	}
}
