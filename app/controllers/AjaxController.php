<?php

namespace Gabs\Controllers;
use Gabs\Models\Personas;
use Gabs\Models\Users;
use Gabs\Models\TipoPregunta;
use Gabs\Models\Preguntas;
use Gabs\Models\Evaluacion;
use Gabs\Models\ResumenEvaluacionTipo;
use Gabs\PHPExcel\IOFactory;
 
class AjaxController extends ControllerBase
{
    /**
     * Default action. Set the public layout (layouts/public.volt)
     */
    public function indexAction()
    {

    }	

    public function loadEvaluacionPersonaAction()
    {

        $rutEvaluado = $_POST['rut'];

		if(isset($_POST['evaluador']))
			$rutEvaluador = $_POST['evaluador'];
		else
			$rutEvaluador = $this->auth->getIdentity()['rut'];

        //traemos personas involucradas
        $personaEvaluar = Personas::findFirst("rut='".$rutEvaluado."'")->toArray();
        $personaEvaluador = Personas::findFirst("rut='".$rutEvaluador."'")->toArray();

        // traemos la encuesta completa
        $evaluacionList = array();
        $categorias = TipoPregunta::find();

        foreach ($categorias as $iter) {
            $cat = $iter->toArray();
            $preguntas = Preguntas::find("id_tipo ='".$iter->getIdTipo()."'")->toArray();
            $cat['preguntas'] = $preguntas;
            array_push($evaluacionList, $cat);
        }

        //values del formulario para editar si es vacio sirve igual (caso de guardar nuevo)
        $dataForm = $this->__dbToFormEvaluacion($evaluacionList,$rutEvaluador, $rutEvaluado);


        $dataView['evaluacion'] = $evaluacionList;
        $dataView['personaEvaluar'] = $personaEvaluar;
        $dataView['personaEvaluador'] = $personaEvaluador;

        //con esto deberiamos cargar los datos al formulario
        $dataView['dataForm'] = $dataForm;

    	$this->mifaces->newFaces();
		$toRend=$this->view->render('evaluacion/evaluacionPersona',$dataView);
		$this->mifaces->addToRend('page-content', $toRend);
		$this->mifaces->run();
    }   

    private function __dbToFormEvaluacion($data,$rutEvaluador,$rutEvaluado) {

        $inputs = Array();


        //inputs del tipo preg-1-1 ... preg-"id_tipo"-"id_pregunta"
        foreach ($data as $cat) {
            foreach ($cat['preguntas'] as $pregunta) {
                
                $evaluacion = Evaluacion::find("id_pregunta = ".$pregunta['id_pregunta']." AND rut_evaluador = '".$rutEvaluador."' AND rut_evaluado = '".$rutEvaluado."'")->toArray();
                if(!empty($evaluacion)){
                    $keyTmp = "preg-".$cat['id_tipo']."-".$pregunta['id_pregunta'];
                    $valueTmp =$evaluacion[0]['puntaje'];

                    $inputs[$keyTmp] = $valueTmp;
                }
            }
        }

        //inputs del tipo pond-1 ... preg-"id_tipo"
        foreach ($data as $cat) {
             $resumen = ResumenEvaluacionTipo::find("id_tipo = ".$cat['id_tipo']." AND rut_evaluador = '".$rutEvaluador."' AND rut_evaluado = '".$rutEvaluado."'")->toArray();

                if(!empty($resumen)){
                    $keyTmp = "pond-".$cat['id_tipo'];
                    $valueTmp =$resumen[0]['puntaje'];
                    $inputs[$keyTmp] = $valueTmp;
                    $keyTmp = "obs-".$cat['id_tipo'];
                    $valueTmp =$resumen[0]['observacion'];
                    $inputs[$keyTmp] = $valueTmp;
                }
        }

       // print_r($inputs);exit;
        return $inputs;
    }

