<?php

	namespace Gabs\Models;

	use Phalcon\Mvc\Model;
	use Phalcon\Mvc\Model\Query;

	class EquipoUser extends Model
	{
	    public $id;
	    public $nombre;

	    public function initialize()
	    {
	    	$this->belongsTo('user_id', 'Gabs\Model\Users', 'id', 
	            array('alias' => 'usuario')
	        );

	        $this->belongsTo('equipo_id', 'Gabs\Model\Equipo', 'id', 
	            array('alias' => 'equipo')
	        );
	    	
	    }

	    public function getSource()
	    {
	        return 'equipo_users';
	    }
	}