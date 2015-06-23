<?php
require '../require/comun.php';
header('Content-Type: application/json');

$bd= new BaseDatos();
$modeloRespuesta= new ModeloRespuesta($bd);
$idPregunta=$modeloRespuesta->getRespuesta(Peticion::get("idRespuesta"))->getIdpregunta();
$modeloRespuesta->deleteRespuesta(Peticion::get("idRespuesta"));

$sesion= new Sesion();
$emailSesion= $sesion->getUsuario()->getEmail();
$isAdmin=$sesion->isAdministrador();
$lista=$modeloRespuesta->getListaRespuestasJson("idpregunta='".$idPregunta."'");

if ($lista!=="[]") {
    echo '{"verRespuestas":true, "isAdministrador":"' . $isAdmin . '" ,"idPregunta":'.$idPregunta.', "datos":' . $lista . '}';
} else {
    echo '{"verRespuestas":false}';
}