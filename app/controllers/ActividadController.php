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

                	$data['user'][$bloque->usuario_id]['actividades'][$j]['proyecto'] 		= $actividad->proyecto->proy_nombre;

                	$data['user'][$bloque->usuario_id]['actividades'][$j]['id'] 			= $actividad->id;
                    $data['user'][$bloque->usuario_id]['actividades'][$j]['hh_estimadas'] 	= $this->IntToTime($actividad->hh_estimadas);
                    $data['user'][$bloque->usuario_id]['actividades'][$j]['hh_reales'] 		= $this->IntToTime($actividad->hh_reales);
                    $data['user'][$bloque->usuario_id]['actividades'][$j]['descripcion'] 	= $actividad->descripcion;

                    $cntHrsE+=$actividad->hh_estimadas;
                    $cntHrsR+=$actividad->hh_reales;

                    $j++;
                }

                $data['user'][$bloque->usuario_id]['cntHrsR'] = $this->IntToTime($cntHrsR);
                $data['user'][$bloque->usuario_id]['cntHrsE'] = $this->IntToTime($cntHrsE);

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