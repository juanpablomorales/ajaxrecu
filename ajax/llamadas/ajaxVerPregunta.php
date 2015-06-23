<?php
require '../require/comun.php';
header('Content-Type: application/json');
$idencuesta= Peticion::get("idEncuesta");
$bd= new BaseDatos();
$sesion=new Sesion();
$isAdmin=$sesion->isAdministrador();
$modeloPregunta= new ModeloPregunta($bd);
$lista=$modeloPregunta->getListaPreguntasJson("idencuesta= '". $idencuesta. "'");

if ($lista!=="[]") {
    echo '{"verPreguntas":true,"idencuesta":'.$idencuesta.',"isAdministrador":"' . $isAdmin . '" , "datos":' . $lista . '}';
} else {
    echo '{"verPreguntas":false}';
}