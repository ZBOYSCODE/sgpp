<?php

	namespace Gabs\Controllers;

	use Gabs\Models\Users;
	use Gabs\Models\Roles;

	use Phalcon\Paginator\Adapter\Model as PaginatorModel;

	class UsuariosController extends ControllerBase{


	    public function initialize()
	    {
		}

		public function indexAction(){

			# Paginación
	    	$nombre 		= $this->request->get("name", 'string');
	    	$email 			= $this->request->get("email", 'string');
			//$user 		= $this->request->get("user", 'int');
			$currentPage	= $this->request->get("page", 'int');

			if(empty($currentPage)){
				$currentPage = 1;
			}

			# Filtro
			$buscar = array();
			if(!empty($nombre)) $buscar['name']	= trim($nombre);
			if(!empty($email)) $buscar['email']	= trim($email);



			$model = new Users();

			$query = self::fromInput($this->di, $model, $buscar);

			$this->persistent->searchParams = $query->getParams();
			
			$usuarios = Users::find($this->persistent->searchParams);

			$paginator   = new PaginatorModel(
			    array(
			        "data"  => $usuarios,
			        "limit" => 10,
			        "page"  => $currentPage
			    )
			);

			$data['page'] = $paginator->getPaginate();


	    	$content    = 'usuarios/list';
            $jsScript   = '';
            $menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';
            $addCss[]   = "";

            $addJs[]  = "js/useradmin.js";

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

		public function createAction()
		{
			# Form creación

	    	$data['roles'] = Roles::find(" id > 1 ");


    		$pcView 	= 'usuarios/crear';
    		$menu       = 'menu/topMenu';
            $sideBar    = 'menu/sideBar';

	        $pcData = $data;

        	echo $this->view->render('theme',array('topMenu'=>$menu,
                                                    'menuSel'=>'', 
                                                    'sideBar'=>$sideBar, 
                                                    'sideBarSel'=>'gestion', 
                                                    'pcView'=>$pcView,
                                                    'pcData'=>$pcData)
                                    ); 
		}

		public function storeAction()
	    {
	    	if ($this->request->isPost())
	    	{

				$this->mifaces->newFaces();

				$usr = Users::findByEmail($this->request->getPost("email", 'email'));

				if(count($usr) > 0){
					$val = "El email ya se encuentra registrado.";
					$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
		    		$this->mifaces->run();
					return false;
				}

				
		    	$users = new Users();

		    	$users->name 		= $this->request->getPost("nombre", 'string');
		    	$users->email		= $this->request->getPost("email", 'email');
		    	$users->rol_id 		= $this->request->getPost("rol", 'int');
		    	$users->rut 		= $this->request->getPost("rut", 'string');
		    	$users->profilesId	= 2;
		    	$users->banned 		= 'N';
		    	$users->suspended 	= 'N';
		    	$users->active 		= 'Y';
		    	$users->createByAdmin = 'Y';

		    	$pass1 = $this->request->getPost('pass');
		    	$pass2 = $this->request->getPost('pass2');

		    	if($pass1 != $pass2)
		    	{
		    		$val = "Las contraseñas no coinciden.";
					$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
		    		$this->mifaces->run();
		    		return false;
		    	}

		    	$users->password 	= $this->security->hash($pass1);
		    		
	    		if($users->save() == false)
				{
					foreach ($users->getMessages() as $message) {
						$val = $message->getMessage();
	                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
	                }

	                $this->mifaces->run();
				}else{
					$val = "Usuario creado correctamente";
					$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
					$this->mifaces->addPosRendEval("window.location.replace('/sgpp/usuarios');");
		            $this->mifaces->run(); 
				}
			}
	    }

		public function editAction($id){

	    	$id = (int)$id;

		 	if($id>0)
		 	{
		 		# Form edit
		    	$data['roles'] = Roles::find(" id > 1 ");

		    	$data['usuario'] = Users::findFirst($id);

		    	$data['estados'] = array( 'Y' => "Sí", 'N' => "No" );


		        $pcView 	= 'usuarios/edit';
	    		$menu       = 'menu/topMenu';
	            $sideBar    = 'menu/sideBar';

		        $pcData = $data;

	        	echo $this->view->render('theme',array('topMenu'=>$menu,
	                                                    'menuSel'=>'', 
	                                                    'sideBar'=>$sideBar, 
	                                                    'sideBarSel'=>'gestion', 
	                                                    'pcView'=>$pcView,
	                                                    'pcData'=>$pcData)
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
	    		$id 	= $this->request->getPost("user_id", 'int');

	    		if($id > 0)
	    		{
	    			$user = Users::findFirst($id);

			    	$user->name 			= $this->request->getPost("nombre", 'string');
			    	$user->rut				= $this->request->getPost("rut", 'string');
			    	$user->rol_id 			= $this->request->getPost("rol", 'int');

			    	$this->mifaces->newFaces();

					if($user->save() == false)
					{
						foreach ($user->getMessages() as $message) {
							$val = $message->getMessage();
		                    $this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}',{type:'danger'});");
		                }

		                $this->mifaces->run();
					}else{
						$val = "Usuario editado correctamente.";
						$this->mifaces->addPosRendEval("$.bootstrapGrowl('{$val}');");
						//$this->mifaces->addPosRendEval("window.location.replace('/qalendar/user');");
			            $this->mifaces->run(); 
					}
	    		}
		    }
	    }

	    public function deleteAction()
	    {

	    	try {

	    		$id = $this->request->getPost("user_id", 'int');

	    		$usuario = Users::findFirst($id);

	    		$usuario->delete = 1;

	    		if(!$usuario->save()){
	    			$data['estado'] = false;
	    			$data['msg'] 	= "No se ha podido eliminar el usuario.";

	    			foreach ($usuario->getMessages() as $message) {
						$data['detalle'][] = $message->getMessage();
	                }
	    		}else{
	    			$data['estado'] = true;
	    			$data['msg'] 	= "Usuario eliminado correctamente.";
	    		}
	    		
	    	} catch (Exception $e) {
	    		$data['estado'] = false;
	    		$data['msg'] = "Error al tratar de eliminar el usuario.";
	    	}

	    	echo json_encode($data);
	    }

	    public function activaAction()
	    {

	    	try {

	    		$id = $this->request->getPost("user_id", 'int');

	    		$usuario = Users::findFirst($id);

	    		$usuario->delete = 0;

	    		if(!$usuario->save()){
	    			$data['estado'] = false;
	    			$data['msg'] 	= "No se ha podido activar el usuario.";

	    			foreach ($usuario->getMessages() as $message) {
						$data['detalle'][] = $message->getMessage();
	                }
	    		}else{
	    			$data['estado'] = true;
	    			$data['msg'] 	= "Usuario activado correctamente.";
	    		}
	    		
	    	} catch (Exception $e) {
	    		$data['estado'] = false;
	    		$data['msg'] = "Error al tratar de activar el usuario.";
	    	}

	    	echo json_encode($data);
	    }

		public 	function profileAction()
		{
			$auth=$this->auth->getIdentity();
			if($auth['email']==$this->dispatcher->getParam("us")){
				$idUser = $auth['id'];
				$userProfile = Users::findFirst($idUser)->toArray();
				$pcData['pcData']['userProfile'] = $userProfile;
				$pcData['pcView']	= "usuarios/profile_edit_view";
				$pcData['topMenu'] = true;
				$pcData['menuSel'] = '';
				$pcData['sideBarSel'] = 'home';
				$pcData['jsScript'] = '';
				$pcData['lmView'] = '';


				print($this->view->render('theme',$pcData));
			}else{
				$response = new \Phalcon\Http\Response();
				$response->redirect("");
				$response->send();
			}

		}

		public function editProfileAction()
		{

			$error = 0;
	        $this->mifaces->newFaces();
	        if($_POST['userPassword']!="")
	        {
				if($_POST['userPassword'] !== $_POST['userConfirmPassword'] OR ($_POST['userPassword'] == "")){
		            $this->mifaces->addPosRendEval("$.bootstrapGrowl('Las contraseñas no coinciden, vuelva a ingresar.',{type:'warning',align:'center'});");
		            $error = 1;
		        }  else{
		            $passwordHashed = $this->getDI()
		                ->getSecurity()
		                ->hash($_POST['userPassword']);              
		        }
	    	}
	        if($_POST['userNombre'] == "" OR $_POST['userEmail'] == ""){
	            $this->mifaces->addPosRendEval("$.bootstrapGrowl('Falta rellenar campos requeridos.',{type:'warning',align:'center'});");
	        } elseif(!$error){
		        $modelUser = new Users();
		        $user = $modelUser::findFirstById($this->auth->getIdentity()['id']);
		        if(isset($passwordHashed))
		        	$user->password = $passwordHashed;
		        $user->name = $_POST['userNombre'];
		        $user->email = $_POST['userEmail'];
		        $user->update();
		        $this->auth->authUserById($this->auth->getIdentity()['id']);
	            $this->mifaces->addPosRendEval("$.bootstrapGrowl('Datos actualizados correctamente.',{type:'success',align:'center'});");	        	        	
	        }

	        $this->mifaces->run();		
		}


		

	}
	?>