    public function updateLiderPersonaAction()
    {
        $modelPersona = new Personas();
        $persona = $modelPersona::findFirstByRut($_POST['rut']);
        $persona->lider_id = $this->auth->getIdentity()['id'];
        $persona->update();
                    

        $dataView['pcData']['personas']  = $modelPersona->getAll();

        $dataView['jsScript'] = "   $('#tablaPersonas').filterTable({
                                        inputSelector: '#searchRow',
                                        minRows: 1          
                                    });";  


        $this->mifaces->newFaces();
        $toRend=$this->view->render('evaluacion/tablaPersonas',$dataView);
        $this->mifaces->addToRend('bodyEvaluacion', $toRend);
        $this->mifaces->addPosRendEval('$.bootstrapGrowl("Persona seleccionada correctamente!",{type:"success",align:"center"})');
        $this->mifaces->run();
    }

    public function deleteLiderPersonaAction()
    {
        $modelPersona = new Personas();
        $persona = $modelPersona::findFirstByRut($_POST['rut']);
        $persona->lider_id = null;
        $persona->update();

        $dataView['pcData']['personas']  = $modelPersona->getByUser($this->auth->getIdentity()['id']);

        $dataView['pcData']['vistaPropias']  = 1;        

        $this->mifaces->newFaces();
        $toRend=$this->view->render('evaluacion/tablaPersonas',$dataView);
        $this->mifaces->addToRend('bodyEvaluacion', $toRend);
        $this->mifaces->addPosRendEval('$.bootstrapGrowl("Persona liberada correctamente!",{type:"success",align:"center"})');
        $this->mifaces->run();
    }


    public function createEvaluacionPersonaAction()
    {


        $errordb = false;

        $modelEvaluacion = new Evaluacion();
        $rutEvaluado = $_POST['rutEvaluado'];
        $rutEvaluador = $_POST['rutEvaluador'];
        //$idEvaluador = $this->auth->getIdentity()['id'];
        foreach ($_POST as $name => $value) {
            $arrExplode = explode('-', $name);
            if($arrExplode[0] == 'preg' && trim($value)!=''){
                $modelPregunta = new Evaluacion();
                $modelPregunta->setRutEvaluador($rutEvaluador);
                $modelPregunta->setRutEvaluado($rutEvaluado);
                $modelPregunta->setPuntaje($value);
                $modelPregunta->setIdPregunta($arrExplode[2]);
                $modelPregunta->save();
                if ($modelPregunta->save() == false) {
                    $errordb = true;
                }
            }
            if($arrExplode[0] == 'pond'){
                $modelResumenEvaluacionTipo = new ResumenEvaluacionTipo();
                $modelResumenEvaluacionTipo->setRutEvaluador($rutEvaluador);
                $modelResumenEvaluacionTipo->setRutEvaluado($rutEvaluado);
                $modelResumenEvaluacionTipo->setPuntaje($value);
				$modelResumenEvaluacionTipo->setObservacion($_POST['obs-'.$arrExplode[1]]);
                $modelResumenEvaluacionTipo->setFecha(date("Y-m-d"));
                $modelResumenEvaluacionTipo->setIdTipo($arrExplode[1]);
                
                if ($modelResumenEvaluacionTipo->save() == false) {
                   /* foreach ($modelResumenEvaluacionTipo->getMessages() as $message) {
                        echo $message, "\n";
                    }
                    */
                    $errordb = true;
                }
            }
        }  

        $this->mifaces->newFaces();
        if($errordb){

            $this->mifaces->addPosRendEval('$.bootstrapGrowl("Error inesperado en la persistencia de datos",{type:"danger",align:"center"})');
        }
        else {

            $this->mifaces->addPosRendEval('$.bootstrapGrowl("Evaluación ingresada correctamente!",{type:"success",align:"center"})');
            $modelPersona = new Personas();
            $dataView['pcData']['personas']  = $modelPersona->getByUser($this->auth->getIdentity()['id']);
            $dataView['pcData']['vistaPropias'] = 1;        
            $toRend=$this->view->render('evaluacion/tablaPersonas',$dataView);
            $this->mifaces->addToRend('bodyEvaluacion',$toRend);


        }

        $this->mifaces->run();
    }
    
    public function changePassAction(){
        $this->mifaces->newFaces();
        if($_POST['userPassword'] == '' OR $_POST['userConfirmPassword'] == ''){
            $this->mifaces->addPosRendEval("$.bootstrapGrowl('Ingrese ambas contraseñas',{type:'warning',align:'center'});");
        } elseif($_POST['userPassword'] !== $_POST['userConfirmPassword']){
            $this->mifaces->addPosRendEval("$.bootstrapGrowl('Las contraseñas no coinciden, vuelva a ingresar.',{type:'warning',align:'center'});");
        }  else{
            $passwordHashed = $this->getDI()
                ->getSecurity()
                ->hash($_POST['userPassword']);              

            $modelUser = new Users();
            $user = $modelUser::findFirstById($this->auth->getIdentity()['id']);
            $user->password = $passwordHashed;
            $user->mustChangePassword = 'N';
            $user->update();

            $this->mifaces->addPosRendEval("
                  $.bootstrapGrowl('La contraseña ha sido cambiada correctamente, se reiniciará la sesión.',{type:'success',align:'center'});
                setTimeout(function(){                    
                    window.location.href ='logout';
                }, 3000);");
        }
        $this->mifaces->run();
    }
	
    public function listadoComiteAction(){

	    $modelPersona = new Personas();
		
        $list = $modelPersona->getListadoConsejoPorEvaluador($_POST['evaluador']);
		$lista = array();
		foreach ($list as $val){
			$lista[$val->evaluado][$val->id_tipo] = $val;		
		}
		
        $pc = Array('lista'=>$lista);
        $toRend=$this->view->render('evaluacion/mesaTabla',array('pcData'=>$pc));
        $this->mifaces->addToRend('mesatabla', $toRend);
		$this->mifaces->run();
    }	

    public function excelEvaluacionAction()
    {
        $rutEvaluado = $_POST['rut'];
        $personaEvaluar = Personas::findFirst("rut='".$rutEvaluado."'")->toArray();

        if(isset($personaEvaluar['lider_id']))
            $idEvaluador = $personaEvaluar['lider_id'];
        else
            $idEvaluador = $this->auth->getIdentity()['id'];        

        $userEvaluador = Users::findFirst("id=".$idEvaluador."")->toArray();
        $personaEvaluador = Personas::findFirst("rut='".$userEvaluador['rut']."'")->toArray();

        $modelRET = new ResumenEvaluacionTipo();
        $evaluacion = $modelRET->getEvaluacionToExcel(array('rut_evaluador'=>$personaEvaluador['rut'],'rut_evaluado'=>$personaEvaluar['rut']))->toArray();
        
        // Manejo de Excel

        $xx = $this->iof;
        $objTpl = $xx->load($this->config->application->libraryDir."Evaluacionentregable.xlsx");
        $objTpl->setActiveSheetIndex(0);  //set first sheet as active

        $objTpl->getActiveSheet()->setCellValue('B8', "{$personaEvaluar['nombres']} {$personaEvaluar['apellido_paterno']} {$personaEvaluar['apellido_materno']}");  
        $objTpl->getActiveSheet()->setCellValue('B10', "{$personaEvaluar['area']}");
        $objTpl->getActiveSheet()->setCellValue('B12', "{$personaEvaluador['nombres']} {$personaEvaluador['apellido_paterno']} {$personaEvaluador['apellido_materno']}");

        $total = 0;
        foreach ($evaluacion as $key => $val) {
            $key = $key + 17;
            $objTpl->getActiveSheet()->setCellValue('B'.$key, $val['puntaje']."%");
            $ponderacion = $objTpl->getActiveSheet()->getCell('C'.$key)->getValue();
            $total = $val['puntaje']*$ponderacion + $total;
            $objTpl->getActiveSheet()->setCellValue('D'.$key, $val['puntaje']*$ponderacion."%");
        }                  

        $objTpl->getActiveSheet()->setCellValue('D26', $total."%");

        //prepare download
        $filename=mt_rand(1,100000).'.xlsx'; //just some random filename
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $objWriter = $xx->createWriter($objTpl, 'Excel2007');
        $objWriter->save('php://output');  //send it to user, of course you can save it to disk also!
        die();
    }	
	
    public function excelMasivoAction()
    {
        $modelRET = new ResumenEvaluacionTipo();
		$resumen = $modelRET->getEvaluacionMasivo()->toArray();

		foreach ($resumen as $rutEvaluado){
			print($rutEvaluado['rut_evaluado']."<br>");
			
			$rutEvaluado = $rutEvaluado['rut_evaluado'];
			$personaEvaluar = Personas::findFirst("rut='".$rutEvaluado."'")->toArray();

			if(isset($personaEvaluar['lider_id']))
				$idEvaluador = $personaEvaluar['lider_id'];
			else
				$idEvaluador = $this->auth->getIdentity()['id'];        

			$userEvaluador = Users::findFirst("id=".$idEvaluador."")->toArray();
			$personaEvaluador = Personas::findFirst("rut='".$userEvaluador['rut']."'")->toArray();


			$evaluacion = $modelRET->getEvaluacionToExcel(array('rut_evaluador'=>$personaEvaluador['rut'],'rut_evaluado'=>$personaEvaluar['rut']))->toArray();
			
			// Manejo de Excel

			$xx = $this->iof;
			$objTpl = $xx->load($this->config->application->libraryDir."Evaluacionentregable.xlsx");
			$objTpl->setActiveSheetIndex(0);  //set first sheet as active

			$objTpl->getActiveSheet()->setCellValue('B8', "{$personaEvaluar['nombres']} {$personaEvaluar['apellido_paterno']} {$personaEvaluar['apellido_materno']}");  
			$objTpl->getActiveSheet()->setCellValue('B10', "{$personaEvaluar['area']}");
			$objTpl->getActiveSheet()->setCellValue('B12', "{$personaEvaluador['nombres']} {$personaEvaluador['apellido_paterno']} {$personaEvaluador['apellido_materno']}");

			$total = 0;
			foreach ($evaluacion as $key => $val) {
				$key = $key + 17;
				$objTpl->getActiveSheet()->setCellValue('B'.$key, $val['puntaje']."%");
				$ponderacion = $objTpl->getActiveSheet()->getCell('C'.$key)->getValue();
				$total = $val['puntaje']*$ponderacion + $total;
				$objTpl->getActiveSheet()->setCellValue('D'.$key, $val['puntaje']*$ponderacion."%");
			}                  

			$objTpl->getActiveSheet()->setCellValue('D26', $total."%");

			//prepare download
			$filename=$personaEvaluar['correo'].'_'.mt_rand(1,10).'.xlsx'; //just some random filename

			$objWriter = $xx->createWriter($objTpl, 'Excel2007');
			$objWriter->save(BASE_DIR.'/public/files/'.$filename);        
		}
    }	

}