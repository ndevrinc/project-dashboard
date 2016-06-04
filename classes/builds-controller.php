<?php

namespace Client_Portal;

use WP_REST_Server;
use WP_REST_Controller;
use WP_REST_Response;
use WP_Error;

class Builds_Controller extends WP_REST_Controller {

	protected $id_project = 0;

	public function __construct() {
		$this->namespace = 'clients/v1';
		$this->rest_base = 'builds';
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * /wp-json/clients/v1/builds/{build_id}
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
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/(?P<user_id>[\d]+)', array(
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

	private function screenshot( $site_url ) {
		$url      = "https://www.browserstack.com/screenshots";
		$username = "meekyhwang1";
		$password = "VKXS5VqUBPwVnudAMifE";

		$args = array(
			"body"    => '{"browsers": [{"os": "Windows", "os_version": "10", "browser_version": "11.0", "browser": "ie"}], "url": "' . $site_url . '"}',
			"headers" => array(
				"Authorization" => "Basic " . base64_encode( "$username:$password" ),
				"content-type"  => "application/json",
				"accept"        => "application/json; charset=utf-8"
			)
		);

		$response = wp_remote_post( $url, $args );
		file_put_contents( "/srv/www/matt.html", $response );

		if ( is_wp_error( $response ) ) {
			$job_id = "error";
		} else {
			$json   = json_decode( $response['body'] );
			$job_id = $json->job_id;
		}

		return $job_id;

	}

	/**
	 * Update a single build.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_item( $request ) {
		global $wpdb;
		$params = $request->get_params();

		$params['id_project']          = $this->id_project;
		$params['browserstack_job_id'] = $this->screenshot( $params['site_url'] );

		$items = $this->prepare_item_for_database( $params );

		if ( count( $items ) > 0 ) {
			$save_results = $wpdb->insert( $wpdb->prefix . "clients_builds", $items );
		}

		$data = array( 'messages' => $save_results );
		if ( false !== $save_results ) {
			return new WP_REST_Response( $data, 200 );
		}

		return new WP_Error( 'cant-update', __( 'save did not work', 'ndevr-clients' ), array(
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
			$return = array(
				'id_project'          => $params['id_project'],
				'ci_name'             => $params['ci_name'],
				'ci_message'          => $params['ci_message'],
				'committer_username'  => $params['committer_username'],
				'environment'         => $params['environment'],
				'time'                => $params['time'],
				'status'              => $params['status'],
				'build_time'          => $params['build_time'],
				'frontend_time'       => $params['frontend_time'],
				'backend_time'        => $params['backend_time'],
				'browserstack_job_id' => $params['browserstack_job_id'],
				'build_url'           => $params['build_url'],
				'pullrequest_url'     => $params['pullrequest_url']
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
		$results  = $projects->get_by_key( $params['api_key'] );

		if ( array_key_exists( 'api_key', $params ) && $results->have_posts() ) {
			$project          = $results->post;
			$this->id_project = $project->ID;

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
		return true; //readable by all for now //current_user_can( 'edit_posts' );
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