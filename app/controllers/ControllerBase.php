<?php
namespace Gabs\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Gabs\AccesoAcl\AccesoAcl;

class ControllerBase extends Controller
{
	public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
		
		//noAuth -> configuracion de controller y acciones que no tienen que pasar por la autentificacion
		
		if(!(isset($this->config->noAuth[$dispatcher->getControllerName()][$dispatcher->getActionName()]) || isset($this->config->noAuth[$dispatcher->getControllerName()]['*'])))
		{
			$identity = $this->auth->getIdentity();

			if (!is_array($identity)) {
				$response = new \Phalcon\Http\Response();
				$response->redirect("login");
				$response->send();
			}
		}	
		
		if(isset($this->auth->getIdentity()['roleId']) && !AccesoAcl::tieneAcceso())
    	{
    		$response = new \Phalcon\Http\Response();
			$response->redirect("acceso/denegado");
			$response->send();
    	}
	
    }
}