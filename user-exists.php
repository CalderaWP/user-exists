<?php
/**
 Plugin name: User Exists API Endpoint
 Version: 0.1.0
 */

add_action( 'rest_api_init', function(){
	register_rest_route( 'calderawp', '/user-exists', [
		'args' => [
			'email' => [
				'type' => 'string',
				'required' => true
			]
		],
		'callback' => function( $request ){
			$pre_serve = apply_filters( 'user_exists_pre_check', $request );
			if( is_wp_error( $pre_serve ) ){
				return rest_ensure_response( $pre_serve );
			}
			if( is_email( $request[ 'email' ] ) && is_numeric( email_exists( $request[ 'email' ] ) ) ){
				return rest_ensure_response( [ 'exists' => true ] );
			}
			/**
			 * Fires when user exist check fails
			 *
			 * @param $request WP_REST_Request Current request
			 */
			do_action( 'user_exits_failed', $request );
			return rest_ensure_response( [ 'exists' => false ] );

		}
	]);
});