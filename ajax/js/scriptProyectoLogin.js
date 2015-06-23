var peticionAjax = null;
var paginar = function (enlaces, funcionRespuesta) {
    funcionClickEnlace("inicio", enlaces[0].inicio, funcionRespuesta);
    funcionClickEnlace("anterior", enlaces[0].anterior, funcionRespuesta);
    funcionClickEnlace("primero", enlaces[0].primero, funcionRespuesta);
    funcionClickEnlace("segundo", enlaces[0].segundo, funcionRespuesta);
    funcionClickEnlace("actual", enlaces[0].actual, funcionRespuesta);
    funcionClickEnlace("cuarto", enlaces[0].cuarto, funcionRespuesta);
    funcionClickEnlace("quinto", enlaces[0].quinto, funcionRespuesta);
    funcionClickEnlace("siguiente", enlaces[0].siguiente, funcionRespuesta);
    funcionClickEnlace("ultimo", enlaces[0].ultimo, funcionRespuesta);
};
var nopaginar = function () {
    asignar("inicio", "");
    asignar("anterior", "");
    asignar("primero", "");
    asignar("segundo", "");
    asignar("actual", "");
    asignar("cuarto", "");
    asignar("quinto", "");
    asignar("siguiente", "");
    asignar("ultimo", "");
};
//funcion para facilitar la insercion de datos en los campos
var asignar = function (id, valor) {
    var elemento = document.getElementById(id);
    elemento.innerHTML = valor;
};
var funcionClickEnlace = function (id, pagina, funcionRespuesta) {
    if (pagina !== "") {
        var elemento = document.getElementById(id);
        elemento.innerHTML = pagina;
        elemento = document.getElementById(id);
        if (elemento.firstChild.firstChild !== null) {
            var link = "";
            link = elemento.firstChild.getAttribute("href");
            elemento.firstChild.setAttribute("href", "#");
            //vuelvo a llamar
            elemento = document.getElementById(id);
            elemento.addEventListener("click", function () {
                ajaxGET(link, funcionRespuesta);
            });
        }
    }else{
        asignar(id,"");
    }
};
var agregar = function (id, valor) {
    var elemento = document.getElementById(id);
    elemento.innerHTML += valor;
};
var creaBotonAtras = function (dondevolver) {
    var contenedor = document.getElementById("page-wrapper");
    var btVolver = document.getElementById("btVolver");
    if (!btVolver) {
        var boton = document.createElement("button");
        boton.textContent = "Volver atras";
        boton.setAttribute("id", "btVolver");
        contenedor.appendChild(boton);
        var btVolver = document.getElementById("btVolver");// lo vuelvo a capturar
    }
    if (dondevolver === "encuesta") {
        btVolver.addEventListener("click", function () {
            verTodasEncuestas();
        });
    } else {
        btVolver.addEventListener("click", function () {
            clickVerPregunta(dondevolver);
        });
    }
};
//implementar respuesta en get
var ajaxGET = function (url, funcionRespuesta) {
    peticionAjax = new XMLHttpRequest();
    peticionAjax.open("GET", url, true);
    peticionAjax.onreadystatechange = funcionRespuesta;
    peticionAjax.send();
};
//implementar respuesta en post
var AjaxPOST = function (url, parametros, funcionRespuesta) {
    peticionAjax = new XMLHttpRequest();
    peticionAjax.open("POST", url, true);
    peticionAjax.onreadystatechange = funcionRespuesta;
    peticionAjax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    peticionAjax.send(parametros);
};
/* controlar las respuestas de PHP*/
//control respuesta si hay sesion o no (carga inicial)    
var respuestaAjax = function () {
    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            var json = JSON.parse(peticionAjax.responseText);
            if (json.r) {
                document.getElementById("fNoLogin").style.display = "block";
                var nombreUsuario = document.getElementsByClassName("nombreUsuario");
                nombreUsuario[0].innerHTML = json.nombreSesion;
                nombreUsuario[1].innerHTML = json.nombreSesion;
                //cabecera por que la machaco cada vez que la creo
                asignar("tableDatos", "<tr><th>Email</th><th>Nombre</th><th>Rol</th><th>Activo</th><th>Borrar</th><th>Editar</th><tr>");
                for (var i in json.datos) {
                    funcionQueCreaLaFila(json.datos[i], "tableDatos"); //crea la fila y lo añade al final de la tabla
                }
                //enlaces de la paginacion para usuario
                paginar(json.enlaces, respuestaAjax);

            } else {
                document.getElementById("containerflogin").style.display = "block";
                peticionAjax = null;
            }

        } else {
           document.getElementById("containerflogin").style.display = "none";
        }
    }else{
        document.getElementById("containerflogin").style.display = "none";
    }
};

