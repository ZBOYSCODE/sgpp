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

/*
    public function getPersonasByProyecto($data)
    {
        $query = new Query("SELECT p.rut, nombres, apellido_paterno 
                            FROM Gabs\Models\ProyectoPersonaSemana pps 
                            LEFT JOIN Gabs\Models\PersonaSemana ps ON ps.prsn_smna_id = pps.prsn_smna_id 
                            LEFT JOIN Gabs\Models\Personas p ON p.rut = ps.rut 
                            WHERE pps.proy_id = {$data['proy_id']} 
                            GROUP BY p.rut",$this->getDI());
        return $query->execute()->toArray();
    }*/

    public function getPersonasByProyecto($data)
    {
        $query = new Query("SELECT p.rut, nombres, apellido_paterno, activo, pp.prsn_proy_id
                            FROM Gabs\Models\Personas p 
                            LEFT JOIN Gabs\Models\PersonaProyecto pp ON pp.rut = p.rut
                            WHERE pp.proy_id = {$data['proy_id']}",$this->getDI());


        $query = $query->execute();     
        if(count($query)>0) 
            return $query->toArray();
        else
            return array();
    }


    public function getPersonasSinProyecto($data)
    {

        $query = new Query("SELECT rut FROM Gabs\Models\PersonaProyecto WHERE proy_id = {$data['proy_id']}",$this->getDI());
        $query = $query->execute();   
        if(count($query)>0){
            $query = $query->toArray();
            $arr = array();
            foreach ($query as $rut) {
                $arr[] = $rut['rut'];
            }
            $arr = implode($arr, "', '");
            $query = new Query("
                        SELECT * FROM Gabs\Models\Personas WHERE rut NOT IN ('{$arr}')",$this->getDI());
            $query = $query->execute();  
            return $query->toArray();               
        } else{
            $query = new Query("SELECT * FROM Gabs\Models\Personas ",$this->getDI());
            $query = $query->execute();
            return $query->toArray();
        }




    }

    
    
}
