<?php

	class ApiCookies
	{

		public static function get( $key )
		{
			return isset( $_COOKIE[ $key ] ) ? json_decode( $_COOKIE[ $key ], true ) : NULL;
		}


		public static function set( $key, $value, $duration )
		{
			$_COOKIE[ $key ] = json_encode( $value );
			if( !setcookie( $key, json_encode( $value ), time() + $duration, "/" ) ) {
				$exception = new ApiException( "Failed to set cookie $key", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiCookies::set" );
				$exception->setAdditionalInfo( "key", $key );
				$exception->setAdditionalInfo( "value", $value );
				$exception->setAdditionalInfo( "duration", $duration );
				throw $exception; 
			}
		}


		public static function delete( $key )
		{
			unset( $_COOKIE[ $key ] );
			if( !setcookie( $key, NULL, time() - 1, "/" ) ) {
				$exception = new ApiException( "Failed to set cookie $key", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiCookies::delete" );
				$exception->setAdditionalInfo( "key", $key );
				throw $exception;
			}
		}

	}
