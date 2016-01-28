<?php

	class ApiSession
	{

		public static function get( $key )
		{
			session_start();
			$content = isset( $_SESSION[ $key ] ) ? $_SESSION[ $key ] : NULL;
			session_write_close();

			if( !empty( $content ) && $content[ "expire" ] !== false && $content[ "expire" ] < time() ) {
				self::delete( $key );
				return NULL;
			}

			return $content;
		}


		public static function set( $key, $value, $fixedDuration, $callback )
		{
			$expire = $fixedDuration !== false ? time() + $fixedDuration : false;
			session_start();
			$_SESSION[ $key ] = array( "value" => $value, "expire" => $expire, "callback" => $callback );
			session_write_close();
		}


		public static function delete( $key )
		{
			session_start();
			$content = isset( $_SESSION[ $key ] ) ? $_SESSION[ $key ] : NULL;
			unset( $_SESSION[ $key ] );
			session_write_close();

			if( !empty( $content ) && !empty( $content[ "callback" ] ) && method_exists( "SessionContentManager", $content[ "callback" ] ) ) {
				call_user_func_array( array( "SessionContentManager", $content[ "callback" ] ), $content[ "value" ] );
			}
		}

	}
