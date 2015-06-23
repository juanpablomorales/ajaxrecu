<?php

class ModeloPregunta {

    private $bd;
    private $tabla = "pregunta";

    function __construct(BaseDatos $bd) {
        $this->bd = $bd;
    }

    function addPregunta(Pregunta $pregunta) {
        $sql = "insert into $this->tabla values (null, :idencuesta, :texto);";
        $parametros["idencuesta"] = $pregunta->getIdencuesta();
        $parametros["texto"] = $pregunta->getTexto();
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
    }
     function updatePregunta($id,$textonuevo) {
        $sql = "UPDATE $this->tabla set texto=:textonuevo where id=:id;";
        $parametros["id"] = $id;
        $parametros["textonuevo"] = $textonuevo;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
    }

    function deletePregunta($idPregunta) {
        $sql = "delete from $this->tabla where id=:id;";
        $parametros["id"] = $idPregunta;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        return $this->bd->getNumeroFilas();
    }

    //le paso el id y me devuelve la encuesta completa
    function getPregunta($idPregunta) {
        $sql = "select * from $this->tabla where id=:id;";
        $parametros["id"] = $idPregunta;
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $pregunta = new Pregunta();
            $pregunta->set($this->bd->getFila());
            return $pregunta;
        }
        return null;
    }

    function getListaPreguntas($condicion) {
        $list = array();
        $sql = "select * from $this->tabla where $condicion;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $pregunta = new Pregunta();
                $pregunta->set($fila);
                $list[] = $pregunta;
            }
        } else {
            return null;
        }
        return $list;
    }

    function countPreguntas($idencuesta) {
        $sql = "select * from $this->tabla where idencuesta=:idencuesta;";
        $parametros["idencuesta"] = $idencuesta;
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $contador = 0;
            while ($fila = $this->bd->getFila()) {
                $contador++;
            }
        } else {
            return null;
        }
        return $contador;
    }
    
    function getListaPreguntasJson($condicion="1=1") {
        $list = "[";
        $sql = "select * from $this->tabla where $condicion;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $pregunta = new Pregunta();
                $pregunta->set($fila);
                $list.= $pregunta->getJSON().",";
            }
            if($list=="["){
                return $list="\"vacio\"";
            }
            $list=substr($list,0,-1);
            $list.="]";
        } else {
            return "[]";
        }
        return $list;
    }
    function getListaPaginadaJSON($pagina = 0, $rpp = 3, $condicion = "1=1", $parametros = array(), $orderby = "1") {
        $pos = $pagina * $rpp;
        $sql = "select * from "
                . $this->tabla .
                " where $condicion order by $orderby limit $pos, $rpp";
        $this->bd->setConsulta($sql, $parametros);
        $r = "[ ";
        while ($fila = $this->bd->getFila()) {
            $objeto = new Pregunta();
            $objeto->set($fila);
            $r .= $objeto->getJSON() . ",";
        }
        $r = substr($r, 0, -1) . "]";
        return $r;
    }

    function count($condicion = "1=1", $parametros = array()) {
        $sql = "select count(*) from $this->tabla where $condicion";
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $aux = $this->bd->getFila();
            return $aux[0];
        }
        return -1;
    }
}
