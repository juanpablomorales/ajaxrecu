<?php

require '../require/comun.php';
header('Content-Type: application/json');

/* paginacion*/
$pagina = 0;
if (Peticion::get("pagina") !== null) {
    $pagina = Peticion::get("pagina");
}

$email = Peticion::get("email");
$clave = Peticion::get("clave");


$bd = new BaseDatos();
$modelo = new ModeloUsuario($bd);

/*paginacion*/
$filas = $modelo->getListaPaginadaJSON($pagina, Configuracion::RPP);
$enlaces = Paginacion::getEnlacesPaginacion($pagina, $modelo->count(), Configuracion::RPP,"./llamadas/ajaxSesion.php?");
$enlaceReparado="[{";
foreach ($enlaces as $key => $value) {
    $enlaceReparado.="\"". $key."\":\"".$value ."\",";
}
$enlaceReparado=substr($enlaceReparado, 0,-1);
$enlaceReparado.="}]";

if ($modelo->login($email, $clave)) {
    $sesion = new Sesion();
    $usuario = $modelo->get($email);
    $sesion->setUsuario($usuario);
    $nombreUsuario = $usuario->getNombre();
    echo '{"login":true ,"nombreSesion":"' . $nombreUsuario . '","enlaces":' . $enlaceReparado . ',"datos":' . $filas. '}';
} else {
    echo '{"login": false}';
}

/**/


/* 
 //backup version sin paginacion funcionando
require '../require/comun.php';
$email = Peticion::get("email");
$clave = Peticion::get("clave");
header('Content-Type: application/json');
$bd = new BaseDatos();
$modeloUsuario = new ModeloUsuario($bd);
 
if ($modeloUsuario->loginJSon($email, $clave)) {
    $sesion = new Sesion();
    $usuario = $modeloUsuario->get($email);
    $sesion->setUsuario($usuario);
    $nombreUsuario = $usuario->getNombre();
    echo '{"login":true ,"nombreSesion":"' . $nombreUsuario . '","datos":' . $modeloUsuario->getListaJson() . '}';
} else {
    echo '{"login": false}';
}
*/
