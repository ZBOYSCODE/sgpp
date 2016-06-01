<?php
return new \Phalcon\Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'sgpp'
    ],
    'application' => [
        'controllersDir' => APP_DIR . '/controllers/',
        'modelsDir' => APP_DIR . '/models/',
        'formsDir' => APP_DIR . '/forms/',
        'viewsDir' => APP_DIR . '/views/',
        'libraryDir' => APP_DIR . '/library/',
        'pluginsDir' => APP_DIR . '/plugins/',
        'cacheDir' => APP_DIR . '/cache/',
        'baseUri' => '/sgpp/',
        'publicUrl' => '/sgpp',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D'
    ],
    'mail' => [
        'fromName' => 'Vokuro',
        'fromEmail' => 'phosphorum@phalconphp.com',
        'smtp' => array(
            'server' => 'smtp.gmail.com',
            'port' => 587,
            'security' => 'tls',
            'username' => '',
            'password' => ''
        )
    ],
    'amazon' => [
        'AWSAccessKeyId' => '',
        'AWSSecretKey' => ''
    ],
    'noAuth' => //noAuth -> configuracion de controller y acciones que no tienen que pasar por la autentificacion
    array('session'=>array('login'=>true,'logout'=>true)),
    'appTitle'=>'Sistema Gestión Personal y Proyectos',
    'appName'=>"<strong>SGPP</strong>",
    'appAutor'=>'Zenta V',
    'appAutorLink'=>'http://www.zentagroup.com/',
]);