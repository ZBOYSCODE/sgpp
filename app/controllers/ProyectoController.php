<?php

	namespace Gabs\Controllers;

	use Gabs\Models\Proyecto;
	use Gabs\Models\Equipo;
	use Gabs\Models\Users;
	use Gabs\Models\Roles;

	use Phalcon\Paginator\Adapter\Model as PaginatorModel;
	use Gabs\Valida\Valida;

	class ProyectoController extends ControllerBase{


	    public function initialize()
	    {
		}

		public function indexAction() {

			# Paginación
	    	$nombre 		= $this->request->get("name", 'string');
	    	$email 			= $this->request->get("email", 'string');
			//$user 		= $this->request->get("user", 'int');
			$currentPage	= $this->request->get("page", 'int');

			if(empty($currentPage)){
				$currentPage = 1;
			}


			//prueba
			//$nombre = "Bicorp";

			# Filtro
			$buscar = array();
			if(!empty($nombre)) $buscar['proy_nombre']	= trim($nombre);
			if(!empty($email)) $buscar['email']	= trim($email);



			$model = new Proyecto();

			$query = self::fromInput($this->di, $model, $buscar);

			$sentencia = $query->getParams();
			$sentencia['order'] = 'created_at desc';
		
			$proyectos = Proyecto::find($sentencia);


			$paginator   = new PaginatorModel(
			    array(
			        "data"  => $proyectos,
			        "limit" => 10,
			        "page"  => $currentPage
			    )
			);

			$data['page'] = $paginator->getPaginate();


	    	$content    = 'proyectos/list';
            $jsScript   = '';
            $menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';
            $addCss[]   = "";

            $addJs[]  	= "js/proyecto.js";

            $pcData = $data;


            echo $this->view->render('theme',array('topMenu'=>$menu,
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


		public function createAction(){

	    	# Form creación
	    	$data['users'] = Users::findByRolId($this->rol_jefep);
	    	$data['areas'] = Area::find();
	    	$data['tecnologias'] = Tecnologia::find();


	    	$themeArray = $this->_themeArray;
    		$themeArray['pcView'] = 'proyectos/crear';
	        $themeArray['pcData'] = $data;

        	echo $this->view->render('theme', $themeArray);
	    }

	    public function storeAction()
	    {
	    	if ($this->request->isPost())
	    	{
		    	$proyecto = new Proyecto();

		    	$proyecto->nombre 			= $this->request->getPost("nombre", 'string');
		    	$proyecto->codigo			= $this->request->getPost("codigo", 'string');
		    	$proyecto->descripcion 		= $this->request->getPost("descripcion", 'string');
		    	$proyecto->creador_id  		= $this->auth->getIdentity()['id'];
		    	$proyecto->jefep_id 		= $this->request->getPost("jefep", 'int');
		    	$proyecto->area_id 			= $this->request->getPost("area", 'int');
		    	$proyecto->tecnologia_id 	= $this->request->getPost("tecnologia", 'int');
		    	$proyecto->created_at 		= date('Y-m-d H:i:s');
		    	$proyecto->updated_at 		= date('Y-m-d H:i:s');

				$this->mifaces->newFaces();

				if($proyecto->save() == false)
				{
					foreach ($proyecto->getMessages() as $message) {
						$val = $message->getMessage();
	                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
	                }

	                $this->mifaces->run();
				}else{
					$val = "Proyecto creado correctamente";
					$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
					$this->mifaces->addPosRendEval("window.location.replace('/qalendar/proyecto');");
		            $this->mifaces->run(); 
				}
			}
	    }

	    public function editAction($id){

	    	$id = (int)$id;

		 	if($id>0)
		 	{
		 		# Form edit
		    	$data['users'] = Users::find(" rol_id = 4 ");
		    	$data['equipos'] = Equipo::find();

		    	$data['proyecto'] = Proyecto::findFirst($id);


		        $content    = 'proyectos/edit';
	            $jsScript   = '';
	            $menu       = 'menu/topMenu';
	            $sideBar    = 'menu/sideBar';
	            $addCss[]   = "";

	            $addJs[]  	= "js/proyecto.js";

	            $pcData = $data;


	            echo $this->view->render('theme',array('topMenu'=>$menu,
	                                                    'menuSel'=>'', 
	                                                    'sideBar'=>$sideBar, 
	                                                    'sideBarSel'=>'gestion', 
	                                                    'pcView'=>$content,
	                                                    'pcData'=>$pcData, 
	                                                    'jsScript' => $jsScript,
	                                                    'addJs' => $addJs,
	                                                    'addCss' => $addCss)
	                                    ); 

		 	
		 	}else{
				$response = new \Phalcon\Http\Response();
				$response->redirect("acceso/denegado");
				$response->send();
		 	}
		 		
	    }

	    public function updateAction()
	    {
	    	if ($this->request->isPost())
	    	{
	    		$id 	= $this->request->getPost("proyecto_id", 'int');

	    		if($id > 0)
	    		{
	    			$proyecto = Proyecto::findFirst($id);

	    			
			    	# Valida
			    	$valida = new Valida($_POST,[
		                'nombre'      	=>  'required|string',
		                'descripcion'  	=>  'required|string',
		                'jefep'      	=>  'required|int',
		                'equipo'      	=>  'required|int',
		                'coordinador'	=>	'required|int'
		            ]);

		            if($valida->failed()) {

		                foreach ($valida->errors as $message) {
		                	$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$message}',{type:'danger'});");
		                }

		                $this->mifaces->run();
		                return false;
		            }


		            $proyecto->name 			= $this->request->getPost("nombre", 'string');
			    	$proyecto->descripcion 		= $this->request->getPost("descripcion", 'string');
			    	$proyecto->jefeproyecto_id 	= $this->request->getPost("jefep", 'int');
			    	$proyecto->equipo_id 		= $this->request->getPost("equipo", 'int');
			    	$proyecto->coordinador_id 	= $this->request->getPost("coordinador", 'int');
			    	$proyecto->updated_at 		= date('Y-m-d H:i:s');




			    	$this->mifaces->newFaces();

					if($proyecto->save() == false)
					{
						foreach ($proyecto->getMessages() as $message) {
							$val = $message->getMessage();
		                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
		                }

		                $this->mifaces->run();
					}else{
						$val = "Proyecto editado correctamente";
						$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
						//$this->mifaces->addPosRendEval("window.location.replace('/qalendar/proyecto');");
			            $this->mifaces->run(); 
					}
	    		}
		    }
	    }


	    public function deleteAction()
	    {

	    	try {

	    		$id = $this->request->getPost("proyecto", 'int');

	    		$proyecto = Proyecto::findFirst($id);

	    		// aquí irán las restricciones
	    		// ejem: no se podrán cancelar a cierta hora de realizarse la actividad
	    		// $se_puede = true/false
	    		// si es false, guardar en la variable $data['msg'] la razón 
	    		$se_puede = true;


	    		if($se_puede)
	    		{
	    			$proyecto->proy_activo = 0;

		    		if(!$proyecto->save()){
		    			$data['estado'] = false;
		    			$data['msg'] 	= "No se ha podido eliminar el proyecto.";
		    		}else{
		    			$data['estado'] = true;
		    			$data['msg'] 	= "Proyecto eliminado correctamente.";
		    		}

	    		}else{
	    			$data['msg'] 	= 'Se cancela la eliminación del proyecto por restricción';
	    			$data['estado'] = false;
	    		}
	    		
	    	} catch (Exception $e) {
	    		$data['estado'] = false;
	    		$data['msg'] = "Error al tratar de eliminar el proyecto.";
	    	}

	    	echo json_encode($data);
	    }

	    public function activarAction()
	    {
	    	try {

	    		$id = $this->request->getPost("proyecto", 'int');

	    		$proyecto = Proyecto::findFirst($id);
	    		$proyecto->proy_activo = 1;

	    		if(!$proyecto->save()){
	    			$data['estado'] = false;
	    			$data['msg'] = "no se ha podido activar el evento.";
	    		}else{
	    			$data['estado'] = true;
	    			$data['msg'] = "Proyecto activado correctamente.";
	    		}

	    	} catch (Exception $e) {
	    		$data['estado'] = false;
	    		$data['msg'] = "Error activando el evento.";
	    	}

	    	echo json_encode($data);
	    }

	    public function getCoordinadoresAction()
	    {

	    	try {
	    		$id = $this->request->getPost("equipo", 'int');

		    	$equipo = Equipo::findFirst($id);




		    	$user = array(0=>'Seleccione...');

		    	
		    	foreach ($equipo->getUsuarios() as $usuario) {
		    		$user[$usuario->id] =  $usuario->name;
		    	}

		    	$data['usuarios'] = $user;
		    	$data['estado'] = true;
	    	} catch (Exception $e) {
	    		$data['estado'] = false;
	    	}
		    	

	    	echo json_encode($data);

	    	
	    }
	}