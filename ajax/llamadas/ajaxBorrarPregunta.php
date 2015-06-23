<?php
require '../require/comun.php';
header('Content-Type: application/json');

$bd= new BaseDatos();
$modeloPregunta= new ModeloPregunta($bd);
$idEncuesta=$modeloPregunta->getPregunta(Peticion::get("idPregunta"))->getIdencuesta();
$modeloPregunta->deletePregunta(Peticion::get("idPregunta"));

$sesion= new Sesion();

$emailSesion= $sesion->getUsuario()->getEmail();
$isAdmin=$sesion->isAdministrador();
$lista=$modeloPregunta->getListaPreguntasJson("idencuesta='".$idEncuesta."'");

if ($lista!=="[]") {
    echo '{"verPreguntas":true, "idencuesta":'.$idEncuesta.', "isAdministrador":"' . $isAdmin . '" , "datos":' . $lista . '}';
} else {
    echo '{"verPreguntas":false}';
}