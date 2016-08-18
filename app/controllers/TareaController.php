<?php
    namespace Gabs\Controllers;

    use Gabs\Models\Bloque;
    use Gabs\Models\Proyecto;
    use Gabs\Models\Prioridad;
    use Gabs\Models\Estado;
    use Gabs\Models\Equipo;

    use Gabs\Valida\Valida;
    
    class TareaController extends ControllerBase
    {
        public function initialize(){}

        public function indexAction() {

        	$data['tareas'] 	= Bloque::find();
        	$data['estados'] 	= Estado::find();
        	$data['proyectos'] 	= Proyecto::find();
        	$data['equipos']	= Equipo::find();

        	$content    = 'tareas/list';
            $menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';
            $addJs[]  	= "js/tarea.js";

            echo $this->view->render('theme',array('topMenu'=>$menu,
                                                    'menuSel'=>'', 
                                                    'sideBar'=>$sideBar, 
                                                    'sideBarSel'=>'gestion', 
                                                    'pcView'=>$content,
                                                    'addJs' => $addJs,
                                                    'pcData'=>$data)
                                    ); 

        }

        public function createAction() {

        	$data['proyectos'] = Proyecto::find();
        	$data['prioridades'] = Prioridad::find();

        	$content    = 'tareas/crea';
            $menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';
            $addJs[]  	= "js/tarea.js";

            echo $this->view->render('theme',array('topMenu'=>$menu,
                                                    'menuSel'=>'', 
                                                    'sideBar'=>$sideBar, 
                                                    'sideBarSel'=>'gestion', 
                                                    'pcView'=>$content,
                                                    'pcData'=>$data)
                                    ); 
        }

        public function editAction($id) {

        	if($id>0)
		 	{
		 		$data['tarea'] 			= Bloque::findFirst($id);
		 		$data['proyectos'] 		= Proyecto::find();
	        	$data['prioridades'] 	= Prioridad::find();

	        	$content    = 'tareas/edit';
	            $menu       = 'menu/topMenu';
	            $sideBar    = 'menu/sideBar';
	            $addJs[]  	= "js/tarea.js";

	            echo $this->view->render('theme',array('topMenu'=>$menu,
	                                                    'menuSel'=>'', 
	                                                    'sideBar'=>$sideBar, 
	                                                    'sideBarSel'=>'gestion', 
	                                                    'pcView'=>$content,
	                                                    'pcData'=>$data)
	                                    ); 

		 	
		 	}else{
				$response = new \Phalcon\Http\Response();
				$response->redirect("acceso/denegado");
				$response->send();
		 	}
        }

        public function updateAction()
        {
        	
        }

        public function deleteAction()
        {
        	
        }

        public function storeAction() {

        	if ($this->request->isPost())
	    	{
	    		# Valida
		    	$valida = new Valida($_POST,[
	                'nombre'      	=>  'required|string',
	                'proyecto'		=>	'required|int|min:1',
	                'prioridad'		=>	'required|int|min:1',
	                'descripcion'	=>	'required|string',
	                'horas'			=>	'int|min:0',
	                'fecha'			=>	'date'

	            ]);

	            if($valida->failed()) {
	                foreach ($valida->errors as $message) {
	                	$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$message}',{type:'danger'});");
	                }
	                $this->mifaces->run();
	                return false;
	            }


	            
		    	$bloque = new Bloque();//tarea
		    	$bloque->nombre 			= $this->request->getPost("nombre", 'string');
		    	$bloque->proyecto_id 		= $this->request->getPost("proyecto", 'int');
		    	$bloque->prioridad_id		= $this->request->getPost("prioridad", 'int');
		    	$bloque->descripcion		= $this->request->getPost("descripcion", 'string');
		    	$bloque->hh_estimadas		= $this->request->getPost("horas", 'int');
		    	$bloque->fecha_termino		= $this->request->getPost("fecha");

		    	$bloque->creado_por			= $this->auth->getIdentity()['id'];
		    	$bloque->orden 				= 1;
		    	$bloque->estado_id 			= 1;
		    	$bloque->fecha 				= date('Y-m-d H:i:s');// fecha creación

				$this->mifaces->newFaces();

				if($bloque->save() == false)
				{
					foreach ($bloque->getMessages() as $message) {
						$val = $message->getMessage();
	                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
	                }

	                $this->mifaces->run();
				}else{

					$val = "Tarea creada correctamente";
					$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
					$this->mifaces->addPosRendEval("window.location.replace('/sgpp/tarea');");
		            $this->mifaces->run(); 
				}
			}
        }

        public function getListaTareasAction()
        {
        	try {

        		$data['estado'] = true;
        		$tareas = Bloque::find(array('order'=>' fecha_termino DESC, prioridad_id DESC '));

        		$data['tareas'] = array();

        		foreach ($tareas as $tarea) {

        			if(!empty($tarea->nombre)){
        				$date = new \DateTime($tarea->fecha_termino);
	        			
	        			$class = new \stdClass();
	        			$class->id 		= $tarea->id;
	        			$class->fecha 	= $date->format('Y-m-d');
	        			$class->nombre	= $tarea->nombre;
	        			$class->desc 	= $tarea->descripcion;
	        			$class->prior 	= $tarea->prioridad_id;
	        			$class->estado 	= $tarea->estado_id;

	        			if(!empty($tarea->proyecto_id)){
	        				$class->proy 	= $tarea->proyecto->proy_nombre;
	        			}else{
	        				$class->proy 	= null;
	        			}
	        			

	        			array_push($data['tareas'], $class);
        			}
        		}


        		
        	} catch (Exception $e) {
        		$data['estado'] = false;
        	}
        	
        	echo json_encode($data);
        }

        public function getTareaAction()
        {
        	$id 	= $this->request->getPost("tarea", 'int');

    		if($id > 0)
    		{
	        	try {

	        		$data['estado'] = true;
	        		$data['tarea'] 	= Bloque::findFirst($id);	        		
	        		
	        	} catch (Exception $e) {
	        		$data['estado'] = false;
	        		$data['msg']	= "Problemas al cargar la tarea";
	        	}

	        } else {
				$response = new \Phalcon\Http\Response();
				$response->redirect("acceso/denegado");
				$response->send();
		 	}
        	
        	echo json_encode($data);
        }
    }