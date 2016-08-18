<?php
    namespace Gabs\Controllers;

    use Gabs\Models\Bloque;
    use Gabs\Models\Actividad;

    use Gabs\Valida\Valida;

    class PruebaController extends ControllerBase
    {

        public function indexAction()
        {

            $post['name']       = 'Seba';
            $post['apellido']   = 'Silva';
            $post['edad']       = (int)'2hola';
            $post['mail']       = 'seba@hotmail.com';

            $valida = new Valida($post,[
                'name'      =>  'required|max:5',
                'apellido'  =>  'required|string',
                'edad'      =>  'required|int|min:2|max:10',
                'mail'      =>  'email'
            ]);

            if( $valida->failed() ){
                print_r($valida->errors);
                return false;
            }

           
        }
    }