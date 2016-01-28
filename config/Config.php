<?php

	class Config
	{
		public static $BUILD_VERSION;
		public static $HOST_NAME;
		public static $EXECUTION_ID;

		public static $SESSION_LIFETIME;
		public static $SESSION_GC_PROBABILITY;

		public static $FILL_INPUT_FROM;

		public static $LANGUAGE_DEFAULT;
		public static $LANGUAGE_SUPPORTED;

		public static $PATH_TO;
		public static $SYSTEM_FOLDERS;
		public static $CUSTOM_FOLDERS;

		public static $CONTROLLER_DEFAULT;
		public static $CONTROLLER_NOT_FOUND;

		public static $CURL_MAX_REDIRECTS;
		public static $CURL_CONNECTION_TIMEOUT;

		public static $DB_NAME;
		public static $DB_HOST;
		public static $DB_USER;
		public static $DB_PASSWORD;

		public static $LEVEL_ERROR_KEY;
		public static $LEVEL_WARNING_KEY;
		public static $LEVEL_MESSAGE_KEY;
		
		public static $EXCEPTION_LEVEL_ACTIVE_KEY;

		public static $LOG_HUMAN_FILENAME;
		public static $LOG_SYSTEM_FILENAME;
		public static $LOG_LEVEL_ACTIVE_KEY;
		public static $LOG_COLOR_ENABLED;
		public static $LOG_LEVELS;
		public static $LOG_COLORS;


		public static function init( $rootPath )
		{
			self::$BUILD_VERSION			= "1.0.0";
			self::$HOST_NAME				= $_SERVER[ "HTTP_HOST" ];
			self::$EXECUTION_ID				= NULL;

			self::$SESSION_LIFETIME			= 1 * 24 * 60 * 60;
			self::$SESSION_GC_PROBABILITY	= 1;

			self::$FILL_INPUT_FROM			= array( "REQUEST" );

			self::$LANGUAGE_DEFAULT			= "en";
			self::$LANGUAGE_SUPPORTED		= array( "en" );

			self::$PATH_TO 					= array( 	"ROOT" => rtrim( $rootPath, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR );
			self::$SYSTEM_FOLDERS			= array(	"backend"   => array(	"parent" => self::$PATH_TO[ "ROOT" ], "owner" => "www-data",
																				"group" => "root", "permission" => "0755" ),
														"frontend"	=> array(	"parent" => self::$PATH_TO[ "ROOT" ], "owner" => "www-data",
																				"group" => "root", "permission" => "0755" ),
														"log" 		=> array(	"parent" => self::$PATH_TO[ "ROOT" ], "owner" => "www-data",
																				"group" => "www-data", "permission" => "0777" ),
														"storage"	=> array(	"parent" => self::$PATH_TO[ "ROOT" ], "owner" => "www-data",
																				"group" => "www-data", "permission" => "0777" ) );
			self::$CUSTOM_FOLDERS			= array();

			self::$CONTROLLER_DEFAULT		= "ViewHomeController";
			self::$CONTROLLER_NOT_FOUND		= "ViewPageNotFoundController";

			self::$CURL_MAX_REDIRECTS		= 3;
			self::$CURL_CONNECTION_TIMEOUT	= 10;

			self::$DB_NAME 					= "";
			self::$DB_HOST 					= "";
			self::$DB_USER 					= "";
			self::$DB_PASSWORD 				= "";

			self::$LEVEL_ERROR_KEY			= "ERROR";
			self::$LEVEL_WARNING_KEY		= "WARNING";
			self::$LEVEL_MESSAGE_KEY		= "MESSAGE";

			self::$EXCEPTION_LEVEL_ACTIVE_KEY = self::$LOG_LEVEL_ERROR_KEY;

			self::$LOG_HUMAN_FILENAME		= "website.log";
			self::$LOG_SYSTEM_FILENAME		= date( "Y-m-d" ) . ".log";
			self::$LOG_LEVEL_ACTIVE_KEY		= self::$LOG_LEVEL_MESSAGE_KEY;
			self::$LOG_COLOR_ENABLED		= true;
			self::$LOG_LEVELS				= array(	self::$LOG_LEVEL_ERROR_KEY   => 80,
														self::$LOG_LEVEL_WARNING_KEY => 50,
														self::$LOG_LEVEL_MESSAGE_KEY => 20	);
			self::$LOG_COLORS				= array( 	self::$LOG_LEVEL_ERROR_KEY   => "\033[31m",
														self::$LOG_LEVEL_WARNING_KEY => "\033[33m",
														self::$LOG_LEVEL_MESSAGE_KEY => "\033[0m"	);

			self::__initSystem();
		}


		private static function __initSystem()
		{
			spl_autoload_register( "Config::loadClass" );
			
			self::__initFolders( self::$SYSTEM_FOLDERS );
			self::__initFolders( self::$CUSTOM_FOLDERS );
			
			self::__initIncludePath( self::$PATH_TO[ "BACKEND" ] );
			
			self::__initExecution();
			self::__initdatabase();
			self::__initLog();
		}


		public static function loadClass( $className )
		{
			$fileName = str_replace( array( "\\", "_" ), "/", $className ) . ".php";
			if( !@include_once $fileName ) {
				throw new IndexException( -1, "File $fileName not found" );
			}

			if( ! ( class_exists( $className, false ) || interface_exists( $className, false ) ) ) {
				throw new IndexException( -2, "Class $className not found" );
			}
		}


		private static function __initFolders( $folders )
		{
			foreach ( $folders as $path => $details ) {
				mkdir( $details[ "parent" ] . $path );
				chown( $details[ "parent" ] . $path, $details[ "owner" ] );
				chgrp( $details[ "parent" ] . $path, $details[ "group" ] );
				chmod( $details[ "parent" ] . $path, $details[ "permission" ] );

				$folderKey = str_replace( DIRECTORY_SEPARATOR, "_", strtoupper( $path ) );
				self::$PATH_TO[ $folderKey ] = $details[ "parent" ] . rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
			}
		}


		private static function __initIncludePath( $rootFolder )
		{
			$subfolders = glob( $rootFolder . "*", GLOB_ONLYDIR );
			set_include_path( implode( ":", glob( $rootFolder . "*", GLOB_ONLYDIR ) ) . ":" . get_include_path() );
			foreach ( $subfolders as $folder ) {
				self::__initIncludePath( $folder );
			}
		}


		private static function __initExecution()
		{
			ini_set( "session.gc_maxlifetime", self::$SESSION_LIFETIME );
			ini_set( "session.gc_probability", self::$SESSION_GC_PROBABILITY );
			session_set_save_handler( new FileSessionHandler(), true );

			self::$EXECUTION_ID = FacadeSession::getExecutionId();
			self::$TAB_EXECUTION_ID = isset( $_REQUEST[ self::$TAB_EXECUTION_ID_KEY ] ) ? isset( $_REQUEST[ self::$TAB_EXECUTION_ID_KEY ] ) : uniqid();
		}


		private static function __initdatabase()
		{
			// ApiDatabase::openConnection();
		}


		private static function __initLog()
		{
			if( !is_file( self::$PATH_TO[ "LOG" ] . self::$LOG_SYSTEM_FILENAME ) ) {
				touch( self::$PATH_TO[ "LOG" ] . self::$LOG_SYSTEM_FILENAME );
				chown( self::$PATH_TO[ "LOG" ] . self::$LOG_SYSTEM_FILENAME, self::$SYSTEM_FOLDERS[ "log" ][ "owner" ] );
				chgrp( self::$PATH_TO[ "LOG" ] . self::$LOG_SYSTEM_FILENAME, self::$SYSTEM_FOLDERS[ "log" ][ "group" ] );
				chmod( self::$PATH_TO[ "LOG" ] . self::$LOG_SYSTEM_FILENAME, self::$SYSTEM_FOLDERS[ "log" ][ "permission" ] );
				unlink( self::$PATH_TO[ "LOG" ] . self::$LOG_HUMAN_FILENAME );
			}

			if( !is_file( self::$PATH_TO[ "LOG" ] . self::$LOG_HUMAN_FILENAME ) ) {
				symlink( self::$PATH_TO[ "LOG" ] . self::$LOG_SYSTEM_FILENAME, self::$PATH_TO[ "LOG" ] . self::$LOG_HUMAN_FILENAME );
			}
		}

	}