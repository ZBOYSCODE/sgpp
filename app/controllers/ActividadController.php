<?php
    namespace Gabs\Controllers;

    use Gabs\Models\Users;
	use Gabs\Models\Proyecto;
	use Gabs\Models\Bloque;
	use Gabs\Models\Actividad;
    use Gabs\Models\Estado;
    use Gabs\Models\Personas;

    class ActividadController extends ControllerBase
    {
    	public function indexAction()
	    {
			$this->listarAction();
	    }

	    public function listarAction()
	    {

	    	$content 	= 'actividad/listar';
	    	$jsScript 	= '';
	    	$menu 		= 'menu/topMenu';
	    	$sideBar 	= 'menu/sideBar';

	    	$addJs[] 	= "js/listar_actividades.js";
	    	$addCss[]	= "css/style_registros.css";

            date_default_timezone_set('America/Santiago');

	    	$pcData['fecha'] 		= date('Y-m-d');
            $pcData['estados']      = Estado::find();
            $pcData['usuarios']     = Personas::find();
            $pcData['proyectos']    = Proyecto::find();

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
	    


	    public function cargarRegistrosAction()
	    {

	    	$fecha      = $this->request->getPost("fecha");
            $proyecto   = $this->request->getPost('proyecto', 'int');
            $estado     = $this->request->getPost('estado', 'int'); 
            $usuario    = $this->request->getPost('usuario', 'string');
            $hhr        = $this->request->getPost('hhr');

            $where = 'b.fecha = :fecha:';
            $bind['fecha'] = $fecha;

            if($proyecto > 0){
                $where .= " AND a.proyecto_id = :proyecto: ";
                $bind['proyecto'] = $proyecto;
            }

            if($estado > 0){
                $where .= " AND a.estado_id = :estado: ";
                $bind['estado'] = $estado;
            }

            if($usuario > 0){
                $where .= " AND p.rut = :usuario: ";
                $bind['usuario'] = $usuario;
            }

            if($hhr == 'true'){
                $where .= " AND a.hh_reales = 0 ";
            }

	    	
            $act = $this->modelsManager->createBuilder()
                        ->from(array("a" => 'Gabs\Models\Actividad'))
                        ->join('Gabs\Models\Bloque', 'b.id = a.bloque_id' ,'b')
                        ->join('Gabs\Models\Users', 'b.usuario_id = u.id' ,'u')
                        ->join('Gabs\Models\Personas', 'p.rut = u.rut' ,'p')
                        ->where($where, $bind)
                        ->getQuery()
                        ->execute();

            foreach ($act as $actividad) {
                
                $user = $actividad->bloque->usuario;

                $data['user'][$user->id]['nombre'] = $user->name;


                $arr['id']              = $actividad->id;
                $arr['proyecto']        = $actividad->proyecto->proy_nombre;
                $arr['hh_estimadas']    = $this->IntToTime($actividad->hh_estimadas);
                $arr['hh_reales']       = $this->IntToTime($actividad->hh_reales);
                $arr['descripcion']     = $actividad->descripcion;
                $arr['estado']          = $actividad->estado->nombre;


                $data['user'][$user->id]['actividades'][] = $arr;

                if(!isset($data['user'][$user->id]['cntHrsR'])){
                    $data['user'][$user->id]['cntHrsR'] = 0;
                }

                if(!isset($data['user'][$user->id]['cntHrsE'])){
                    $data['user'][$user->id]['cntHrsE'] = 0;
                }

                $data['user'][$user->id]['cntHrsR'] += $actividad->hh_reales;
                $data['user'][$user->id]['cntHrsE'] += $actividad->hh_estimadas;
            }

            
	    	/*$i =0 ;

	    	foreach ($bloques as $bloque)
	    	{

                $cntHrsR = 0;
                $cntHrsE = 0;

                $j = 0;

                $data['user'][$bloque->usuario_id]['nombre'] = $bloque->usuario->name;


                foreach ($bloque->actividad as $actividad) {

                	$arr['id'] 			    = $actividad->id;
                	$arr['proyecto'] 		= $actividad->proyecto->proy_nombre;
                	$arr['hh_estimadas'] 	= $this->IntToTime($actividad->hh_estimadas);
                	$arr['hh_reales'] 	    = $this->IntToTime($actividad->hh_reales);
                	$arr['descripcion'] 	= $actividad->descripcion;
                    $arr['estado']          = $actividad->estado->nombre;

                	$data['user'][$bloque->usuario_id]['actividades'][] = $arr;

                    $cntHrsE+=$actividad->hh_estimadas;
                    $cntHrsR+=$actividad->hh_reales;


                    $j++;
                }

                if(!isset($data['user'][$bloque->usuario_id]['cntHrsR'])){
                	$data['user'][$bloque->usuario_id]['cntHrsR'] = 0;
                }

                if(!isset($data['user'][$bloque->usuario_id]['cntHrsE'])){
                	$data['user'][$bloque->usuario_id]['cntHrsE'] = 0;
                }

                $data['user'][$bloque->usuario_id]['cntHrsR'] += $cntHrsR;
                $data['user'][$bloque->usuario_id]['cntHrsE'] += $cntHrsE;

                //$this->IntToTime

                $i++;
            }*/

            if(isset($data))
            {
                $i = count( $data['user'] );

            	$data['nbloques'] = $i;
				$data['estado'] = true;
				$data['msg']	= "se cargaron ".$i." usuarios.";
                
            }else{
            	$data['estado'] = false;
            	$data['msg']	= "no se encontraron resultados";
            }

	    	echo json_encode($data, JSON_PRETTY_PRINT);
	    }


	    private function getIntByHrs($horas)
        {
            $arr = explode(':', $horas);
            $h = $arr[0];
            $m = $arr[1];

            if($h>0){
                $hrs = $h*60;
            }else{
                $hrs = 0;
            }

            return $hrs+$m;
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