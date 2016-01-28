<?php

	class FileSessionHandler extends SessionHandler
	{

		public function gc( $maxlifetime )
		{
	        foreach ( glob( session_save_path() ) as $file ) {
	            if ( filemtime( $file ) + $maxlifetime < time() ) {
	            	$sessionId = XXXXX;
	            	ApiSession::delete( $sessionId );
	                unlink( $file );
	            }
	        }

	        return TRUE;
		}

	}