//control respuesta para loguearte
var respuestaAjaxLogin = function () {
    console.log(peticionAjax);
    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            console.log(peticionAjax.responseText);
            var json = JSON.parse(peticionAjax.responseText);
            if (json.login) {
                asignar("titulo", "Usuarios:");
                document.getElementById("containerflogin").style.display = "none";
                document.getElementById("fNoLogin").style.display = "block";
                var nombreUsuario = document.getElementsByClassName("nombreUsuario");
                nombreUsuario[0].innerHTML = json.nombreSesion;
                nombreUsuario[1].innerHTML = json.nombreSesion;
                //cabecera por que la machaco cada vez que la creo
                asignar("tableDatos", "<tr><th>Email</th><th>Nombre</th><th>Rol</th><th>Activo</th><th>Borrar</th><th>Editar</th><tr>");
                for (var i in json.datos) {
                    //crea la fila y lo añade al final de la tabla
                    funcionQueCreaLaFila(json.datos[i], "tableDatos");
                }
                //enlaces de la paginacion
                paginar(json.enlaces, respuestaAjax);
            } else {
                document.getElementById("containerflogin").style.display = "block";
                //pendiente de arreglar
                alert("error en el logueo");
            }
        } else {
            //hay algun error
        }
    }
};
// control respuesta para hacer logOut
var respuestaAjaxLogOut = function () {
    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            var json = JSON.parse(peticionAjax.responseText);
            if (json.logout) {
                //logOut con exito
                document.getElementById("fNoLogin").style.display = "none";
                document.getElementById("inputEmail").value = "";
                document.getElementById("inputClave").value = "";
                containerlogin = document.getElementById("containerflogin").style.display = "block";
            }
        } else {
            //hay algun error
        }
    }
};
// control respuesta para ver Todas las Encuestas (independientemente del creador)
var respuestaAjaxVerTodasEncuestas = function () {
    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            var json = JSON.parse(peticionAjax.responseText);
            if (json.verEncuestas) {
                document.getElementById("containerflogin").style.display = "none";
                //cabecera por que la machaco cada vez que la creo
                asignar("titulo", "Todas las encuestas:");
                asignar("tableDatos", "<tr><th>ID</th><th>Email</th><th>Titulo</th>");
                for (var i in json.datos) {
                    //crea la fila y lo añade al final de la tabla
                    funcionQueCreaLaFilaEncuesta(json.datos[i], "tableDatos", json.isAdministrador, json.emailSesion);
                }
                paginar(json.enlaces,respuestaAjaxVerTodasEncuestas);
                agregar("tableDatos", "<tr><td></td><td><input id='emailEncuesta' type='text' value='" +
                        json.emailSesion + "' disabled=''></td>" +
                        "<td><input type='text' id='tituloEncuesta' onblur='blurTitulo()'></td></tr>");

                
                boton = document.getElementById("btVolver");
                if (boton) {
                    boton.parentNode.removeChild(boton);
                }
            }
        } else {
            //hay algun error
        }
    }
};
// control respuesta para ver solo las Encuestas del usuario que las ha creado
var respuestaAjaxVerSoloMiasEncuestas = function () {
    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            var json = JSON.parse(peticionAjax.responseText);
            if (json.verEncuestas) {
                asignar("titulo", "Solamente mis encuestas:");
                //cabecera por que la machaco cada vez que la creo
                asignar("tableDatos", "<tr><th>ID</th><th>Email</th><th>Titulo</th>");
                for (var i in json.datos) {
                    //crea la fila y lo añade al final de la tabla
                    funcionQueCreaLaFilaEncuesta(json.datos[i], "tableDatos", json.isAdministrador, json.emailSesion);
                }
                paginar(json.enlaces, respuestaAjaxVerSoloMiasEncuestas);
                agregar("tableDatos", "<tr><td></td><td><input id='emailEncuesta' type='text' value='" +
                        json.emailSesion + "' disabled=''></td>" +
                        "<td><input type='text' id='tituloEncuesta' onblur='blurTitulo()'><input type='hidden' value=\"mias\" id='banderaTodas'></td></tr>");
            }
            boton = document.getElementById("btVolver");
            if (boton) {
                boton.parentNode.removeChild(boton);
            }

        } else {
            //hay algun error
        }
    }
};
// control respuesta para ver las preguntas de una encuesta
var respuestaAjaxVerPreguntas = function () {

    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            var json = JSON.parse(peticionAjax.responseText);
            if (json.verPreguntas) {
                //cabecera por que la machaco cada vez que la creo
                asignar("titulo", "Preguntas:");
                asignar("tableDatos", "<tr><th>ID</th><th>Id Encuesta</th><th>Texto</th>");
                if (json.datos === "vacio") {
                    funcionQueCreaLaFilaPreguntaVacio("tableDatos");
                    nopaginar();
                } else {
                    for (var i in json.datos) {
                        //crea la fila y lo añade al final de la tabla
                        funcionQueCreaLaFilaPregunta(json.datos[i], "tableDatos", json.isAdministrador);
                    }
                    nopaginar();
                }
                agregar("tableDatos", "<tr><td></td><td><input id='idencuestaPregunta' type='text' value='" +
                        json.idencuesta + "' disabled=''></td>" +
                        "<td><input type='text' id='textoPregunta' onblur='blurPregunta()'></td></tr>");

                creaBotonAtras("encuesta");
            }
        } else {
            //hay algun error
        }
    }
};
// control respuesta para ver las respuestas de una pregunta
var respuestaAjaxVerRespuestas = function () {
    if (peticionAjax.readyState === 4) {
        if (peticionAjax.status === 200) {
            var json = JSON.parse(peticionAjax.responseText);
            if (json.verRespuestas) {
                //cabecera por que la machaco cada vez que la creo
                asignar("titulo", "Respuestas:");
                asignar("tableDatos", "<tr><th>ID</th><th>Id Pregunta</th><th>Texto</th></tr>");

                if (json.datos === "vacio") {
                    funcionQueCreaLaFilaRespuestaVacio("tableDatos");
                    nopaginar(json.enlaces);
                } else {
                    for (var i in json.datos) {
                        //crea la fila y lo añade al final de la tabla
                        funcionQueCreaLaFilaRespuesta(json.datos[i], "tableDatos", json.isAdministrador);
                    }
                    nopaginar();
                }
                agregar("tableDatos", "<tr><td></td><td><input id='idpreguntaRespuesta' type='text' value='" +
                        json.idPregunta + "' disabled=''></td>" +
                        "<td><input type='text' id='textoRespuesta' onblur='blurRespuesta()'></td></tr>");
                creaBotonAtras(json.idPregunta);
            }
        } else {
            //hay algun error
        }
    }
};

