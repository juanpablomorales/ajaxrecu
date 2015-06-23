<?php

require '../require/comun.php';
header('Content-Type: application/json');

$pagina = 0;
if (Peticion::get("pagina") != null) {
    $pagina = Peticion::get("pagina");
}
$email = Peticion::get("email");
$clave = Peticion::get("clave");

$bd = new BaseDatos();
$modelo = new ModeloUsuario($bd);
$sesion = new Sesion();

$filas = $modelo->getListaPaginadaJSON($pagina, Configuracion::RPP);
$enlaces = Paginacion::getEnlacesPaginacion($pagina, $modelo->count(), Configuracion::RPP,"./llamadas/ajaxSesion.php?");

$enlaceReparado="[{";
foreach ($enlaces as $key => $value) {
    $enlaceReparado.="\"". $key."\":\"".$value ."\",";
}
$enlaceReparado=substr($enlaceReparado, 0,-1);
$enlaceReparado.="}]";
//echo $enlaceReparado;

if ($sesion->isAutentificado()) {
    $usuario=$sesion->getUsuario();
    $nombreUsuario = $usuario->getNombre();
    echo '{"r":true ,"nombreSesion":"' . $nombreUsuario . '","enlaces":' . $enlaceReparado . ',"datos":' . $filas. '}';
} else {
    echo '{"r": false}';
}


/* backup sin ajax
require '../require/comun.php';
header('Content-Type: application/json');
$bd = new BaseDatos();
$modeloUsuario = new ModeloUsuario($bd);
$sesion = new Sesion();

if ($sesion->isAutentificado()) {
    $usuario = $sesion->getUsuario();
    $nombreUsuario = $usuario->getNombre();
    echo '{"r":true ,"nombreSesion":"' . $nombreUsuario . '","datos":' . $modeloUsuario->getListaJson() . '}';
} else {
    echo '{"r":false}';
}
 * 
 */