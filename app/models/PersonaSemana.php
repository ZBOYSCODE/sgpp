<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class PersonaSemana extends Model
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
        return 'persona_semana';
    }

    public function getData($data = null){
        if(is_null($data))
            $proyecto = "";
        else
            $proyecto = " WHERE pr.proy_id = ".$data['proy_id']." ";
         
        $query = new Query("
            SELECT p.rut, CONCAT(p.nombres,' ',p.apellido_paterno) as nombre, hh_mensuales, area_nombre, fecha_inicio_semana, proy_nombre,proy_color,pr.proy_id,ps.prsn_smna_id,ps.hh_total_porcentaje_asignadas,pps.hh_porcentaje_asignadas, proy_ps_id
            FROM Gabs\Models\PersonaSemana ps 
            LEFT JOIN Gabs\Models\Personas p ON p.rut = ps.rut 
            LEFT JOIN Gabs\Models\ProyectoPersonaSemana pps ON ps.prsn_smna_id = pps.prsn_smna_id
            LEFT JOIN Gabs\Models\Proyecto pr ON pr.proy_id = pps.proy_id 
            LEFT JOIN Gabs\Models\Area a ON a.area_id = ps.area_id ".$proyecto."
             ORDER BY pps.hh_porcentaje_asignadas DESC", $this->getDI());
        $query = $query->execute()->toArray(); 
        if(count($query)>0){
            $arr = array();
            foreach ($query as $val) {
                $arr[date('d-m-Y',strtotime($val['fecha_inicio_semana']))][$val['rut']][$val['proy_id']] = $val;
            }
            return $arr;
        } else
            return $query;
    }
}