function funcionQueCreaLaFila(datos, nombreTabla) {
    var tabla = document.getElementById(nombreTabla);

    var fila = document.createElement("tr");
    var celda = document.createElement("td");
    celda.textContent = (datos.email);
    fila.appendChild(celda);
    celda = document.createElement("td");
    celda.textContent = (datos.nombre);
    fila.appendChild(celda);
    celda = document.createElement("td");
    celda.textContent = (datos.rol);
    fila.appendChild(celda);
    celda = document.createElement("td");
    if (datos.activo) {
        celda.textContent = ("Activo");
    } else {
        celda.textContent = ("No Activo");
    }
    fila.appendChild(celda);

    celda = document.createElement("td");
    var enlacecelda = document.createElement("a");
    enlacecelda.textContent = "Borrar";
    enlacecelda.setAttribute("href", "#");
    celda.appendChild(enlacecelda);
    fila.appendChild(celda);
    celda = document.createElement("td");
    enlacecelda = document.createElement("a");
    enlacecelda.textContent = "Editar";
    enlacecelda.setAttribute("href", "#");
    celda.appendChild(enlacecelda);
    fila.appendChild(celda);
    tabla.appendChild(fila);

}
function funcionQueCreaLaFilaEncuesta(datos, nombreTabla, controlAdministrador, emailSesion) {
    var tabla = document.getElementById(nombreTabla);

    var fila = document.createElement("tr");
    var celda = document.createElement("td");
    celda.textContent = (datos.id);
    celda.setAttribute("id", "id-" + datos.id);
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = (datos.email);
    celda.setAttribute("id", "email-" + datos.id);
    fila.appendChild(celda);

    celda = document.createElement("td");
    var enlace = document.createElement("a");
    enlace.textContent = (datos.titulo);
    enlace.setAttribute("id", "titulo-" + datos.id);
    enlace.setAttribute("href", "#");
    enlace.setAttribute("onClick", "clickVerPregunta('" + datos.id + "')");
    celda.appendChild(enlace);
    fila.appendChild(celda);

    if (controlAdministrador || emailSesion === datos.email) {
        //si es administrador puede borrar las encuestas
        celda = document.createElement("td");
        var enlace = document.createElement("a");
        enlace.textContent = ("Borrar");
        enlace.setAttribute("href", "#");
        enlace.setAttribute("onClick", "clickBorrarEncuesta('" + datos.id + "')");
        celda.appendChild(enlace);
        fila.appendChild(celda);

        celda = document.createElement("td");
        var enlace = document.createElement("a");
        enlace.textContent = ("Editar");
        enlace.setAttribute("href", "#");
        enlace.setAttribute("onClick", "clickEditarEncuesta('" + datos.id + "')");
        celda.appendChild(enlace);
        fila.appendChild(celda);
    }

    tabla.appendChild(fila);
}
function funcionQueCreaLaFilaPregunta(datos, nombreTabla, controlAdministrador) {
    var tabla = document.getElementById(nombreTabla);

    var fila = document.createElement("tr");
    var celda = document.createElement("td");
    celda.textContent = (datos.id);
    celda.setAttribute("id", "idPregunta-" + datos.id);
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = (datos.idencuesta);
    celda.setAttribute("id", "idEncuesta-" + datos.id);
    fila.appendChild(celda);

    celda = document.createElement("td");
    var enlace = document.createElement("a");
    enlace.textContent = (datos.texto);
    enlace.setAttribute("id", "texto-" + datos.id);
    enlace.setAttribute("href", "#");
    enlace.setAttribute("onClick", "clickVerRespuestas('" + datos.id + "')");
    celda.appendChild(enlace);
    fila.appendChild(celda);

    if (controlAdministrador) {
        //si es administrador puede borrar las preguntas
        celda = document.createElement("td");
        var enlace = document.createElement("a");
        enlace.textContent = ("Borrar");
        enlace.setAttribute("href", "#");
        enlace.setAttribute("onClick", "clickBorrarPregunta('" + datos.id + "')");
        celda.appendChild(enlace);
        fila.appendChild(celda);

        celda = document.createElement("td");
        var enlace = document.createElement("a");
        enlace.textContent = ("Editar");
        enlace.setAttribute("href", "#");
        enlace.setAttribute("onClick", "clickEditarPregunta('" + datos.id + "')");
        celda.appendChild(enlace);
        fila.appendChild(celda);
    }

    tabla.appendChild(fila);
}
function funcionQueCreaLaFilaPreguntaVacio(nombreTabla) {
    var tabla = document.getElementById(nombreTabla);

    var fila = document.createElement("tr");
    var celda = document.createElement("td");
    celda.textContent = ("-");
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = ("-");
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = ("Aun no hay preguntas para esta encuesta");
    fila.appendChild(celda);

    tabla.appendChild(fila);
}
function funcionQueCreaLaFilaRespuesta(datos, nombreTabla, controlAdministrador) {
    var tabla = document.getElementById(nombreTabla);

    var fila = document.createElement("tr");
    var celda = document.createElement("td");
    celda.textContent = (datos.id);
    celda.setAttribute("id", "idRespuesta-" + datos.id);
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = (datos.idpregunta);
    celda.setAttribute("id", "idPregunta-" + datos.id);
    fila.appendChild(celda);

    celda = document.createElement("td");
    var enlace = document.createElement("span");
    enlace.textContent = (datos.texto);
    enlace.setAttribute("id", "texto-" + datos.id);
    celda.appendChild(enlace);
    fila.appendChild(celda);

    if (controlAdministrador) {
        //si es administrador puede borrar las encuestas
        celda = document.createElement("td");
        var enlace = document.createElement("a");
        enlace.textContent = ("Borrar");
        enlace.setAttribute("href", "#");
        enlace.setAttribute("onClick", "clickBorrarRespuesta('" + datos.id + "')");
        celda.appendChild(enlace);
        fila.appendChild(celda);

        celda = document.createElement("td");
        var enlace = document.createElement("a");
        enlace.textContent = ("Editar");
        enlace.setAttribute("href", "#");
        enlace.setAttribute("onClick", "clickEditarRespuesta('" + datos.id + "')");
        celda.appendChild(enlace);
        fila.appendChild(celda);
    }

    tabla.appendChild(fila);
}
function funcionQueCreaLaFilaRespuestaVacio(nombreTabla) {
    var tabla = document.getElementById(nombreTabla);

    var fila = document.createElement("tr");
    var celda = document.createElement("td");
    celda.textContent = ("-");
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = ("-");
    fila.appendChild(celda);

    celda = document.createElement("td");
    celda.textContent = ("Aun no hay respuestas");
    fila.appendChild(celda);

    tabla.appendChild(fila);
}

