<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class Area extends Model
{
    public $area_id;
    public $area_nombre;

    public function initialize()
    {
    }

    public function getSource()
    {
        return 'area';
    }
}
