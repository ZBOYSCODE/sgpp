<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class PersonaProyecto extends Model
{

    public function initialize()
    {
    }

    public function getSource()
    {
        return 'persona_proyecto';
    }
}