function comprobarSesion() {
    ajaxGET("./llamadas/ajaxSesion.php", respuestaAjax);
}
function logOut() {
    ajaxGET("./llamadas/ajaxLogOut.php", respuestaAjaxLogOut);
}
function verTodasEncuestas() {
    ajaxGET("./llamadas/ajaxVerTodasEncuestas.php?", respuestaAjaxVerTodasEncuestas);
}
function verSoloMiasEncuestas() {
    ajaxGET("./llamadas/ajaxVerSoloMiasEncuestas.php", respuestaAjaxVerSoloMiasEncuestas);
}
function agregarEncuesta(titulo, email, bandera) {
    if (bandera === "todas") {
        ajaxGET("./llamadas/ajaxAgregarEncuesta.php?email=" + email + "&titulo=" + titulo + "", respuestaAjaxVerTodasEncuestas);
    } else if (bandera === "mias") {
        ajaxGET("./llamadas/ajaxAgregarEncuesta.php?email=" + email + "&titulo=" + titulo + "&mias=mias", respuestaAjaxVerSoloMiasEncuestas);
        //ajaxGET("./llamadas/ajaxVerSoloMiasEncuestas.php", respuestaAjaxVerSoloMiasEncuestas);
    }
}
function agregarPregunta(idencuesta, texto) {
    ajaxGET("./llamadas/ajaxAgregarPregunta.php?idencuesta=" + idencuesta + "&texto=" + texto + "", respuestaAjaxVerPreguntas);
    //ajaxGET("./llamadas/ajaxAgregarEncuesta.php?email='"+email+"'&titulo='"+titulo+"'", respuestaAjaxVerTodasEncuestas);
}
function agregarRespuesta(idpregunta, texto) {
    ajaxGET("./llamadas/ajaxAgregarRespuesta.php?idpregunta=" + idpregunta + "&texto=" + texto + "", respuestaAjaxVerRespuestas);
    //ajaxGET("./llamadas/ajaxAgregarEncuesta.php?email='"+email+"'&titulo='"+titulo+"'", respuestaAjaxVerTodasEncuestas);
}
function blurTitulo() {
    var titulo = document.getElementById("tituloEncuesta").value;
    var email = document.getElementById("emailEncuesta").value;
    var banderaTodas = document.getElementById("banderaTodas");
    if (titulo !== "") {
        if (banderaTodas) {
            agregarEncuesta(titulo, email, "mias");
        } else {
            agregarEncuesta(titulo, email, "todas");
        }
    }
}
function clickVerPregunta(idencuesta) {
    ajaxGET("./llamadas/ajaxVerPregunta.php?idEncuesta=" + idencuesta, respuestaAjaxVerPreguntas);
}
function clickBorrarEncuesta(idencuesta) {
    var banderaTodas = document.getElementById("banderaTodas");
    if (banderaTodas) {
        ajaxGET("./llamadas/ajaxBorrarEncuesta.php?idEncuesta=" + idencuesta + "&mias=\"mias\"", respuestaAjaxVerSoloMiasEncuestas);
    } else {
        ajaxGET("./llamadas/ajaxBorrarEncuesta.php?idEncuesta=" + idencuesta, respuestaAjaxVerTodasEncuestas);
    }
}
function clickBorrarPregunta(idpregunta) {
    ajaxGET("./llamadas/ajaxBorrarPregunta.php?idPregunta=" + idpregunta, respuestaAjaxVerPreguntas);
}
function clickBorrarRespuesta(idRespuesta) {
    ajaxGET("./llamadas/ajaxBorrarRespuesta.php?idRespuesta=" + idRespuesta, respuestaAjaxVerRespuestas);
}
function clickVerRespuestas(idpregunta) {
    ajaxGET("./llamadas/ajaxVerRespuesta.php?idPregunta=" + idpregunta, respuestaAjaxVerRespuestas);
}
function clickEditarEncuesta(idEncuesta) {
    var elemento = document.getElementById("titulo-" + idEncuesta);
    var elementovalue = elemento.innerText;

    var elementonuevo = document.createElement("input");
    elementonuevo.setAttribute("value", elementovalue);
    elementonuevo.setAttribute("id", "editarTituloEncuesta-" + idEncuesta);
    elementonuevo.setAttribute("type", "text");

    elemento.parentNode.appendChild(elementonuevo);
    elemento.parentNode.removeChild(elemento);

    elementonuevo = document.getElementById("editarTituloEncuesta-" + idEncuesta);
    elementonuevo.addEventListener("blur", function () {
        var valornuevo = document.getElementById("editarTituloEncuesta-" + idEncuesta).value;
        var banderaTodas = document.getElementById("banderaTodas");
        if (!banderaTodas) {
            ajaxGET("./llamadas/ajaxEditarEncuesta.php?idEncuesta=" + idEncuesta + "&tituloNuevo=" + valornuevo, respuestaAjaxVerTodasEncuestas);
        } else {
            ajaxGET("./llamadas/ajaxEditarEncuesta.php?idEncuesta=" + idEncuesta + "&tituloNuevo=" + valornuevo + "&mias=mias", respuestaAjaxVerSoloMiasEncuestas);
        }
    });
}
function clickEditarPregunta(idPregunta) {
    var elemento = document.getElementById("texto-" + idPregunta);
    var elementovalue = elemento.innerText;

    var elementonuevo = document.createElement("input");
    elementonuevo.setAttribute("value", elementovalue);
    elementonuevo.setAttribute("id", "editarTextoPregunta-" + idPregunta);
    elementonuevo.setAttribute("type", "text");

    elemento.parentNode.appendChild(elementonuevo);
    elemento.parentNode.removeChild(elemento);

    elementonuevo = document.getElementById("editarTextoPregunta-" + idPregunta);
    elementonuevo.addEventListener("blur", function () {
        var valornuevo = document.getElementById("editarTextoPregunta-" + idPregunta).value;
        ajaxGET("./llamadas/ajaxEditarPregunta.php?idPregunta=" + idPregunta + "&textoNuevo=" + valornuevo, respuestaAjaxVerPreguntas);
    });
}
function clickEditarRespuesta(idRespuesta) {
    var elemento = document.getElementById("texto-" + idRespuesta);
    var elementovalue = elemento.innerText;

    var elementonuevo = document.createElement("input");
    elementonuevo.setAttribute("value", elementovalue);
    elementonuevo.setAttribute("id", "editarTextoRespuesta-" + idRespuesta);
    elementonuevo.setAttribute("type", "text");

    elemento.parentNode.appendChild(elementonuevo);
    elemento.parentNode.removeChild(elemento);

    elementonuevo = document.getElementById("editarTextoRespuesta-" + idRespuesta);
    elementonuevo.addEventListener("blur", function () {
        var valornuevo = document.getElementById("editarTextoRespuesta-" + idRespuesta).value;
        ajaxGET("./llamadas/ajaxEditarRespuesta.php?idRespuesta=" + idRespuesta + "&textoNuevo=" + valornuevo, respuestaAjaxVerRespuestas);
    });
}
function blurPregunta() {
    var texto = document.getElementById("textoPregunta").value;
    var idencuesta = document.getElementById("idencuestaPregunta").value;
    if (texto !== "") {
        agregarPregunta(idencuesta, texto);
    }
}
function blurRespuesta() {
    var texto = document.getElementById("textoRespuesta").value;
    var idpregunta = document.getElementById("idpreguntaRespuesta").value;
    if (texto !== "") {
        agregarRespuesta(idpregunta, texto);
    }
}
//inicio
(function () {
    //comprobar si existe una sesion activa al iniciar
    comprobarSesion();
    //manejadores de eventos logOut
    document.getElementById("aLogOut").addEventListener("click", logOut);
    //manejador eventos submit para hacer login
    var sendButton = document.getElementById("submitLogin");
    sendButton.addEventListener("click", function () {
        var mail = document.getElementById("inputEmail").value;
        var clave = document.getElementById("inputClave").value;
        ajaxGET("./llamadas/ajaxLogin.php?email=" + mail + "&clave=" + clave, respuestaAjaxLogin);
    });
    //manejador de evento para volver a ver usuarios
    document.getElementById("verHome").addEventListener("click", comprobarSesion);
    //manejador de evento para ver encuestas
    document.getElementById("verTodasEncuestas").addEventListener("click", verTodasEncuestas);
    //manejador de evento para ver solo las encuestas del usuario actual
    document.getElementById("verSoloMiasEncuestas").addEventListener("click", verSoloMiasEncuestas);
}
)();


