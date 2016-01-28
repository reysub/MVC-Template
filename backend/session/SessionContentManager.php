<?php

	class SessionContentManager
	{

		public static function deleteExecution( $executionId )
		{
			session_unset();
			session_destroy();
			ApiCookies::delete( "PHPSESSID" );
			FacadeStorage::deleteExecution( $executionId );
		}
	}