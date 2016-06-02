<?php
namespace Gabs\Controllers; 
use Gabs\Models\Personas;
use Gabs\Models\Proyecto;
use Gabs\Models\PersonaSemana;
use Gabs\Models\PersonaMes;
use Gabs\Models\ProyectoPersonaSemana;

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

        echo $this->view->render('theme',array('topMenu'=>$menu,'menuSel'=>'', 'sideBar'=>$sideBar, 'sideBarSel'=>'gestion', 'pcView'=>$content,'pcData'=>$pcData, 'jsScript' => $jsScript));  	    	    	

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
	    	$pcData['bloquePP'] = PersonaSemana::findFirst('prsn_proy_id = '.$_POST['pp_id'])->toArray();
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

    public function modalAsignacionProyectoAction(){

    	$pcData['persona'] = Personas::findFirst('rut = '.$_POST['rut'])->toArray();
    	$pcData['fechaInicio'] = $_POST['fecha'];
    	$pcData['fechaFin'] = date('d-m-Y',strtotime($_POST['fecha']." + 4 days"));

    	$pcData['proyectoSelected'] = $_POST['proy_id'];

    	if(isset($_POST['pps_id'])){
    		$pcData['bloquePPS'] = ProyectoPersonaSemana::findFirst('proy_ps_id = '.$_POST['pps_id'])->toArray();
    		$pcData['bloquePS'] = PersonaSemana::findFirst('prsn_smna_id = '.$_POST['ps_id'])->toArray();
    	} else{
    		$pcData['bloquePS'] = PersonaSemana::findFirst('fecha_inicio_semana = '.$_POST['fecha'].' AND rut = '.$pcData['persona']['rut']);
    		if($pcData['bloquePS'])
    			$pcData['bloquePS'];
    		else
    			$pcData['bloquePS'] = null;
    	}

    	$toRend = $this->view->render('asignacion/asignacion_modal_editarAsignacionProyectos',array('pcData'=>$pcData, 'jsScript' => ''));  

    	$this->mifaces->newFaces();
    	$this->mifaces->addToRend('modal-asignar',$toRend);
    	$this->mifaces->addPreRendEval('$("#modal-asignar").modal();');
    	$this->mifaces->addPosRendEval('$(".select-chosen").chosen({width:"100%"});');
    	$this->mifaces->run();
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
    	if($ps->hh_total_porcentaje_asignadas<=0 OR $pps->hh_porcentaje_asignadas<0){
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

		print_r($_POST);

    	//Carga objetos/arrays
    	$pps = new ProyectoPersonaSemana();
    	$ps = new PersonaSemana();
    	$p = Personas::findFirst('rut = '.$ps->rut);
    	

		$ps->hh_total_porcentaje_asignadas = $ps->hh_total_porcentaje_asignadas - $pps->hh_porcentaje_asignadas + $_POST['porcentaje_hh'];
    	$pps->hh_porcentaje_asignadas = $_POST['porcentaje_hh'];

    	//Validación
    	if($ps->hh_total_porcentaje_asignadas<=0 OR $pps->hh_porcentaje_asignadas<0){
    		$this->mifaces->addPreRendEval("$.bootstrapGrowl('Error en datos ingresados',{type:'danger',align:'center'})");
    		$this->mifaces->run();
    		return;
    	} 

    	//Persistencia
    	//$ps->update();
    	//$pps->update();

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