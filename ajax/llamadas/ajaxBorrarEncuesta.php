<?php

require '../require/comun.php';
header('Content-Type: application/json');
$pagina = 0;
$mias = Peticion::get("mias");
$bd = new BaseDatos();
$modeloEncuesta = new ModeloEncuesta($bd);
$modeloEncuesta->delete(Peticion::get("idEncuesta"));
$sesion = new Sesion();
$emailSesion = $sesion->getUsuario()->getEmail();
$isAdmin = $sesion->isAdministrador();

if ($mias) {
    $lista = $modeloEncuesta->getListaPaginadaJSON($pagina, Configuracion::RPP, 'email="' . $emailSesion . '"');
    $enlaces = Paginacion::getEnlacesPaginacion($pagina, $modeloEncuesta->count('email="' . $emailSesion . '"'), Configuracion::RPP, "./llamadas/ajaxVerSoloMiasEncuestas.php?");
    //$lista = $modeloEncuesta->getListaEncuestasJson('email="'.$emailSesion.'"');
} else {
    $lista = $modeloEncuesta->getListaPaginadaJSON($pagina, Configuracion::RPP);
    $enlaces = Paginacion::getEnlacesPaginacion($pagina, $modeloEncuesta->count(), Configuracion::RPP, "./llamadas/ajaxVerTodasEncuestas.php?");
    //$lista = $modeloEncuesta->getListaEncuestasJson();
}

$enlaceReparado="[{";
foreach ($enlaces as $key => $value) {
    $enlaceReparado.="\"". $key."\":\"".$value ."\",";
}
$enlaceReparado=substr($enlaceReparado, 0,-1);
$enlaceReparado.="}]";

if ($lista !== "[]") {
    echo '{"verEncuestas":true,"enlaces":' . $enlaceReparado . ',"emailSesion":"' . $emailSesion . '","isAdministrador":"' . $isAdmin . '" ,"datos":' . $lista . '}';
} else {
    echo '{"verEncuestas":false}';
}