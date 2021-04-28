<?php

namespace Project_Dashboard;

use WP_REST_Server;
use WP_REST_Controller;
use WP_REST_Response;
use WP_Error;

class Checks_Controller extends WP_REST_Controller {

	protected $project = 0;

	public function __construct() {
		$this->namespace = 'pd/v1';
		$this->rest_base = 'checks';
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * /wp-json/pd/v1/checks/{check_id}
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'                => array(),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
		) );
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => array(
					'context' => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$items = array(); //do a query, call another class, etc
		$data  = array();
		foreach ( $items as $item ) {
			$itemdata = $this->prepare_item_for_response( $item, $request );
			$data[]   = $this->prepare_response_for_collection( $itemdata );
		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Update a single check.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		$params = $request->get_params();

		$params['project'] = $this->project;

		$items = $this->prepare_item_for_database( $params );

		if ( count( $items ) > 0 ) {
			$save_results = wp_insert_post( $items );
		}

		$data = array( 'messages' => $save_results );
		if ( false !== $save_results ) {
			\update_post_meta( $save_results, 'pd_status', $items['status'] );
			\update_post_meta( $save_results, 'pd_lighthouse', $items['lighthouse'] );
			\update_post_meta( $save_results, 'pd_environment', $items['environment'] );
			\update_post_meta( $save_results, 'pd_url', $items['url'] );
			\update_post_meta( $save_results, 'pd_project', $items['project'] );

			return new WP_REST_Response( $data, 200 );
		}

		return new WP_Error( 'cant-update', __( 'save did not work', 'project-dashboard' ), array(
			'status' => 500
		) );

	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param Array $params Request array
	 *
	 * @return array $prepared_item
	 */
	protected function prepare_item_for_database( $params ) {
		$return = array();

		if ( ! empty( $params ) ) {
			$project = get_post( $params['project'] );
			$return  = array(
				'post_type'   => 'pd-check',
				'post_title'  => $project->post_title . ' - ' . $params['environment'] . ' Check',
				'status'      => isset( $params['status'] ) ? $params['status'] : 'notset',
				'lighthouse'  => isset( $params['lighthouse'] ) ? $params['lighthouse'] : '{}',
				'environment' => isset( $params['environment'] ) ? $params['environment'] : 'notset',
				'url'         => isset( $params['url'] ) ? $params['url'] : 'notset',
				'project'     => $params['project'],
			);
		}

		return $return;
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {

		return $item;
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'page'     => array(
				'description'       => 'Current page of the collection.',
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => 'Maximum number of items to be returned in result set.',
				'type'              => 'integer',
				'default'           => 10,
				'sanitize_callback' => 'absint',
			),
			'search'   => array(
				'description'       => 'Limit results to those matching a string.',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) {
		$params   = $request->get_params();
		$projects = Projects::get_instance();
		if ( array_key_exists( 'api_key', $params ) ) {
			$results  = $projects->get_by_key( $params['api_key'] );
		}

		if ( isset( $results ) && $results->have_posts() ) {
			$project       = $results->post;
			$this->project = $project->ID;

			return true;
		} else {
			return new WP_Error( 'rest_cannot_edit', __( 'Sorry, you are not allowed to update this post.' ), array( 'status' => rest_authorization_required_code() ) );;
		}
		$post = get_post( $request['id'] );

		if ( $post && ! $this->check_update_permission( $post ) ) {
		}

		return true;
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		//TODO verify the api key
		return true;

	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}


}