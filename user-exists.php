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
			if( is_email( $request[ 'email' ] ) && is_numeric( email_exists( $request[ 'email' ] ) ) ){
				return true;
			}
			return false;

		}
	]);
});