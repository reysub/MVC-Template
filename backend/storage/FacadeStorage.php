<?php

	class FacadeStorage
	{

		public static function deleteExecution( $executionId )
		{
			ApiStorage::deleteDirectory( Config::$PATH_TO[ "STORAGE" ] . $executionId );
		}

	}