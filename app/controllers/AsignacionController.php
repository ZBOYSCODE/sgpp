<?php
namespace Gabs\Controllers; 
use Gabs\Models\Personas;
use Gabs\Models\Proyecto;
use Gabs\Models\PersonaSemana;
use Gabs\Models\PersonaMes;
use Gabs\Models\ProyectoPersonaSemana;
use Gabs\Models\PersonaProyecto;

class AsignacionController extends ControllerBase
{

    public function indexAction()
    {
    	$this->generalAction();
    }

    public function generalAction(){
    	$content = 'asignacion/asignacion_vistaGeneral';
    	$jsScript = '';
    	$menu = 'menu/topMenu';
    	$sideBar = 'menu/sideBar';

    	$modelPP = new PersonaSemana();
    	$pcData['data'] = $modelPP->getData();
    	$pcData['weeks'] = $this->getWeeks(5);
    	$pcData['personas'] = Personas::find()->toArray();

        echo $this->view->render('theme',array('topMenu'=>$menu,'menuSel'=>'', 'sideBar'=>$sideBar, 'sideBarSel'=>'gestion', 'pcView'=>$content,'pcData'=>$pcData, 'jsScript' => $jsScript));  	    	
    }

    public function proyectosAction(){
    	$content = 'asignacion/asignacion_vistaProyectos';
    	$jsScript = '';
    	$menu = 'menu/topMenu';
    	$sideBar = 'menu/sideBar';

    	$pcData['proyectos'] = Proyecto::find('proy_activo = 1')->toArray();
    	$pcData['proyectoSelected'] = reset($pcData['proyectos']);

    	$modelPP = new PersonaSemana();
    	$modelP = new Personas();
    	$pcData['personas'] = $modelP->getPersonasByProyecto($pcData['proyectoSelected']);
    	$pcData['data'] = $modelPP->getData($pcData['proyectoSelected']);
    	$pcData['weeks'] = $this->getWeeks(5);
        

        $addJs[] = 'js/asignacionpersonas.js';
        
        echo $this->view->render('theme',array( 'topMenu'=>$menu,
                                                'menuSel'=>'',
                                                'sideBar'=>$sideBar,
                                                'sideBarSel'=>'gestion',
                                                'pcView'=>$content,
                                                'pcData'=>$pcData,
                                                'jsScript' => $jsScript,
                                                'addJs' => $addJs));

    }

    public function changeProyectoAction(){
    	$pcData['proyectoSelected'] = Proyecto::findFirst("proy_id = ".$_POST['proyectoSelected'])->toArray();

    	$modelPP = new PersonaSemana();
    	$modelP = new Personas();
    	$pcData['personas'] = $modelP->getPersonasByProyecto($pcData['proyectoSelected']);

    	$pcData['data'] = $modelPP->getData($pcData['proyectoSelected']);
    	$pcData['weeks'] = $this->getWeeks(5);    	

    	$pcData['pcData'] = $pcData;

    	$toRend = $this->view->render('asignacion/asignacion_tablaAsignacionProyectos',$pcData);
    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('tablaData',$toRend);
		$this->mifaces->run();
    }

    private function getWeeks($numberWeeks){
    	$lunesActual = date('d-m-Y',strtotime('monday this week'));
    	$lunesInicial = date('d-m-Y',strtotime($lunesActual."- ".$numberWeeks." weeks"));
    	$lunesFinal = date('d-m-Y',strtotime($lunesActual."+ ".$numberWeeks." weeks"));
    	$arr = array();
    	$lunesActual = $lunesInicial;

    	
		$diff = date_diff(date_create($lunesActual),date_create($lunesFinal));
    	while($diff->format("%R%a")>=0){
    		array_push($arr, $lunesActual);
			$lunesActual = date('d-m-Y',strtotime($lunesActual."+ 1 weeks"));
			$diff = date_diff(date_create($lunesActual),date_create($lunesFinal));
    	}
    	return $arr;
    }

