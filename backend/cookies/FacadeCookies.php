<?php

	class FacadeCookies
	{

		public static function getWebsiteDisplayLanguage()
		{
			return ApiCookies::get( "websiteDisplayLanguage" );
		}


		public static function setWebsiteDisplayLanguage( $websiteDisplayLanguage )
		{
			ApiCookies::set( "websiteDisplayLanguage", $websiteDisplayLanguage, 1 * 30 * 24 * 60 * 60 );
		}

	}
