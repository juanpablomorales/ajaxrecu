<?php

require '../require/comun.php';
header('Content-Type: application/json');

$pagina = 0;
if (Peticion::get("pagina") != null) {
    $pagina = Peticion::get("pagina");
}

$bd= new BaseDatos();
$modeloEncuesta= new ModeloEncuesta($bd);
$sesion= new Sesion();
$emailSesion= $sesion->getUsuario()->getEmail();
$isAdmin=$sesion->isAdministrador();
//$lista=$modeloEncuesta->getListaEncuestasJson();

$filas = $modeloEncuesta->getListaPaginadaJSON($pagina, Configuracion::RPP);
$enlaces = Paginacion::getEnlacesPaginacion($pagina, $modeloEncuesta->count(), Configuracion::RPP,"./llamadas/ajaxVerTodasEncuestas.php?");

$enlaceReparado="[{";
foreach ($enlaces as $key => $value) {
    $enlaceReparado.="\"". $key."\":\"".$value ."\",";
}
$enlaceReparado=substr($enlaceReparado, 0,-1);
$enlaceReparado.="}]";

if ($filas!=="[]") {
    echo '{"verEncuestas":true,"emailSesion":"' . $emailSesion . '","enlaces":' . $enlaceReparado . ',"isAdministrador":"' . $isAdmin . '" ,"datos":' . $filas . '}';
} else {
    echo '{"verEncuestas":false}';
}