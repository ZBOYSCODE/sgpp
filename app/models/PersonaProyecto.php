<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class PersonaProyecto extends Model
{

	public $prsn_proy_id;


    public function initialize()
    {
    }

    public function getSource()
    {
        return 'persona_proyecto';
    }
}
