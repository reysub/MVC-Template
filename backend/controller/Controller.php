<?php

	abstract class Controller
	{
		protected $_user;
		protected $_input;
		protected $_output;
		protected $_errors;


		public function __construct()
		{
			$this->_user	= FacadeSession::getUser();
			$this->_input  	= array();
			$this->_output 	= array();
			$this->_errors 	= array();
		}


		public function executeRequest()
		{
			$this->__getInput();
			$this->_processInput();

			if ( empty( $this->_errors ) ) {
				$this->_doAction();
			}

			$this->_finalizeRequest();
		}


		private function __getInput()
		{
			foreach ( Config::$FILL_INPUT_FROM as $superGlobal ) {
				$this->_input = array_merge( $this->_input, $_$superGlobal );
			}
		}


		protected abstract function _processInput();


		protected abstract function _doAction();


		protected abstract function _finalizeRequest();


		protected function _printResult( $output )
		{
			if( $output === null ) {
				$output = json_encode( array (	"output"    => $this->_output,
												"errors"    => $this->_errors   )    );
			}

			echo $output;
		}


		protected function _loadPage( $pageUri, $pageName )
		{
			if( empty( $pageUri ) ) {
				$pageUri = Config::$PATH_TO[ "FRONTEND" ] . "view/" . $this->_getWebsiteDisplayLanguage() . "/$pageName.html";
			}

			include_once $pageUri;
		}


		protected function _getWebsiteDisplayLanguage()
		{
			$websiteDisplayLanguage = FacadeSession::getWebsiteDisplayLanguage();

			if( empty( $websiteDisplayLanguage ) || !in_array( $websiteDisplayLanguage, Config::$LANGUAGE_SUPPORTED ) ) {
				$websiteDisplayLanguage = FacadeCookies::getWebsiteDisplayLanguage();
			}

			if( empty( $websiteDisplayLanguage ) || !in_array( $websiteDisplayLanguage, Config::$LANGUAGE_SUPPORTED ) ) {
				$websiteDisplayLanguage = Config::$LANGUAGE_DEFAULT;
			}

			return $websiteDisplayLanguage;
		}

	}