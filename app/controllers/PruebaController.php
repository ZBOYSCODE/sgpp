<?php
    namespace Gabs\Controllers;

    use Gabs\Models\Bloque;
    use Gabs\Models\Actividad;

    class PruebaController extends ControllerBase
    {

        public function indexAction()
        {
        	echo $this->relaciones();
        }

        private function relaciones()//
        {
            
            /*$bloques = Bloque::find(array(
                "id = 8"
            ));*/

            $bloques = Bloque::findById(8);

            echo "<pre>";

            foreach ($bloques as $bloque) {
                echo $bloque->id."<br>";
                echo $bloque->fecha."<br>";

                foreach ($bloque->actividad as $actividad) {
                     echo $actividad->descripcion."<br>";
                }
            }
        }

        private function getFloatByHrs($horas)// 10:30
        {
        	$horas = "03:05";
        	$arr = explode(':', $horas);
        	$h = $arr[0];
        	$m = $arr[1];

        	$num = $h*3600 + $m*60; 

    		$num = ($num/60)/60;

    		return floatval(round($num, 2));
        }
    }