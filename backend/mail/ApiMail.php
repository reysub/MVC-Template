<?php

	class ApiMail
	{

		public static function sendTextMail( $sender, $receivers, $subject, $body, $replyTo, $ccs, $bccs )
		{
			self::__sendMail( $sender, $receivers, $subject, $body, $replyTo, $ccs, $bccs, "text/plain" );
		}


		public static function sendHtmlMail( $sender, $receivers, $subject, $body, $replyTo, $ccs, $bccs )
		{
			self::__sendMail( $sender, $receivers, $subject, $body, $replyTo, $ccs, $bccs, "text/html" );
		}


		private static function __sendMail( $sender, $receivers, $subject, $body, $replyTo, $ccs, $bccs, $contentType )
		{
			$replyTo 	= self::__parseField( empty( $replyTo ) ? $sender : $replyTo );
			$sender 	= self::__parseField( $sender );
			$receivers 	= self::__parseField( $receivers );
			$ccs 		= self::__parseField( $ccs );
			$bccs 		= self::__parseField( $bccs );

			$headers = self::__composeHeaders( $sender[ "forHeaders" ], $receivers[ "forHeaders" ], $subject, $replyTo[ "forHeaders" ], $ccs[ "forHeaders" ], $bccs[ "forHeaders" ], $contentType );

			if( !mail( $receivers[ "forEmail" ], $subject, $body, $headers ) ) {
				$exception = new ApiException( "Failed to send email", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiMail::__sendMail" );
				$exception->setAdditionalInfo( "sender", $sender );
				$exception->setAdditionalInfo( "receivers", $receivers );
				$exception->setAdditionalInfo( "subject", $subject );
				$exception->setAdditionalInfo( "body", $body );
				$exception->setAdditionalInfo( "replyTo", $replyTo );
				$exception->setAdditionalInfo( "ccs", $ccs );
				$exception->setAdditionalInfo( "bccs", $bccs );
				$exception->setAdditionalInfo( "contentType", $contentType );
				throw $exception;
			}
		}


		private static function __composeHeaders( $sender, $receivers, $subject, $replyTo, $ccs, $bccs, $contentType )
		{
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-type: $contentType; charset=UTF-8";
			$headers[] = "Subject: $subject";
			$headers[] = "From: $sender";
			$headers[] = "To: $receivers";
			$headers[] = "Reply-To: $replyTo";

			if( !empty( $ccs ) )
				$headers[] = "CC: $ccs";

			if( !empty( $bccs ) )
				$headers[] = "BCC: $bccs";

			$headers[] = "X-Mailer: PHP/" . phpversion();

			return implode( "\r\n", $headers );
		}


		private static function __parseField( $field )
		{
			if( !is_array( $field ) ) {
				return array( "forEmail" => $field, "forHeaders" => $field );
			}

			$forEmail	= array();
			$forHeaders	= array();

			foreach( $field as $email => $name ) {
				$forEmail[] = $email;
				if( !empty( $name ) ) {
					$forHeaders[] = "$name <$email>";
				} else {
					$forHeaders[] = $email;
				}
			}

			return array( "forEmail" => implode( ",", $forEmail ), "forHeaders" => implode( ",", $forHeaders ) );
		}

	}
