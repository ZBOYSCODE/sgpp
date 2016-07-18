<?php
namespace Gabs\Models;
use Phalcon\Mvc\Model;

class Roles extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $nombre;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany(__NAMESPACE__.'\id', 'Users', 'rol_id', array('alias' => 'usuario'));

        $this->hasMany('id', __NAMESPACE__ . '\Users', 'rol_id', array(
            'alias' => 'Users',
            'foreignKey' => array(
                'message' => 'Rol no puede ser eliminado dado que tiene usuarios relacionados'
            )
        ));


    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'roles';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Prioridad[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Prioridad
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
