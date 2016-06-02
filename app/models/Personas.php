<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class Personas extends Model
{
    public $rut;
    public $apellido_paterno;
    public $correo;
    public $apellido_materno;
    public $nombres;
    public $area;
    public $hh_mensuales;
    public $hh_disponibles;
    public $hh_porcentaje_disponibles;
    public function initialize()
    {
    }
    
    public function getSource()
    {
        return 'personas';
    }

    public function getAll()
    {
        $query = new Query("SELECT * FROM  Gabs\Models\Personas", $this->getDI());
        return $query->execute();        
    }

    public function getByUser($id)
    {
        $query = new Query("SELECT * FROM  Gabs\Models\Personas as p 
                            WHERE p.lider_id = ".$id, $this->getDI());
        return $query->execute();        
    }

    public function getPersonasByProyecto($data)
    {
        $query = new Query("SELECT p.rut, nombres, apellido_paterno 
                            FROM Gabs\Models\ProyectoPersonaSemana pps 
                            LEFT JOIN Gabs\Models\PersonaSemana ps ON ps.prsn_smna_id = pps.prsn_smna_id 
                            LEFT JOIN Gabs\Models\Personas p ON p.rut = ps.rut 
                            WHERE pps.proy_id = {$data['proy_id']} 
                            GROUP BY p.rut",$this->getDI());
        return $query->execute()->toArray();
    }

	
	
	
	
	
}
