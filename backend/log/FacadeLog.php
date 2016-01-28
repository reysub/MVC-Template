<?php

	class FacadeLog
	{

		public static function logError( $text )
		{
			ApiLog::doLog( $text, Config::$LOG_LEVEL_ERROR_KEY );
		}


		public static function logWarning( $text )
		{
			ApiLog::doLog( $text, Config::$LOG_LEVEL_WARNING_KEY );
		}


		public static function logMessage( $text )
		{
			ApiLog::doLog( $text, Config::$LOG_LEVEL_MESSAGE_KEY );
		}

	}