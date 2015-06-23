<?php

require '../require/comun.php';
header('Content-Type: application/json');
$idpregunta = Peticion::get("idPregunta");
$bd = new BaseDatos();
$modeloRespuesta = new ModeloRespuesta($bd);
$lista = $modeloRespuesta->getListaRespuestasJson("idpregunta= '" . $idpregunta . "'");
$sesion= new Sesion();
$isAdmin= $sesion->isAdministrador();
if ($lista !== "[]") {
    echo '{"verRespuestas":true,"idPregunta":' . $idpregunta . ',"isAdministrador":"' . $isAdmin . '" , "datos":' . $lista . '}';
} else {
    echo '{"verRespuestas":false}';
}