<?php
require '../require/comun.php';
header('Content-Type: application/json');
$pagina = 0;
if (Peticion::get("pagina") != null) {
    $pagina = Peticion::get("pagina");
}
$bd = new BaseDatos();
$modeloEncuesta = new ModeloEncuesta($bd);
$sesion = new Sesion();
$emailSesion = $sesion->getUsuario()->getEmail();
//$lista=$modeloEncuesta->getListaEncuestasJson('email="'.$emailSesion.'"');
$lista = $modeloEncuesta->getListaPaginadaJSON($pagina, Configuracion::RPP, 'email="' . $emailSesion . '"');
$enlaces = Paginacion::getEnlacesPaginacion($pagina, $modeloEncuesta->count('email="' . $emailSesion . '"'), Configuracion::RPP, "./llamadas/ajaxverSoloMiasEncuestas.php?");

$isAdmin = $sesion->isAdministrador();

$enlaceReparado="[{";
foreach ($enlaces as $key => $value) {
    $enlaceReparado.="\"". $key."\":\"".$value ."\",";
}
$enlaceReparado=substr($enlaceReparado, 0,-1);
$enlaceReparado.="}]";
//echo $enlaceReparado;

if ($lista !== "[]") {
    echo '{"verEncuestas":true ,"enlaces":' . $enlaceReparado . ',"emailSesion":"' . $emailSesion . '","isAdministrador":"' . $isAdmin . '" ,"datos":' . $lista . '}';
} else {
    echo '{"verEncuestas":false}';
}