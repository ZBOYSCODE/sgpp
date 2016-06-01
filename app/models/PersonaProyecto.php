<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class PersonaProyecto extends Model
{

    public $prsn_proy_id;
    public $rut;
    public $proy_id;
    public $area_id;
    public $hh_asignadas;

    public function initialize()
    {
    }

    public function getSource()
    {
        return 'persona_proyecto';
    }

    public function getData(){
        $query = new Query("
            SELECT p.rut, CONCAT(p.nombres,' ',p.apellido_paterno) as nombre, pm.hh_porcentaje_disponibles, month, year, hh_mensuales, area_nombre, fecha_inicio_semana, proy_nombre,proy_color,pr.proy_id,prsn_proy_id,pp.hh_porcentaje_asignadas, prsn_mes_id
            FROM Gabs\Models\PersonaProyecto pp 
            LEFT JOIN Gabs\Models\Personas p ON p.rut = pp.rut 
            LEFT JOIN Gabs\Models\PersonaMes pm on pm.rut = p.rut
            LEFT JOIN Gabs\Models\Proyecto pr ON pr.proy_id = pp.proy_id
            LEFT JOIN Gabs\Models\Area a ON a.area_id = pp.area_id", $this->getDI());
        $query = $query->execute()->toArray();        
        if(count($query)>0){
            $arr = array();
            foreach ($query as $val) {
                $arr[date('d-m-Y',strtotime($val['fecha_inicio_semana']))][$val['rut']] = $val;
            }
            return $arr;
        } else
            return $query;
    }
}
