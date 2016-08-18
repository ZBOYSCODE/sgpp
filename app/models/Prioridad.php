<?php

	namespace Gabs\Models;

	use Phalcon\Mvc\Model;
	use Phalcon\Mvc\Model\Query;

	class Prioridad extends Model
	{
	    public $id;
	    public $nombre;

	    public function initialize()
	    {
	    	
	    }

	    public function getSource()
	    {
	        return 'prioridades';
	    }
	}
