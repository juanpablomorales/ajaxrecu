<?php

require '../require/comun.php';

header('Content-Type: application/json');
$idRespuesta = Peticion::get("idRespuesta");
$textoNuevo = Peticion::get("textoNuevo");

$sesion = new Sesion();
$emailSesion = $sesion->getUsuario()->getEmail();
$isAdmin = $sesion->isAdministrador();

$bd = new BaseDatos();
$modeloRespuesta = new ModeloRespuesta($bd);
$idPregunta= $modeloRespuesta->getRespuesta($idRespuesta)->getIdpregunta();
$modeloPregunta=new ModeloPregunta($bd);
$idEncuesta=$modeloPregunta->getPregunta($idPregunta)->getIdencuesta();
$modeloRespuesta->updateRespuesta($idRespuesta, $textoNuevo);

$lista = $modeloRespuesta->getListaRespuestasJson("idpregunta=\"".$idPregunta." \"");

if ($lista !== "[]") {
    echo '{"verRespuestas":true,"idEncuesta":"' . $idEncuesta . '","emailSesion":"' . $emailSesion . '","idPregunta":"' . $idPregunta . '","isAdministrador":"' . $isAdmin . '" ,"datos":' . $lista . '}';
} else {
    echo '{"verRespuestas":false}';
}