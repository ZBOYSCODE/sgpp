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
    }

    public function getSource()
    {
        return 'proyecto_persona_semana';
    }
}
