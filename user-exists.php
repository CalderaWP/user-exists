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
				'required' => true,
			]
		],
		'callback' => function( $request ){
			/**
			 * Hook to prevent check
			 *
			 * Use to impliment throttling
			 *
			 * @param null|WP_Error Return a WP_Error to stop check and return that error via API
			 * @param string $email Email address
			 * @param WP_REST_Request $request  Current request
			 */
			$pre_serve = apply_filters( 'cwp_user_exists_pre_check', null,  $request[ 'email' ], $request );
			if( is_wp_error( $pre_serve ) ){
				return rest_ensure_response( $pre_serve );
			}

			if( is_email( $request[ 'email' ] ) && is_numeric( email_exists( $request[ 'email' ] ) ) ){
				return rest_ensure_response( [ 'exists' => true ] );
			}

			/**
			 * Fires when user exist check fails
			 *
			 * @param string$email Email address
			 * @param WP_REST_Request $request  Current request
			 */
			do_action( 'cwp_user_exits_failed', $request[ 'email' ], $request );
			return rest_ensure_response( [ 'exists' => false ] );

		}
	]);
});

/**
 * Trigger login lock down blocks if login lockdown exists
 */
add_action( 'plugins_loaded', function(){
	if( function_exists( 'loginLockdown_install' ) ){

		/**
		 * Increment and count fails
		 */
		add_action( 'cwp_user_exits_failed', function( $email  ){
			incrementFails( $email );
			$fails = countFails( $email );
			$settings = get_loginlockdownOptions();
			$allowed = 3;
			if( ! empty( $settings[ 'max_login_retries' ] ) ){
				$allowed = $settings[ 'max_login_retries' ];
			}


			//To many fails, you are now locked out.
			if( cwp_user_exist_is_to_many( $email ) ){
				lockDown( $email );
			}
		});

		/**
		 * Prevent user exists check if locked out
		 */
		add_filter( 'cwp_user_exists_pre_check', function( $return, $email ){
			//If locked out, return WP_Error
			if( isLockedDown() ||cwp_user_exist_is_to_many( $email ) ){
				return new WP_Error( 'user-exists-lockout', __( 'Locked out' ) );
			}

			return null;


		}, 10, 2 );
	}

	/**
	 * Check if too many lockouts
	 *
	 * @param string $email Email to check
	 *
	 * @return bool
	 */
	function cwp_user_exist_is_to_many( $email ){
		$fails = countFails( $email );
		$settings = get_loginlockdownOptions();
		$allowed = 3;
		if( ! empty( $settings[ 'max_login_retries' ] ) ){
			$allowed = $settings[ 'max_login_retries' ];
		}


		//To many fails, you are now locked out.
		if( $fails > $allowed ){
			return true;
		}

		return false;
	}
});