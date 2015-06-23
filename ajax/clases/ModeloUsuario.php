<?php

/**
 * Description of ModeloUsuario
 *
 * @author juanpablo
 */
class ModeloUsuario {

    //Implementamos los métodos que necesitamos para trabajar con la tabla
    private $bd;
    private $tabla = "usuario";

    function __construct(BaseDatos $bd) {
        $this->bd = $bd;
    }

    //INSERT INTO `usuario`(`email`, `contrasenia`, `nombre`, `rol`, `activo`)
    /*
     *  insert -> insert(usuario) return true|false
     *  get-> get(email), getLista(condicion) return objeto
     *  update -> update(sinclave), update(conclave) return true|false
     *  delete-> delete(email) return true|false
     *  login (email,clave) return objeto|false
     *      
     */

    function insert(Usuario $objeto) {
        $sql = "insert into $this->tabla values (:email, :contrasenia, :nombre, :rol, :activo);";
        $parametros["email"] = $objeto->getEmail();
        $parametros["contrasenia"] = $objeto->getContrasenia();
        $parametros["nombre"] = $objeto->getNombre();
        $parametros["rol"] = $objeto->getRol();
        $parametros["activo"] = 0;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        return $r;
    }

    function login($email, $clave) {
        $sql = "select email from usuario where contrasenia=:contrasenia and activo=1;";
        $parametros["contrasenia"] = sha1($clave);
        $r = $this->bd->setConsulta($sql, $parametros);
        $resultado = $this->bd->getFila();
        $emailEncontrado = $resultado[0];
        if ($r) {
            if ($email == $emailEncontrado && $email !="") {
                return $this->get($emailEncontrado);
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    /*
      function loginJSon($email, $clave) {
      $sql = "select email from usuario where contrasenia=:contrasenia and activo=1 and email=:email;";
      $parametros["email"] = $email;
      $parametros["contrasenia"] = sha1($clave);
      $r = $this->bd->setConsulta($sql, $parametros);
      if ($r) {
      $resultado = $this->bd->getFila();
      $emailEncontrado = $resultado[0];
      if ($email == $emailEncontrado && $resultado) {
      $usuario = $this->get($emailEncontrado);
      return $usuario->getJSON();
      }
      return false;
      }
      }
     */

    function deletePorEmail($email) {
        $sql = "delete from $this->tabla where email=:email;";
        $parametros["email"] = $email;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        return $this->bd->getNumeroFilas();
    }

    //le paso el email y me devuelve un usuario completo
    function get($email) {
        $sql = "select * from $this->tabla where email=:email;";
        $parametros["email"] = $email;
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $usuario = new usuario();
            $usuario->set($this->bd->getFila());
            return $usuario;
        }
        //return new usuario();
        return null;
    }

    function activa($chorizo) {
        $sql = 'update usuario set activo = 1 where activo = 0 and md5(concat(email,"' . Configuracion::PEZARANA . '",contrasenia)) =:chorizo';
        $parametros["chorizo"] = $chorizo;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        } else {
            return $this->bd->getNumeroFilas();
        }
    }

    function recuerdaClave($recuerdaChorizo, $clavenueva) {
        $sql = 'update usuario set contrasenia=:contrasenia where md5(concat("' . Configuracion::PEZARANA . '", email,"' . Configuracion::PEZARANA . '")) =:recuerdaChorizo';
        $parametros["recuerdaChorizo"] = $recuerdaChorizo;
        $parametros["contrasenia"] = $clavenueva;
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        } else {
            return $this->bd->getNumeroFilas();
        }
    }

    function getLista($condicion) {
        $list = array();
        $sql = "select * from $this->tabla where $condicion;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $usuario = new usuario();
                $usuario->set($fila);
                $list[] = $usuario;
            }
        } else {
            return null;
        }
        return $list;
    }

    function getListaJson($condicion = "1=1") {
        $list = "[";
        $sql = "select * from $this->tabla where $condicion;";
        $r = $this->bd->setConsulta($sql);
        if ($r) {
            while ($fila = $this->bd->getFila()) {
                $usuario = new usuario();
                $usuario->set($fila);
                $list.= $usuario->getJSON() . ",";
            }
            $list = substr($list, 0, -1);
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
            $objeto = new Usuario();
            $objeto->set($fila);
            $r .= $objeto->getJSON() . ",";
        }
        $r = substr($r, 0, -1) . "]";
        return $r;
    }

    function update(Usuario $usuario) {
        $sql = "UPDATE $this->tabla SET contrasenia=:contrasenia, "
                . "nombre=:nombre, rol=:rol, activo=:activo where email=:email;";
        $parametros["email"] = $usuario->getEmail();
        $parametros["contrasenia"] = $usuario->getContrasenia();
        $parametros["nombre"] = $usuario->getNombre();
        $parametros["rol"] = $usuario->getRol();
        $parametros["activo"] = $usuario->getActivo();
        $r = $this->bd->setConsulta($sql, $parametros);
        if (!$r) {
            return -1;
        }
        return $this->bd->getNumeroFilas();
    }

    function isAdministrador($email) {
        $sql = "select * from $this->tabla where email=:email;";
        $parametros["email"] = $email;
        $r = $this->bd->setConsulta($sql, $parametros);
        if ($r) {
            $usuario = new usuario();
            $usuario->set($this->bd->getFila());
            if ($usuario->getRol() === "administrador") {
                return true;
            } else {
                return false;
            }
        }
        return null;
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
