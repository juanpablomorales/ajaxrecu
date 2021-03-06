<?php

class ModeloEncuesta {

    private $bd;
    private $tabla = "encuesta";

    function __construct(BaseDatos $bd) {
        $this->bd = $bd;
    }

    function addEncuesta(Encuesta $encuesta) {
        $sql = "insert into $this->tabla values (null, :email, :titulo);";
        $parametros["email"] = $encuesta->getEmail();
        $parametros["titulo"] = $encuesta->getTitulo();
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
    }
     function updateEncuesta($id,$titulonuevo) {
        $sql = "UPDATE $this->tabla set titulo=:titulonuevo where id=:id;";
        $parametros["id"] = $id;
        $parametros["titulonuevo"] = $titulonuevo;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
    }

    function delete($id) {
        $sql = "delete from $this->tabla where id=:id;";
        $parametros["id"] = $id;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        return $this->bd->getNumeroFilas();
    }

    //le paso el id y me devuelve la encuesta completa
    function get($id) {
        $sql = "select * from $this->tabla where id=:id;";
        $parametros["id"] = $id;
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $encuesta = new Encuesta();
            $encuesta->set($this->bd->getFila());
            return $encuesta;
        }
        return null;
    }

    function getListaEncuestas($condicion="1=1") {
        $list = array();
        $sql = "select * from $this->tabla where $condicion order by titulo;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $encuesta = new Encuesta();
                $encuesta->set($fila);
                $list[] = $encuesta;
            }
        } else {
            return null;
        }
        return $list;
    }
    function getListaEncuestasJson($condicion="1=1") {
        $list = "[";
        $sql = "select * from $this->tabla where $condicion;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $encuesta = new Encuesta();
                $encuesta->set($fila);
                $list.= $encuesta->getJSON().",";
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
            $objeto = new Encuesta();
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
