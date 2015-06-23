<?php
require '../require/comun.php';
header('Content-Type: application/json');
$idpregunta=Peticion::get("idpregunta");
$texto=Peticion::get("texto");
$sesion= new Sesion();
$bd= new BaseDatos();
$modeloRespuesta= new ModeloRespuesta($bd);
$respuesta= new Respuesta(null,$idpregunta,$texto);
$modeloRespuesta->addRespuesta($respuesta);
$isAdmin=$sesion->isAdministrador();

$lista=$modeloRespuesta->getListaRespuestasJson("idpregunta='".$idpregunta."'");

if ($lista!=="[]") {
    echo '{"verRespuestas":true, "isAdministrador":"' . $isAdmin . '" ,"idPregunta":'.$idpregunta.', "datos":' . $lista . '}';
} else {
    echo '{"verRespuestas":false}';
}