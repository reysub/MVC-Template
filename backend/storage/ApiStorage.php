<?php

	class ApiStorage
	{

		public static function getFileContent( $file, $asArray )
		{
			if( !is_file( $file ) ) {
				return NULL;
			}

			$content = $asArray ? file( $file ) : file_get_contents( $file );

			if( $content === FALSE ) {
				$exception = new ApiException( "Failed to read from file $file", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::getFileContent" );
				$exception->setAdditionalInfo( "file", $file );
				$exception->setAdditionalInfo( "asArray", $asArray );
				throw $exception;
			}

			return $content;
		}


		public static function setFileContent( $file, $content, $append )
		{
			self::createDirectory( dirname( $file ) );

			$output = $append ? file_put_contents( $file, $content, FILE_APPEND ) : file_put_contents( $file, $content );

			if( $output === FALSE ) {
				$exception = new ApiException( "Failed to write on file $file", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::setFileContent" );
				$exception->setAdditionalInfo( "file", $file );
				$exception->setAdditionalInfo( "content", $content );
				$exception->setAdditionalInfo( "append", $append );
				throw $exception;
			}

			if( !chmod( $file, Config::$SYSTEM_FOLDERS[ "storage" ][ "owner" ] ) ) {
				$exception = new ApiException( "Failed to chmod file $file", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::setFileContent" );
				$exception->setAdditionalInfo( "file", $file );
				$exception->setAdditionalInfo( "chmod", Config::$SYSTEM_FOLDERS[ "storage" ][ "owner" ] );
				throw $exception;
			}

			if( !chown( $file, Config::$SYSTEM_FOLDERS[ "storage" ][ "group" ] ) ) {
				$exception = new ApiException( "Failed to chown file $file", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::setFileContent" );
				$exception->setAdditionalInfo( "file", $file );
				$exception->setAdditionalInfo( "chown", Config::$SYSTEM_FOLDERS[ "storage" ][ "group" ] );
				throw $exception;
			}

			if( !chgrp( $file, Config::$SYSTEM_FOLDERS[ "storage" ][ "permission" ] ) ) {
				$exception = new ApiException( "Failed to chgrp file $file", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::setFileContent" );
				$exception->setAdditionalInfo( "file", $file );
				$exception->setAdditionalInfo( "chgrp", Config::$SYSTEM_FOLDERS[ "storage" ][ "permission" ] );
				throw $exception;
			}
		}


		public static function deleteFile( $file )
		{
			if( is_file( $file ) && !unlink( $file ) ) {
				$exception = new ApiException( "Failed to delete file $file", Config::$LOG_LEVEL_WARNING_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::deleteFile" );
				$exception->setAdditionalInfo( "file", $file );
				throw $exception;
			}
		}


		public static function createDirectory( $directory )
		{
			$folders = explode( DIRECTORY_SEPARATOR, rtrim( $directory, DIRECTORY_SEPARATOR ) );
			$path = "";

			foreach( $folders as $folder ) {
				$path .= $folder;

				if( !is_dir( $path ) && !empty( $path ) ) {
					if( mkdir( $path ) === FALSE ) {
						$exception = new ApiException( "Failed to create directory $directory", Config::$LOG_LEVEL_ERROR_KEY );
						$exception->setAdditionalInfo( "source", "ApiStorage::createDirectory" );
						$exception->setAdditionalInfo( "directory", $directory );
						throw $exception;
					}

					chmod( $path, Config::$SYSTEM_FOLDERS[ "storage" ][ "owner" ] );
					chown( $path, Config::$SYSTEM_FOLDERS[ "storage" ][ "group" ] );
					chgrp( $path, Config::$SYSTEM_FOLDERS[ "storage" ][ "permission" ] );
				}
			}
		}


		public static function getDirectoryContent( $directory, $hiddenFiles, $systemFiles )
		{
			$content = scandir( $directory );
			$output = array();

			foreach( $content as $element ) {
				if( ( !$hiddenFiles && $element[ 0 ] == "." ) || ( !$systemFiles && in_array( $element, array( ".", ".." ) ) ) ) {
					continue;
				}

				$output[] = $element;
			}

			return $output;
		}


		public static function deleteDirectory( $directory )
		{
			$content = self::getDirectoryContent( $directory, TRUE, FALSE );

			foreach( $content as $element ) {
				if( is_dir( $element ) ) {
					self::deleteDirectory( $directory . $element );
				} else {
					self::deleteFile( $directory . $element );
				}
			}

			if( !rmdir( $directory ) ) {
				$exception = new ApiException( "Failed to delete directory $directory", Config::$LOG_LEVEL_ERROR_KEY );
				$exception->setAdditionalInfo( "source", "ApiStorage::deleteDirectory" );
				$exception->setAdditionalInfo( "directory", $directory );
				throw $exception;
			}
		}

	}