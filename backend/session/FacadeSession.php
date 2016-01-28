<?php

	class FacadeSession
	{

		public static function getExecutionId()
		{
			if( empty( ApiSession::get( "executionId" ) ) ) {
				self::setExecutionId( uniqid() );
			}

			return ApiSession::get( "executionId" );
		}


		public static function setExecutionId( $executionId )
		{
			ApiSession::set( "executionId", $executionId, FALSE, "deleteExecution" );
		}


		public static function getWebsiteDisplayLanguage()
		{
			return ApiSession::get( "websiteDisplayLanguage" );
		}


		public static function setWebsiteDisplayLanguage( $websiteDisplayLanguage )
		{
			ApiSession::set( "websiteDisplayLanguage", $websiteDisplayLanguage, FALSE, NULL );
		}


		public static function getUser()
		{
			return ApiSession::get( "user" );
		}

	}