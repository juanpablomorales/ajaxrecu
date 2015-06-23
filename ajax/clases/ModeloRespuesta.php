<?php

class ModeloRespuesta{
    private $bd;    
    private $tabla = "respuesta";

    function __construct(BaseDatos $bd) {
        $this->bd = $bd;
    }
   
    function addRespuesta(Respuesta $respuesta) {
        $sql = "insert into $this->tabla values (null, :idpregunta, :texto);";
        $parametros["idpregunta"] = $respuesta->getIdpregunta();
        $parametros["texto"] = $respuesta->getTexto();
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        }
    function updateRespuesta($id,$textonuevo) {
        $sql = "UPDATE $this->tabla set texto=:textonuevo where id=:id;";
        $parametros["id"] = $id;
        $parametros["textonuevo"] = $textonuevo;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
    }
    function deleteRespuesta($id) {
        $sql = "delete from $this->tabla where id=:id;";
        $parametros["id"] = $id;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        return $this->bd->getNumeroFilas();
    }

    //le paso el id y me devuelve la encuesta completa
    function getRespuesta($id) {
        $sql = "select * from $this->tabla where id=:id;";
        $parametros["id"] = $id;
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $respuesta = new Respuesta();
            $respuesta->set($this->bd->getFila());
            return $respuesta;
        }
        return null;
    }
    function getListaRespuestas($condicion) {
        $list = array();
        $sql = "select * from $this->tabla where $condicion ;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $respuesta = new Respuesta();
                $respuesta->set($fila);
                $list[] = $respuesta;
            }
        } else {
            return null;
        }
        return $list;
    }
     function countRespuestas($idpregunta) {
        $sql = "select * from $this->tabla where idpregunta=:idpregunta;";
        $parametros["idpregunta"] = $idpregunta;
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
    function getListaRespuestasJson($condicion="1=1") {
        $list = "[";
        $sql = "select * from $this->tabla where $condicion;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $respuesta = new Respuesta();
                $respuesta->set($fila);
                $list.= $respuesta->getJSON().",";
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
            $objeto = new Respuesta();
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
