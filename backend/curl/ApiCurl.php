<?php

	class ApiCurl
	{

		public static function doGet( $url, $data, $additionalHeaders )
		{
			self::__doCurl( "$url?" . http_build_query( $data ), "", $additionalHeaders );
		}


		public static function doPost( $url, $data, $additionalHeaders, $useMultipartEncryption )
		{
			if( !$useMultipartEncryption ) {
				$data = http_build_query( $data );
			}

			self::__doCurl( $url, $data, $additionalHeaders );
		}


		private static function __doCurl( $url, $data, $additionalHeaders )
		{
			$curlHandle = curl_init( $url );

			if( $curlHandle === FALSE ) {
				$exception = new ApiException( "Failed to connect to url $url", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiCurl::__doCurl" );
				$exception->setAdditionalInfo( "url", $url );
				throw $exception;
			}

			if( !empty( $data ) ) {
				curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $data );
				curl_setopt( $curlHandle, CURLOPT_POST, 1 );
			}

			curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curlHandle, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $curlHandle, CURLOPT_MAXREDIRS, Config::$CURL_MAX_REDIRECTS );
			curl_setopt( $curlHandle, CURLOPT_CONNECTTIMEOUT, Config::$CURL_CONNECTION_TIMEOUT );

			if( !empty( $additionalHeaders ) ) {
				foreach( $additionalHeaders as $key => $value ) {
					curl_setopt( $curlHandle, $key, $value );
				}
			}

			$result = curl_exec( $curlHandle );
			curl_close ( $curlHandle );

			if( $result === FALSE ) {
				$exception = new ApiException( "Failed to exec curl to $url", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiCurl::__doCurl" );
				$exception->setAdditionalInfo( "url", $url );
				$exception->setAdditionalInfo( "data", $data );
				$exception->setAdditionalInfo( "additionalHeaders", $additionalHeaders );
				throw $exception;
			}

			return $result;
		}

	}
