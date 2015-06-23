<?php

require '../require/comun.php';

header('Content-Type: application/json');
$idPregunta = Peticion::get("idPregunta");
$textoNuevo = Peticion::get("textoNuevo");

$sesion = new Sesion();
$emailSesion = $sesion->getUsuario()->getEmail();
$isAdmin = $sesion->isAdministrador();

$bd = new BaseDatos();
$modeloPregunta = new ModeloPregunta($bd);
$idEncuesta= $modeloPregunta->getPregunta($idPregunta)->getIdencuesta();
$modeloPregunta->updatePregunta($idPregunta, $textoNuevo);

$lista = $modeloPregunta->getListaPreguntasJson("idencuesta=\"".$idEncuesta." \"");

if ($lista !== "[]") {
    echo '{"verPreguntas":true,"emailSesion":"' . $emailSesion . '","idencuesta":"' . $idEncuesta . '","isAdministrador":"' . $isAdmin . '" ,"datos":' . $lista . '}';
} else {
    echo '{"verPreguntas":false}';
}