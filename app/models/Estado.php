<?php

	namespace Gabs\Models;

	use Phalcon\Mvc\Model;
	use Phalcon\Mvc\Model\Query;

	class Estado extends Model
	{
	    public $id;
	    public $nombre;

	    public function initialize()
	    {
	    }

	    public function getSource()
	    {
	        return 'estados';
	    }
	}
