<?php

	class ApiLog
	{

		public static function doLog( $text, $errorLevel )
		{
			if( Config::$LOG_LEVELS[ $errorLevel ] < Config::$LOG_LEVELS[ Config::$LOG_LEVEL_ACTIVE_KEY ] ) {
				return;
			}
			
			$logExecutionId	 	 = Config::$EXECUTION_ID;
			$logDateTime 		 = date( "Y-m-d H:i:s" );
			$logCalledByClass 	 = debug_backtrace()[ 2 ][ "class" ];
			$logCalledByFunction = debug_backtrace()[ 2 ][ "function" ];

			$header = "[$logExecutionId]" . "[$errorLevel]" . "[$logDateTime]" . "[$logCalledByClass::$logCalledByFunction]: ";
			$text 	= is_array( $text ) ? print_r( $text, true ) : $text;
			$footer = "";

			if( Config::$LOG_COLOR_ENABLED && isset( Config::$LOG_COLORS[ $errorLevel ] ) ) {
				$logColor = Config::$LOG_COLORS[ $errorLevel ];
				$header = "$logColor" . $header;
				$footer = "\033[0m";
			}

			$text = $header . str_replace( "\n", "$footer\n$header", $text ) . $footer . "\n";

			file_put_contents( Config::$PATH_TO[ "LOG" ] . Config::$LOG_SYSTEM_FILENAME, $text, FILE_APPEND );
		}

	}