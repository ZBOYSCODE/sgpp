<?php

	namespace Gabs\Controllers;

	use Gabs\Models\Users;
	use Gabs\Models\Equipo;
	use Gabs\Models\EquipoUser;

	use Phalcon\Paginator\Adapter\Model as PaginatorModel;
	use Gabs\Valida\Valida;

	class EquipoController extends ControllerBase{


	    public function initialize()
	    {
		}

		public function indexAction() {

			# Paginación
	    	$nombre 		= $this->request->get("name", 'string');
			$currentPage	= $this->request->get("page", 'int');

			if(empty($currentPage)){
				$currentPage = 1;
			}


			# Filtro
			$buscar = array();
			if(!empty($nombre)) $buscar['proy_nombre']	= trim($nombre);
			//if(!empty($email)) $buscar['email']	= trim($email);



			$model = new Equipo();

			$query = self::fromInput($this->di, $model, $buscar);

			$sentencia = $query->getParams();
			$sentencia['order'] = 'id asc';
		
			$equipo = Equipo::find($sentencia);


			$paginator   = new PaginatorModel(
			    array(
			        "data"  => $equipo,
			        "limit" => 10,
			        "page"  => $currentPage
			    )
			);

			$data['page'] = $paginator->getPaginate();


	    	$content    = 'equipo/list';
            $jsScript   = '';
            $menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';
            $addCss[]   = "";

            $addJs[]  	= "js/equipo.js";

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

	    	$content    = 'equipo/crear';
            $jsScript   = '';
            $menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';
            $addCss[]   = "";

            $pcData['usuarios'] = Users::find("rol_id = 2");


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

	    public function storeAction()
	    {
	    	if ($this->request->isPost())
	    	{

	    		# Valida
		    	$valida = new Valida($_POST,[
	                'nombre'      	=>  'required|string'
	            ]);

	            if($valida->failed()) {

	                foreach ($valida->errors as $message) {
	                	$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$message}',{type:'danger'});");
	                }

	                $this->mifaces->run();
	                return false;
	            }


		    	$equipo = new Equipo();
		    	$equipo->nombre 			= $this->request->getPost("nombre", 'string');

				$this->mifaces->newFaces();

				if($equipo->save() == false)
				{
					foreach ($proyecto->getMessages() as $message) {
						$val = $message->getMessage();
	                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
	                }

	                $this->mifaces->run();

				}else{

					$val = "Equipo creado correctamente";
					$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
					$this->mifaces->addPosRendEval("window.location.replace('/sgpp/equipo');");


					$users = $this->request->getPost("usuarios");

					if(is_array($users)){
						foreach ($users as $usuario) {

							$usr = new EquipoUser();
							$usr->equipo_id = $equipo->id;
							$usr->user_id = $usuario;
							$usr->save();

							unset($usr);
						}
					}

		            $this->mifaces->run(); 
				}
			}
	    }

	    public function editAction($id){

	    	$id = (int)$id;

		 	if($id>0)
		 	{
		 		# Form edit
		    	$data['equipo'] 	= Equipo::findFirst($id);
		    	$data['usuarios'] 	= Users::find("rol_id = 2");

		        $content    = 'equipo/edit';
	            $jsScript   = '';
	            $menu       = 'menu/topMenu';
	            $sideBar    = 'menu/sideBar';
	            $addCss[]   = "";

	            $addJs[]  	= "js/equipo.js";

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
	    		$id 	= $this->request->getPost("equipo_id", 'int');

	    		if($id > 0)
	    		{
	    			$equipo = Equipo::findFirst($id);

	    			
			    	# Valida
			    	$valida = new Valida($_POST,[
		                'nombre'      	=>  'required|string'
		            ]);

		            if($valida->failed()) {

		                foreach ($valida->errors as $message) {
		                	$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$message}',{type:'danger'});");
		                }

		                $this->mifaces->run();
		                return false;
		            }


		            $equipo->nombre 			= $this->request->getPost("nombre", 'string');


			    	$this->mifaces->newFaces();

					if($equipo->save() == false)
					{
						foreach ($proyecto->getMessages() as $message) {
							$val = $message->getMessage();
		                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
		                }

		                $this->mifaces->run();
					}else{
						$val = "Equipo editado correctamente";
						$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
						//$this->mifaces->addPosRendEval("window.location.replace('/qalendar/proyecto');");

						if($this->deleteUsuarios($id)) {

							$users = $this->request->getPost("usuarios");

							if(is_array($users)) {

								foreach ($users as $usuario) {
									$usr = new EquipoUser();
									$usr->equipo_id = $equipo->id;
									$usr->user_id = $usuario;
									$usr->save();
									unset($usr);
								}
							}
						}

							


			            $this->mifaces->run(); 
					}
	    		}
		    }
	    }


	    public function deleteAction()
	    {

	    	try {

	    		$id = $this->request->getPost("equipo", 'int');

	    		$equipo = Equipo::findFirst($id);

				if($this->deleteUsuarios($id)){
					if(!$equipo->delete()){
		    			$data['estado'] = false;
		    			$data['msg'] 	= "No se ha podido eliminar el equipo.";
		    		}else{
		    			$data['estado'] = true;
		    			$data['msg'] 	= "Equipo eliminado correctamente.";
		    		}
				}
	    		
	    	} catch (Exception $e) {
	    		$data['estado'] = false;
	    		$data['msg'] = "Error al tratar de eliminar el proyecto.";
	    	}

	    	echo json_encode($data);
	    }

	    public function deleteUsuarios($equipo)
	    {
	    	$equipousuarios = EquipoUser::findByEquipoId($equipo);

	    	foreach ($equipousuarios as $equipouser) {
	    		$equipouser->delete();
	    	}

	    	return true;
	    }

	    public function getUsuariosAction()
	    {
	    	$id 	= $this->request->getPost("id", 'int');

	    	$equipo = Equipo::findFirst($id);

	    	$data['estado'] = true;
	    	$data['usuarios'] = $equipo->usuarios->toArray();


	    	echo json_encode($data);
	    }

	   
	}