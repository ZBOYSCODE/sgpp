<?php
namespace Gabs\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Gabs\AccesoAcl\AccesoAcl;

use Phalcon\Mvc\Model\Criteria;


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

    public static function fromInput($dependencyInjector, $model, $data)
		{
		    $conditions = array();

		    if (count($data)) 
		    {
		        $metaData = $dependencyInjector->getShared('modelsMetadata');

		        $dataTypes = $metaData->getDataTypes($model);

		        $bind = array();

		        foreach ($data as $fieldName => $value) 
		        {
	                if (!is_null($value)) 
	                {
	                    if ($value != '') 
	                    {  
	                    	if ($dataTypes[$fieldName] == 2 || $dataTypes[$fieldName] == 6 || $dataTypes[$fieldName] == 1) 
	                        {                              
	                            $condition = $fieldName . " LIKE :" . $fieldName . ":";                             
	                            $bind[$fieldName] = '%' . $value . '%';
	                        } 
	                        //en otro caso buscamos la búsqueda exacta
	                        else 
	                        {                                
	                            $condition = $fieldName . ' = :' . $fieldName . ':';
	                            $bind[$fieldName] = $value;
	                        }
	                        
	                     	$conditions[] = $condition;
	                    }
	                }
		        }
		    }
		 
		    $criteria = new Criteria();
		    if (count($conditions)) 
		    {
		    	# como será una busqueda ocuparemos OR
		    	# en caso de ser un filtro se ocuparía AND
		        $criteria->where(join(' OR ', $conditions));
		        $criteria->bind($bind);
		    }
		    return $criteria;
		}
}