    public function modalAsignacionProyectoAction(){



    	$pcData['persona'] = Personas::findFirst('rut = '.$_POST['rut'])->toArray();
    	$pcData['fechaInicio'] = $_POST['fecha'];
    	$pcData['fechaFin'] = date('d-m-Y',strtotime($_POST['fecha']." + 4 days"));

    	$pcData['proyectoSelected'] = $_POST['proy_id'];

    	if(isset($_POST['pps_id']))
        {
    		$pcData['bloquePPS'] = ProyectoPersonaSemana::findFirst('proy_ps_id = '.$_POST['pps_id'])->toArray();
    		$pcData['bloquePS'] = PersonaSemana::findFirst('prsn_smna_id = "'.$_POST['ps_id'].'"')->toArray();
    	} else{
    		$data = array('rut' =>$_POST['rut'], 'fecha'=>date('Y-m-d',strtotime($_POST['fecha'])));
    		$modelPS = new PersonaSemana();
    		$pcData['bloquePS'] = $modelPS->getPersonaSemanaByFechaRut($data);
    	}


        $pcData['proySemana'] = $this->getProyectosSemana($_POST['fecha'], $_POST['rut']);

    	$toRend = $this->view->render('asignacion/asignacion_modal_editarAsignacionProyectos',array('pcData'=>$pcData, 'jsScript' => ''));  

    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('modal-asignar',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal();');
    	$this->mifaces->addPosRendEval('$(".select-chosen").chosen({width:"100%"});');
    	$this->mifaces->run();
    } 


    public function getProyectosSemana($fecha, $rut)
    {

        $fecha = date('Y-m-d', strtotime($fecha));

        // lista de proyectos asignados
        $conditions = "fecha_inicio_semana = :fecha: AND rut = :rut:";

        $params = array(
                    "fecha"     => $fecha,
                    "rut"       => $rut
                );

        $pcData['proySemana'] = PersonaSemana::find(array(
                $conditions,
                "bind" => $params
        ));

        $arr = array();
        foreach ($pcData['proySemana'] as $personasemana)
        {
            foreach ($personasemana->proyectos as $proyectos) {

                $arr[$proyectos->proy_id]['nombre'] = $proyectos->proy_nombre;
                $arr[$proyectos->proy_id]['hh'] = $this->getHHAsignadas($proyectos->proy_id, $personasemana->prsn_smna_id);
            }
            
        }

        return $arr;
    }

    private function getHHAsignadas($proy_id, $semana_id)
    {
        // lista de proyectos asignados
        $conditions = "prsn_smna_id = :semana_id: AND proy_id = :proy_id:";

        $params = array(
                    "semana_id"     => $semana_id,
                    "proy_id"       => $proy_id
                );

        $result = ProyectoPersonaSemana::findFirst(array(
                $conditions,
                "bind" => $params
        ));

        return $result->hh_porcentaje_asignadas;
    }




    public function editarBloqueProyectoAction(){
		$this->mifaces->newFaces();

    	//Carga objetos/arrays
    	$pps = ProyectoPersonaSemana::findFirst('proy_ps_id = '.$_POST['pps_id']);
    	$ps = PersonaSemana::findFirst('prsn_smna_id = '.$pps->prsn_smna_id);
    	$p = Personas::findFirst('rut = '.$ps->rut);
    	

		$ps->hh_total_porcentaje_asignadas = $ps->hh_total_porcentaje_asignadas - $pps->hh_porcentaje_asignadas + $_POST['porcentaje_hh'];
    	$pps->hh_porcentaje_asignadas = $_POST['porcentaje_hh'];

    	//Validación
    	if($ps->hh_total_porcentaje_asignadas<0 OR $pps->hh_porcentaje_asignadas<0){
    		$this->mifaces->addPreRendEval("$.bootstrapGrowl('Error en datos ingresados',{type:'danger',align:'center'})");
    		$this->mifaces->run();
    		return;
    	} 

    	//Persistencia
    	$ps->update();
    	$pps->update();

    	//Renderizado
    	$pcData['proyectoSelected'] = Proyecto::findFirst("proy_id = ".$pps->proy_id)->toArray();

    	$modelPP = new PersonaSemana();
    	$modelP = new Personas();
    	$pcData['personas'] = $modelP->getPersonasByProyecto($pcData['proyectoSelected']);
    	$pcData['data'] = $modelPP->getData($pcData['proyectoSelected']);
    	$pcData['weeks'] = $this->getWeeks(5);    	

    	$pcData['pcData'] = $pcData;

    	$toRend = $this->view->render('asignacion/asignacion_tablaAsignacionProyectos',$pcData);
    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('tablaData',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal("hide");');
		$this->mifaces->run();
    }

    public function guardarBloqueProyectoAction(){
		$this->mifaces->newFaces();

    	//Carga objetos/arrays
    	$pps = new ProyectoPersonaSemana();
    	if(isset($_POST['ps_id'])){
    		$ps = PersonaSemana::findFirst("prsn_smna_id = ".$_POST['ps_id']);
    		$ps->hh_total_porcentaje_asignadas = $ps->hh_total_porcentaje_asignadas + $_POST['porcentaje_hh'];
    	}
    	else{
	    	$ps = new PersonaSemana();
			$ps->rut = $_POST['rut'];
			$ps->area_id = 0;
			$ps->fecha_inicio_semana = date('Y-m-d',strtotime($_POST['fecha']));
			$ps->hh_total_asignadas = 0;	    	
			$ps->hh_total_porcentaje_asignadas = $_POST['porcentaje_hh'];
    	}

    	$p = Personas::findFirst('rut = '.$_POST['rut']);

    	$pps->hh_porcentaje_asignadas = $_POST['porcentaje_hh'];
    	$pps->proy_id = $_POST['proy_id'];

    	//Validación
    	if($ps->hh_total_porcentaje_asignadas<0 OR $pps->hh_porcentaje_asignadas<0){
    		$this->mifaces->addPreRendEval("$.bootstrapGrowl('Error en datos ingresados',{type:'danger',align:'center'})");
    		$this->mifaces->run();
    		return;
    	} 


		//Persistencia
		$ps->save();
		$pps->prsn_smna_id = $ps->prsn_smna_id;
		$pps->save();

    	//Renderizado
    	$pcData['proyectoSelected'] = Proyecto::findFirst("proy_id = ".$pps->proy_id)->toArray();

    	$modelPP = new PersonaSemana();
    	$modelP = new Personas();
    	$pcData['personas'] = $modelP->getPersonasByProyecto($pcData['proyectoSelected']);
    	$pcData['data'] = $modelPP->getData($pcData['proyectoSelected']);
    	$pcData['weeks'] = $this->getWeeks(5);    	

    	$pcData['pcData'] = $pcData;

    	$toRend = $this->view->render('asignacion/asignacion_tablaAsignacionProyectos',$pcData);
    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('tablaData',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal("hide");');
		$this->mifaces->run();
    }   

    public function modalAgregarPersonaAction(){
    	$modelP = new Personas();
    	$pcData['personas'] = $modelP->getPersonasSinProyecto($_POST);
    	$pcData['proyectoSelected'] = Proyecto::findFirst('proy_id = '.$_POST['proy_id'])->toArray();

    	$pcData['pcData'] = $pcData;

    	$toRend = $this->view->render('asignacion/asignacion_modal_agregarPersonaProyecto',$pcData);

    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('modal-asignar',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal();');
    	$this->mifaces->addPosRendEval('$(".select-chosen").chosen({width:"100%", max_selected_options: 2});');
    	$this->mifaces->run();
    } 

    public function agregarPersonaAction(){

        foreach ($_POST['users'] as $usuario)
        {
            $pp = new PersonaProyecto();
            $pp->rut = $usuario;
            $pp->proy_id = $_POST['proy_id'];
            $pp->activo = 1;
            $pp->save();
        }

        /*
		$pp = new PersonaProyecto();
		$pp->rut = $_POST['personaSelected'];
		$pp->proy_id = $_POST['proy_id'];
		$pp->activo = 1;
		$pp->save();
        */

    	//Renderizado
    	$pcData['proyectoSelected'] = Proyecto::findFirst("proy_id = ".$pp->proy_id)->toArray();

    	$modelPP = new PersonaSemana();
    	$modelP = new Personas();
    	$pcData['personas'] = $modelP->getPersonasByProyecto($pcData['proyectoSelected']);
    	$pcData['data'] = $modelPP->getData($pcData['proyectoSelected']);
    	$pcData['weeks'] = $this->getWeeks(5);    	

    	$pcData['pcData'] = $pcData;

    	$toRend = $this->view->render('asignacion/asignacion_tablaAsignacionProyectos',$pcData);
    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('tablaData',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal("hide");');
		$this->mifaces->run();

    }


/*
    public function asignacionPersonas()
    {
    	$content = 'asignacion/asignacion_personas';
    	$jsScript = $this->jsGeneral();
    	$menu = 'menu/topMenu';
    	$sideBar = '';

    	$modelP = new Proyecto();
    	$modelPP = new PersonaSemana();
    	$pcData['default'] = $modelP::findFirst('proy_id = 0')->toArray();
    	$pcData['proyectos'] = $modelP::find('proy_activo = 1')->toArray();
    	$pcData['horasDefault'] = $modelPP->getHorasDefault()->toArray();
    	$pcData['horasAsignadas'] = $modelPP::find('proy_id != 0')->toArray();

        echo $this->view->render('theme',array('topMenu'=>$menu,'menuSel'=>'', 'sideBar'=>$sideBar, 'sideBarSel'=>'gestion', 'pcView'=>$content,'pcData'=>$pcData, 'jsScript' => $jsScript));  	
    }    

    private function jsGeneral(){
    	return "
    					var sortList = [];
				var objs = $('.fwBody .connectedSortable .block');
				
				function sorTear(){
					sortList = [];
					objs.each(
						function(){
							sortList.push({idOb:$(this).data('idob'), col:$(this).parent().data('col'), row:$(this).index()});
						}
					);				
				}
				
				sorTear();
				$('.connectedSortable').sortable({
                connectWith: '.connectedSortable',
                items: '.block',
				forcePlaceholderSize: true,
                opacity: 0.75,
                handle: '.block-title',
                placeholder: 'draggable-placeholder',
                tolerance: 'pointer',
                start: function(e, ui){
					ui.item.startPos = ui.item.parent().data('col') +' - '+ui.item.index();
                    ui.placeholder.css('height', ui.item.outerHeight());
					console.log('Start Div position: ' + ui.item.data('col') +' - '+ui.item.data('row'));
					var col = ui.item.data('col');
					var horasActuales = $(\"div[data-proy=\"+col+\"]\").data('actuales');
					var horasRestar = ui.item.data('horas');
					

					console.log(col);
					console.log(horasActuales);
					console.log(horasRestar);
					console.log(horasTotal);
						
					if(col == 0)
						var horasTotal = horasActuales - horasRestar;	
					else
						var horasTotal = horasActuales + horasRestar;	
					
					$(\"div[data-proy=\"+col+\"]\").find('.hh-actuales').html(horasTotal);
					$(\"div[data-proy=\"+col+\"]\").data('actuales',horasTotal);

                },
				stop: function(event, ui) {
					ui.item.data('col',ui.item.parent().data('col'));
					ui.item.data('row',ui.item.index());
					console.log('horas:'+ui.item.data('horas'));
					console.log('Start position: ' + ui.item.startPos);
					console.log('New position: ' + ui.item.parent().data('col') +' - '+ui.item.index());
					console.log('Div position: ' + ui.item.data('col') +' - '+ui.item.data('row'));

					
					var oldCol = $.trim(ui.item.startPos.split(\"-\"))[0];
					var newCol = ui.item.parent().data('col')
					var horas = ui.item.data('horas');
					var horasActuales = $(\"div[data-proy=\"+newCol+\"]\").data('actuales');
					if(newCol == 0){
						var horasTotal = horasActuales + horas;	
					} else{
						var horasTotal = horasActuales - horas;
					}
					
					$(\"div[data-proy=\"+newCol+\"]\").find('.hh-actuales').html(horasTotal);
					$(\"div[data-proy=\"+newCol+\"]\").data('actuales',horasTotal);
					

					sorTear();
				}
            }).disableSelection();

			$('#horasBloque').change(function(event) {
				var hh_disp = $('#hhDispTotales').val();
				var hh_disp_actual = $('#hhDispActuales').html();
				var hh_req = $(this).val();
				var hh_result = hh_disp-hh_req;
				if((hh_result)>=0)
					$('#hhDispActuales').html(hh_result);
				else{
					$('#hhDispActuales').html(hh_disp);
					$('#horasBloque').val(0);
					$.bootstrapGrowl('No hay suficientes horas para asignar',{type:'warning',align:'center'});
				}
			});    
    	";
    }
  */
}