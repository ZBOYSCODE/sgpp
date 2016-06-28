<?php
	namespace Gabs\Controllers;

	use Gabs\Models\Personas;
	use Gabs\Models\Proyecto;
	use Gabs\Models\Estado;
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

	    	$pcData['proyectos'] 	= Proyecto::find();

	    	date_default_timezone_set('America/Santiago');
	    	$pcData['fecha'] 		= date('Y-m-d');


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


	    public function getProyectosAction()
        {
            $proyectos = Proyecto::find();
            $arr = array();

            foreach ($proyectos as $proyecto) {
                $arr[$proyecto->proy_id] = $proyecto->proy_nombre;
            }

            $data['estado'] = true;
            $data['proyectos'] = $arr;

            echo json_encode($data);
        }

        public function getEstadosAction()
        {
            $estados = Estado::find();
            $arr = array();

            foreach ($estados as $estado) {
                $arr[$estado->id] = $estado->nombre;
            }

            $data['estado'] = true;
            $data['estados'] = $arr;

            echo json_encode($data);
        }

	    public function crearBloqueAction()
	    {
			$bloque = new Bloque();

		    //$bloque->proyecto_id 		= $this->request->getPost("proyecto", "int");
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

	    	$idact 		= 	$this->request->getPost("idActividad");

	    	$proyecto 	=	$this->request->getPost("proyecto", 'int');
	    	$estado 	=	$this->request->getPost("estado", 'int');

	    	if(!$idact){
	    		# para crear una nueva actividad
	    		$actividad = new Actividad();
	    		
	    		# al crearlo ponemos el bloque y el proyecto al que pertenece !
	    		$actividad->bloque_id 		= $this->request->getPost("idbloque", "int");

	    	} else {
	    		# para actualizar actividad existente
	    		$actividad = Actividad::findFirst($idact);
	    	}

	    	$hh_estimadas = $this->getIntByHrs($this->request->getPost("horas"));
	    	$horas_reales = $this->getIntByHrs($this->request->getPost("horas_reales"));

	    	if($hh_estimadas == 0){
	    		$data['estado'] = false;
	    		$data['msg'] 	= "Lo sentimos, debe incluir las horas estimadas";
	    	}

	    	if($proyecto == 0){
	    		$data['estado'] = false;
	    		$data['msg'] 	= "Lo sentimos, debe seleccionar un proyecto";
	    	}

	    	if($estado == 0){
	    		$data['estado'] = false;
	    		$data['msg'] 	= "Lo sentimos, debe seleccionar un estado";
	    	}

	    	if($data['estado'])
	    	{
	    		$actividad->proyecto_id		= $proyecto;
			    $actividad->descripcion 	= $this->request->getPost("descripcion", 'string');
			    $actividad->hh_estimadas 	= $hh_estimadas;
			    $actividad->hh_reales 		= $horas_reales;
			    $actividad->fecha 			= $this->request->getPost("fecha");
			    $actividad->estado_id		= $estado;


		        if ($actividad->save())
		        {
		        	$data['estado'] = true;
		        	$data['msg']	= "Actividad creada";
		        	$data['id'] 	= $actividad->id;
		        	$data['nombre_proyecto'] = $actividad->proyecto->proy_nombre;
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
	    	//$proyecto 	= 	$this->request->getPost("proyecto", "int");
	    	$fecha 		=	$this->request->getPost("fecha");
	    	$usuario 	=	$this->auth->getIdentity()['id'];

	    	// Query robots binding parameters with both string and integer placeholders
			$conditions = "usuario_id = :usuario:
						   AND fecha = :fecha:";

			// Parameters whose keys are the same as placeholders
	    	$params = array(
		    			"usuario"  	=> $usuario,
		    			"fecha" 	=> $fecha
		    		);

	    	$bloques = Bloque::find(array(
	    			$conditions,
	    			"bind" => $params
	    	));


	    	$i =0 ;
	    	foreach ($bloques as $bloque)
	    	{
                $data['bloques'][$i]['id'] 			= $bloque->id;
                $data['bloques'][$i]['usuario_id'] 	= $bloque->usuario_id;
                $data['bloques'][$i]['horas'] 		= $bloque->horas;
                $data['bloques'][$i]['fecha'] 		= $bloque->fecha;
                $data['bloques'][$i]['orden'] 		= $bloque->orden;

                $cntHrsR = 0;
                $cntHrsE = 0;

                $j = 0;
                foreach ($bloque->actividad as $actividad) {
                	$data['bloques'][$i]['actividades'][$j]['id'] 			= $actividad->id;
                	$data['bloques'][$i]['actividades'][$j]['proyecto_id'] 	= $actividad->proyecto_id;
                    $data['bloques'][$i]['actividades'][$j]['hh_estimadas'] = $this->IntToTime($actividad->hh_estimadas);
                    $data['bloques'][$i]['actividades'][$j]['hh_reales'] 	= $this->IntToTime($actividad->hh_reales);
                    $data['bloques'][$i]['actividades'][$j]['descripcion'] 	= $actividad->descripcion;
                    $data['bloques'][$i]['actividades'][$j]['estado_id'] 	= $actividad->estado_id;

                    $cntHrsE+=$actividad->hh_estimadas;
                    $cntHrsR+=$actividad->hh_reales;

                    $j++;
                }

                $data['bloques'][$i]['cntHrsR'] = $this->IntToTime($cntHrsR);
                $data['bloques'][$i]['cntHrsE'] = $this->IntToTime($cntHrsE);

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

	    public function diferenciaHoraBloqueAction()
	    {
	    	$horas 		= 	$this->request->getPost("horas", "string");
	    	$hrsBloque	=	$this->config->actividades->horas;

	    	$val1 = '2001-01-01 '.$horas.":00";
			$val2 = '2001-01-01 '.$hrsBloque.":00";

			$datetime1 = new \DateTime($val1);
			$datetime2 = new \DateTime($val2);


			if($datetime1 > $datetime2){
				$data['estado'] = false;
				$data['msg'] = "El total de las horas es mayor a la configurada por bloque";
			}
			else
			 	$data['estado']	= true;

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

	    private function getIntByHrs($horas)
        {
        	if($horas != ''){
        		
        		$arr = explode(':', $horas);
	            $h = $arr[0];
	            $m = $arr[1];

	            if($h>0){
	                $hrs = $h*60;
	            }else{
	                $hrs = 0;
	            }

	            return $hrs+$m;
        	}else{
        		return 0;
        	}
	            
        }

        private function IntToTime($int)
        {
            $min = $int % 60;//min
            $hrs = floor($int / 60);//hrs

            if($min<10){
                $min = "0".$min;
            }

            if($hrs<10){
                $hrs = "0".$hrs;
            }

            return $hrs.":".$min;
        }
	}