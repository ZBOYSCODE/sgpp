<?php

	namespace Gabs\Controllers;

	class AccesoController extends ControllerBase
	{

		public function initialize()
		{
	    	$this->_themeArray = array('topMenu'=>true, 'menuSel'=>'','pcView'=>'', 'pcData'=>'', 'jsScript'=>'');
	    }
	
		public function denegadoAction()
		{

    		$content    = 'acceso/denegado';


            $pcData = '';


            echo $this->view->render('theme',array('pcView'=>$content,'pcData'=>$pcData, )); 
		}
	}
