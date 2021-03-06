<?php
	
	namespace Gabs\Models;


	use Phalcon\Mvc\Model;
	use Phalcon\Mvc\Model\Relation;
	
	class Bloque extends Model
	{
		public $id;
		public $usuario_id;
		public $horas = 2;
		public $fecha;
		public $orden;

	    public function getSource()
	    {
	        return "bloques";
	    }

	    public function initialize()
	    {
	        $this->hasMany('id', __NAMESPACE__ . "\Actividad", 'bloque_id', array(
	        	'alias' => 'actividad',
	        	'foreignKey' => [
	                'action' => Relation::ACTION_CASCADE,
	                'message' => 'Organization cannot be deleted because it has sites'
	            ]));

	       	$this->belongsTo("usuario_id", __NAMESPACE__ . "\Users", "id", array('alias' => 'usuario'));

	       	$this->hasOne("proyecto_id", __NAMESPACE__ . "\Proyecto", 'proy_id', array('alias'=>'proyecto'));
	    }
	}