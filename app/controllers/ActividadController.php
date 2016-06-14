<?php
    namespace Gabs\Controllers;

    use Gabs\Models\Users;
	use Gabs\Models\Proyecto;
	use Gabs\Models\Bloque;
	use Gabs\Models\Actividad;

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
	    


	    public function cargarRegistrosAction()
	    {

	    	$fecha 		=	$this->request->getPost("fecha");

	    	// Query robots binding parameters with both string and integer placeholders
			$conditions = "fecha = :fecha:";

			// Parameters whose keys are the same as placeholders
	    	$params = array(
		    			"fecha" 	=> $fecha
		    		);

	    	$bloques = Bloque::find(array(
	    			$conditions,
	    			"bind" => $params
	    	));


	    	$i =0 ;

	    	foreach ($bloques as $bloque)
	    	{

                $cntHrsR = 0;
                $cntHrsE = 0;

                $j = 0;

                $data['user'][$bloque->usuario_id]['nombre'] = $bloque->usuario->name;


                foreach ($bloque->actividad as $actividad) {

                	$arr['id'] 			= $actividad->id;
                	$arr['proyecto'] 		= $actividad->proyecto->proy_nombre;
                	$arr['hh_estimadas'] 	= $this->IntToTime($actividad->hh_estimadas);
                	$arr['hh_reales'] 	= $this->IntToTime($actividad->hh_reales);
                	$arr['descripcion'] 	= $actividad->descripcion;

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
            }

            if($i>0){
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