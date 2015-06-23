<?php

require '../require/comun.php';
header('Content-Type: application/json');
$sesion = new Sesion();
$sesion->close();
if (!$sesion->isAutentificado()) {
    echo '{"logout":true}';
} else {
    echo '{"logout": false}';
}