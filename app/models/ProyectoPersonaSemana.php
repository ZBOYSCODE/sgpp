<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class ProyectoPersonaSemana extends Model
{

    public $proy_id;
    public $blsa_id;
    public $proy_nombre;
    public $hh_totales;
    public $hh_actuales;
    public $proy_activo;

    public function initialize()
    {
        $this->belongsTo('proy_id', 'Gabs\Models\Proyecto', 'proy_id', 
            array('alias' => 'proyectos')
        );
        
        $this->belongsTo('prsn_smna_id', 'Gabs\Models\PersonaSemana', 'prsn_smna_id', 
            array('alias' => 'personasemanas')
        );
    }

    public function getSource()
    {
        return 'proyecto_persona_semana';
    }
}
