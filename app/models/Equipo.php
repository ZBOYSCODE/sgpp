<?php

	namespace Gabs\Models;

	use Phalcon\Mvc\Model;
	use Phalcon\Mvc\Model\Query;

	class Equipo extends Model
	{
	    public $id;
	    public $nombre;

	    public function initialize()
	    {
	    	$this->hasManyToMany(
	            "id",
	            "Gabs\Models\EquipoUser",
	            "equipo_id",
	            "user_id",
	            "Gabs\Models\Users",
	            "id",
	            array('alias' => 'usuarios')
	        );
	    }

	    public function getSource()
	    {
	        return 'equipos';
	    }
	}
