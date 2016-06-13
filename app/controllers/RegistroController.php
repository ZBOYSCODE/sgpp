<?php
	namespace Gabs\Controllers;

	use Gabs\Models\Personas;
	use Gabs\Models\Proyecto;
	use Gabs\Models\Bloque;
	use Gabs\Models\Actividad;

	class RegistroController extends ControllerBase
	{

	    public function indexAction()
	    {
			$this->ingresarAction();
	    }

	    public function ingresarAction()
	    {
	    	$content 	= 'registro/ingresar';
	    	$jsScript 	= '';
	    	$menu 		= 'menu/topMenu';
	    	$sideBar 	= 'menu/sideBar';

	    	$addJs[] 	= "js/registro.js";
	    	$addCss[]	= "css/style_registros.css";

	    	$pcData['proyectos'] = Proyecto::find();
	    	$pcData['fecha'] 	= date('Y-m-d');

	        echo $this->view->render('theme',array(	'topMenu'=>$menu,
	        										'menuSel'=>'', 
	        										'sideBar'=>$sideBar, 
	        										'sideBarSel'=>'gestion', 
	        										'pcView'=>$content,
	        										'pcData'=>$pcData, 
	        										'jsScript' => $jsScript,
	        										'addJs' => $addJs,
	        										'addCss' => $addCss)
	        						);  
	    }


	    public function crearBloqueAction()
	    {
			$bloque = new Bloque();

		    $bloque->proyecto_id 		= $this->request->getPost("proyecto", "int");
		    $bloque->usuario_id      	= $this->auth->getIdentity()['id'];
		    $bloque->fecha           	= $this->request->getPost("fecha");
		    $bloque->orden             	= $this->request->getPost("orden", "int");

	        if ($bloque->save())
	        {
	        	$data['estado'] = true;
	        	$data['msg']	= "¡Bloque creado!";
	        	$data['id']		= $bloque->id;
	        } else {
	        	$data['msg'] 	= "¡Lo sentimos, no hemos podido crear el bloque!";

	            foreach ($bloque->getMessages() as $message) {
	                $data['detalleError'][] = $message->getMessage();
	            }
	        }

	        echo json_encode($data, JSON_PRETTY_PRINT);
	    }

	    public function deleteBloqueAction()
	    {
	    	$id = $this->request->getPost("bloque", "int");
	    	$bloque = Bloque::findById($id);

	    	if ($bloque != false)
	    	{

	    		if($this->deleteActividadByBloque($id)){
	        			$data['act'] = "Actividades eliminadas"; 
	        		

				    if ($bloque->delete() == false)
				    {
				    	$data['estado'] = false;
				        $data['msg'] 	= "¡Lo sentimos, no hemos podido eliminar el bloque!";

				        foreach ($bloque->getMessages() as $message) {
				            $data['detalleError'][] = $message->getMessage();
				        }

				    } else {
				        $data['estado'] = true;
		        		$data['msg']	= "¡Bloque eliminado!";
		        		$data['id']		= $id;
				    }
				}
			}else{
				$data['estado'] = false;
			    $data['msg'] 	= "¡Lo sentimos, el bloque no existe!";
			}

			echo json_encode($data, JSON_PRETTY_PRINT);
	    }

	    public function deleteActividadAction()
	    {
	    	$id = $this->request->getPost("actividad", "int");
	    	$actividad = Actividad::findById($id);

	    	if ($actividad != false)
	    	{
			    if ($actividad->delete() == false)
			    {
			    	$data['estado'] = false;
			        $data['msg'] 	= "¡Lo sentimos, no hemos podido eliminar la actividad!";

			        foreach ($actividad->getMessages() as $message) {
			            $data['detalleError'][] = $message->getMessage();
			        }

			    } else {
			        $data['estado'] = true;
	        		$data['msg']	= "Actividad eliminado!";
	        		$data['id']		= $id;
			    }
			}else{
				$data['estado'] = false;
			    $data['msg'] 	= "¡Lo sentimos, la actividad no existe!";
			}

			echo json_encode($data, JSON_PRETTY_PRINT);
	    }

	    public function guardarActividadAction()
	    {
	    	$data['estado'] = true;

	    	$idact = $this->request->getPost("idActividad");

	    	if(!$idact){
	    		# para crear una nueva actividad
	    		$actividad = new Actividad();
	    		
	    		# al crearlo ponemos el bloque al que pertenece !
	    		$actividad->bloque_id 		= $this->request->getPost("idbloque", "int");

	    	} else {
	    		# para actualizar actividad existente
	    		$actividad = Actividad::findFirst($idact);
	    	}

	    	$hh_estimadas = $this->getFloatByHrs($this->request->getPost("horas"));
	    	$horas_reales = $this->getFloatByHrs($this->request->getPost("horas_reales"));

	    	if($hh_estimadas == 0){
	    		$data['estado'] = false;
	    		$data['msg'] 	= "Lo sentimos, debe incluir las horas estimadas";
	    	}

	    	if($data['estado'])
	    	{
	    		
			    $actividad->descripcion 	= $this->request->getPost("descripcion", 'string');
			    $actividad->hh_estimadas 	= $hh_estimadas;
			    $actividad->hh_reales 		= $horas_reales;
			    $actividad->fecha 			= $this->request->getPost("fecha");



		        if ($actividad->save())
		        {
		        	$data['estado'] = true;
		        	$data['msg']	= "Actividad creada";
		        	$data['id'] 	= $actividad->id;
		        } else {
		        	$data['estado'] = false;
		        	$data['msg'] 	= "Lo sentimos, los siguientes errores ocurrieron mientras te dabamos de alta: ";

		            foreach ($actividad->getMessages() as $message) {
		                $data['detalleError'][] = $message->getMessage();;
		            }
		        }
	    	}

	        echo json_encode($data, JSON_PRETTY_PRINT);
	    }

	    public function cargarRegistrosAction()
	    {
	    	$proyecto 	= 	$this->request->getPost("proyecto", "int");
	    	$fecha 		=	$this->request->getPost("fecha");
	    	$usuario 	=	$this->auth->getIdentity()['id'];

	    	$bloques = Bloque::find(
	    		array(
	    			"proyecto_id = ".$proyecto,
	    			"usuario_id  = ".$usuario
	    		)
	    	);

	    	$i =0 ;
	    	foreach ($bloques as $bloque)
	    	{
                $data['bloques'][$i]['id'] 			= $bloque->id;
                $data['bloques'][$i]['usuario_id'] 	= $bloque->usuario_id;
                $data['bloques'][$i]['horas'] 		= $bloque->horas;
                $data['bloques'][$i]['fecha'] 		= $bloque->fecha;
                $data['bloques'][$i]['orden'] 		= $bloque->orden;

                $j = 0;
                foreach ($bloque->actividad as $actividad) {
                	$data['bloques'][$i]['actividades'][$j]['id'] 			= $actividad->id;
                    $data['bloques'][$i]['actividades'][$j]['hh_estimadas'] = $this->floatToTime($actividad->hh_estimadas);
                    $data['bloques'][$i]['actividades'][$j]['hh_reales'] 	= $this->floatToTime($actividad->hh_reales);
                    $data['bloques'][$i]['actividades'][$j]['descripcion'] 	= $actividad->descripcion;

                    $j++;
                }
                $i++;
            }

            if($i>0){
            	$data['nbloques'] = $i;
				$data['estado'] = true;
				$data['msg']	= "se cargaron ".$i." bloques.";
            }else{
            	$data['estado'] = false;
            	$data['msg']	= "no se encontraron resultados";
            }
            
	    	echo json_encode($data, JSON_PRETTY_PRINT);
	    }

	    private function deleteActividadByBloque($bloque)
	    {
	    	// eliminamos las actividades pertenecientes al bloque
	    	foreach (Actividad::find("bloque_id=".$bloque) as $actividad)
	    	{
			    $actividad->delete();
			}

			return true;
	    }

	    private function getFloatByHrs($horas)
	    {
	    	$arr = explode(':', $horas);
	    	$h = $arr[0];
	    	$m = $arr[1];

			$num = ( ($h*3600 + $m*60) /60)/60;

			return floatval(round($num, 3));
	    }

	    private function floatToTime($float)
	    {
	    	return sprintf('%02d:%02d', (int) $float, fmod($float, 1) * 60);
	    }
	}