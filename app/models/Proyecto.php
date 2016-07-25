<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class Proyecto extends Model
{

    public $proy_id;
    public $blsa_id;
    public $proy_nombre;
    public $hh_totales;
    public $hh_actuales;
    public $proy_activo;

    public $equipo_id;
    public $jefeproyecto_id;
    public $coordinador_id;

    public function initialize()
    {

        $this->hasOne("jefeproyecto_id", __NAMESPACE__ . "\Users", "id", array('alias' => 'jefeproyecto'));
        $this->belongsTo("coordinador_id", __NAMESPACE__ . "\Users", "id", array('alias' => 'coordinador'));

    }

    public function getSource()
    {
        return 'proyecto';
    }
}
