<?php
/*
 *   Singleton classes
 */
namespace Project_Dashboard;

class Projects {
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

	}

	public function get_by_key( $api_key ) {
		$query_args = array(
			'post_type'      => 'pd-project',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'orderby'        => 'meta_value',
			'meta_query'     => array(
				array(
					'key'     => 'pd_api_key',
					'value'   => $api_key,
					'compare' => '=',
				),
			),
		);
		$query      = new \WP_Query( $query_args );

		return $query;

	}

}
