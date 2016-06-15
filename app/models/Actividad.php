<?php
	
	namespace Gabs\Models;

	use Phalcon\Mvc\Model;

	class Actividad extends Model
	{
		public $id;
		public $bloque_id;
		public $proyecto_id;
		public $hh_estimadas;
		public $hh_reales;
		public $descripcion;

	    public function getSource()
	    {
	        return "actividades";
	    }

	    public function initialize()
	    {
	        $this->belongsTo("bloque_id", __NAMESPACE__ . "\Bloque", "id", array('alias' => 'bloque'));
	    }
	}