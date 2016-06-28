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

	    	$this->hasOne('estado_id', __NAMESPACE__ . "\Estado", 'id', array('alias' => 'estado'));
	        $this->belongsTo("bloque_id", __NAMESPACE__ . "\Bloque", "id", array('alias' => 'bloque'));
	        $this->belongsTo("proyecto_id", __NAMESPACE__ . "\Proyecto", "proy_id", array('alias' => 'proyecto'));
	    }

	    public function getActividades($filtros)
	    {
	    	extract($filtros);
	    
	    	$sql = "SELECT * 
                    FROM Gabs\Models\Actividad a 
                    INNER JOIN  Gabs\Models\Bloque b ON b.id = a.bloque_id 
                    INNER JOIN  Gabs\Models\Users u ON b.usuario_id = u.id
                    INNER JOIN  Gabs\Models\Personas p ON p.rut = u.rut 
                    WHERE 1=1 ";

            $sql .= " AND b.fecha = '$fecha' ";

            if($proyecto > 0){
                $sql .= " AND a.proyecto_id = $proyecto ";
            }

            if($estado > 0){
                $sql .= " AND a.estado_id = $estado ";
            }

            if($usuario > 0){
                $sql .= " AND p.rut = '$usuario' ";
            }

            if($hhr){
                $sql .= " AND a.hh_reales = 0 ";
            }

            $result = $this->modelsManager->executeQuery($sql);

            return $result;
	    }
	}