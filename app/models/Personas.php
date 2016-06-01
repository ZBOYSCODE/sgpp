<?php

namespace Gabs\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;

class Personas extends Model
{

    /**
     *
     * @var string
     */
    public $rut;

    /**
     *
     * @var string
     */
    public $apellido_paterno;

    /**
     *
     * @var string
     */
    public $correo;

    /**
     *
     * @var string
     */
    public $apellido_materno;

    /**
     *
     * @var string
     */
    public $nombres;

    /**
     *
     * @var string
     */
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

    public function getListadoEvaluados()
    {
        $query = new Query("SELECT p1.correo evaluador, p2.correo evaluado, count(p2.correo)*100/9 eval, re.rut_evaluador
                            FROM Gabs\Models\ResumenEvaluacionTipo re
                            ,    Gabs\Models\Personas p1
                            ,    Gabs\Models\Personas p2
                            WHERE re.rut_evaluador = p1.rut
                            AND   re.rut_evaluado  = p2.rut
                            GROUP BY re.rut_evaluador, re.rut_evaluado",$this->getDI());
        return $query->execute();
    }

    public function getListadoConsejoPorEvaluador($rut_evaluador)
    {
        $query = new Query("SELECT p1.correo evaluador, p2.correo evaluado, re.id_tipo, re.puntaje, tp.nombre_tipo, re.rut_evaluado, re.rut_evaluador
                            FROM   Gabs\Models\ResumenEvaluacionTipo re
                            ,      Gabs\Models\Personas p1
                            ,      Gabs\Models\Personas p2
							,      Gabs\Models\TipoPregunta tp
                            WHERE  re.rut_evaluador = p1.rut
                            AND    tp.id_tipo = re.id_tipo
                            AND    re.rut_evaluado  = p2.rut
                            AND    re.rut_evaluador ='".$rut_evaluador."'
							ORDER BY p2.correo, re.id_tipo",$this->getDI());
        return $query->execute();
    }
	
	
	
	
	
	
}
