<?php
namespace Gabs\Controllers; 
use Gabs\Models\Personas;
use Gabs\Models\Proyecto;
use Gabs\Models\PersonaProyecto;
use Gabs\Models\PersonaMes;

class AsignacionController extends ControllerBase
{

    public function indexAction()
    {
    	$this->vistaGeneralProyectos();
    }



    public function vistaGeneralProyectos(){
    	$content = 'asignacion/asignacion_vistaGeneral';
    	$jsScript = '';
    	$menu = 'menu/topMenu';
    	$sideBar = '';

    	$modelPP = new PersonaProyecto();
    	$pcData['data'] = $modelPP->getData();
    	$pcData['weeks'] = $this->getWeeks(5);
    	$pcData['personas'] = Personas::find()->toArray();

        echo $this->view->render('theme',array('topMenu'=>$menu,'menuSel'=>'', 'sideBar'=>$sideBar, 'sideBarSel'=>'gestion', 'pcView'=>$content,'pcData'=>$pcData, 'jsScript' => $jsScript));  	    	
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

    public function editarAsignacionAction(){
    	$pcData['proyectos'] = Proyecto::find('proy_activo = 1')->toArray();
    	$pcData['persona'] = Personas::findFirst('rut = '.$_POST['rut'])->toArray();
    	$pcData['fechaInicio'] = $_POST['fecha'];
    	$pcData['fechaFin'] = date('d-m-Y',strtotime($_POST['fecha']." + 4 days"));

		$month = date('m',strtotime($_POST['fecha']));
		$year = date('Y',strtotime($_POST['fecha']));    	
		$rut = $_POST['rut'];

		$data = array('month'=>$month,'year'=>$year,'rut'=>$rut);
		$modelPM = new PersonaMes();
		$pm = $modelPM->getPersonaMes($data);


    	if(isset($_POST['pp_id'])){
	    	$pcData['proyectoSelected'] = $_POST['proy_id'];
	    	$pcData['bloquePP'] = PersonaProyecto::findFirst('prsn_proy_id = '.$_POST['pp_id'])->toArray();

    	} 

    	if($pm)
			$pcData['bloquePM'] = $pm;

    	$toRend = $this->view->render('asignacion/asignacion_modal_editarAsignacion',array('pcData'=>$pcData, 'jsScript' => ''));  

    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('modal-asignar',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal();');
    	$this->mifaces->addPosRendEval('$(".select-chosen").chosen({width:"100%"});');
    	$this->mifaces->run();
    }

    public function editarBloqueAction(){
		$this->mifaces->newFaces();

    	//Carga objetos
    	$pp = PersonaProyecto::findFirst('prsn_proy_id = '.$_POST['prsn_proy_id']);
    	$p = Personas::findFirst('rut = '.$pp->rut);

    	// Update PM
		$pm = PersonaMes::findFirst("prsn_mes_id = ".$_POST['pm_id']);
		$pm->hh_porcentaje_disponibles = $pm->hh_porcentaje_disponibles+$pp->hh_porcentaje_asignadas-$_POST['porcentaje_hh'];    	
		$hh_asignadas = $_POST['porcentaje_hh']*$p->hh_mensuales/100; 	
		$pm->hh_disponibles = $pm->hh_disponibles+$pp->hh_asignadas - $hh_asignadas;

    	//Validación
    	if(($pm->hh_porcentaje_disponibles+$pp->hh_porcentaje_asignadas)<$_POST['porcentaje_hh'] OR $_POST['porcentaje_hh']<0){
    		$this->mifaces->addPreRendEval("$.bootstrapGrowl('Error en datos ingresados',{type:'danger',align:'center'})");
    		$this->mifaces->run();
    		return;
    	} 

    	//Update P
    	/*
    	$p->hh_porcentaje_disponibles = $p->hh_porcentaje_disponibles+$pp->hh_porcentaje_asignadas-$_POST['porcentaje_hh'];
		$hh_asignadas = $_POST['porcentaje_hh']*$p->hh_mensuales/100;    	
    	$p->hh_disponibles = $p->hh_disponibles+$pp->hh_asignadas-$hh_asignadas;
    	*/

    	// Update PP
    	$pp->proy_id = $_POST['proyecto'];
    	$pp->hh_porcentaje_asignadas = $_POST['porcentaje_hh'];
    	$pp->hh_asignadas = $hh_asignadas;    	

    	//Persistencia
    	//$p->update();
    	$pp->update();
    	$pm->update();

    	//Renderizado
    	$modelPP = new PersonaProyecto();
    	$pcData['data'] = $modelPP->getData();
    	$pcData['weeks'] = $this->getWeeks(5);
    	$pcData['personas'] = Personas::find()->toArray();    	
    	$toRend = $this->view->render('asignacion/asignacion_tablaAsignacion',array('pcData'=>$pcData));
    	$this->mifaces->addToRend('tablaData',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal("hide");');
    	$this->mifaces->run();
    }

    public function guardarBloqueAction(){
		$this->mifaces->newFaces();
    	//Carga objetos
    	$p = Personas::findFirst('rut = '.$_POST['rut']);
    	$pp = new PersonaProyecto();

    	// Update PM
    	if(isset($_POST['pm_id'])){
    		$pm = PersonaMes::findFirst("prsn_mes_id = ".$_POST['pm_id']);
    		$pm->hh_porcentaje_disponibles = $pm->hh_porcentaje_disponibles-$_POST['porcentaje_hh'];    	
    		$hh_asignadas = $_POST['porcentaje_hh']*$p->hh_mensuales/100; 	
    		$pm->hh_disponibles = $pm->hh_disponibles - $hh_asignadas;

    	}
    	else{
    		$pm = new PersonaMes();
    		$pm->rut = $_POST['rut'];
    		$pm->month = date('m',strtotime($_POST['fecha']));
    		$pm->year = date('Y',strtotime($_POST['fecha']));
    		$pm->hh_porcentaje_disponibles = 100 - $_POST['porcentaje_hh'];
    		$hh_asignadas = $_POST['porcentaje_hh']*$p->hh_mensuales/100; 	
    		$pm->hh_disponibles = $p->hh_mensuales - $hh_asignadas;

    	}

    	// Update PP
    	$pp->proy_id = $_POST['proyecto'];
    	$pp->hh_porcentaje_asignadas = $_POST['porcentaje_hh'];
    	$pp->hh_asignadas = $hh_asignadas; 
    	$pp->rut = $_POST['rut'];
    	$pp->fecha_inicio_semana = date('Y-m-d',strtotime($_POST['fecha']));
    	$pp->area_id = 0;


    	//Validación
    	if(($pm->hh_porcentaje_disponibles+$pp->hh_porcentaje_asignadas)<$_POST['porcentaje_hh'] OR $_POST['porcentaje_hh']<0){
    		$this->mifaces->addPreRendEval("$.bootstrapGrowl('Error en datos ingresados',{type:'danger',align:'center'})");
    		$this->mifaces->run();
    		return;
    	} 

    	/*
    	//Update P
    	$p->hh_porcentaje_disponibles = $p->hh_porcentaje_disponibles-$_POST['porcentaje_hh'];
		$hh_asignadas = $_POST['porcentaje_hh']*$p->hh_mensuales/100;    	
    	$p->hh_disponibles = $p->hh_disponibles-$hh_asignadas;
    	*/


    	//Persistencia
    	//$p->update();
    	if(isset($_POST['pm_id']))
    		$pm->update();
    	else
    		$pm->save();
    	$pp->save();
		

    	//Renderizado
    	$modelPP = new PersonaProyecto();
    	$pcData['data'] = $modelPP->getData();
    	$pcData['weeks'] = $this->getWeeks(5);
    	$pcData['personas'] = Personas::find()->toArray();    	
    	$toRend = $this->view->render('asignacion/asignacion_tablaAsignacion',array('pcData'=>$pcData));
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
    	$modelPP = new PersonaProyecto();
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