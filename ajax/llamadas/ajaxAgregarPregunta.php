<?php
require '../require/comun.php';
header('Content-Type: application/json');
$idencuesta=Peticion::get("idencuesta");
$texto=Peticion::get("texto");
$sesion= new Sesion();
$bd= new BaseDatos();
$modeloPregunta= new ModeloPregunta($bd);
$pregunta= new Pregunta(null,$idencuesta,$texto);
$modeloPregunta->addPregunta($pregunta);
$isAdmin=$sesion->isAdministrador();

$lista=$modeloPregunta->getListaPreguntasJson("idencuesta='".$idencuesta."'");
if ($lista!=="[]") {
    echo '{"verPreguntas":true, "isAdministrador":"' . $isAdmin . '" ,"idencuesta":'.$idencuesta.', "datos":' . $lista . '}';
} else {
    echo '{"verPreguntas":false}';
}