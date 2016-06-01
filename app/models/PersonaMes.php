<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class PersonaMes extends Model
{

    public $prsn_mes_id;
    public $rut;
    public $month;
    public $year;
    public $hh_disponibles;
    public $hh_porcentaje_disponibles;

    public function initialize()
    {
    }

    public function getSource()
    {
        return 'persona_mes';
    }

    public function getPersonaMes($data){
        $query = new Query("
                    SELECT hh_porcentaje_disponibles, prsn_mes_id, month, year, rut
                    FROM Gabs\Models\PersonaMes pm 
                    WHERE month = {$data['month']} 
                    AND year = {$data['year']}
                    AND rut = {$data['rut']}", $this->getDI());
                $query = $query->execute()->toArray();        
                if(count($query)>0)
                    return $query[0];
                else
                    return false;
    }
}
