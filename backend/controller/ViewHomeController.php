<?php

	class ViewHomeController extends Controller
	{

		public function __construct()
		{
			parent::__construct();
		}


		protected function _processInput() {}


		protected function _doAction() {}


		protected function _finalizeRequest()
		{
			$this->_loadPage( NULL, "home" );
		}

	}