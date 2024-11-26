<?php
//routes\api.php
require_once 'app/controllers/AuthController.php';
require_once 'app/controllers/UserController.php';
require_once 'app/middlewares/AuthMiddleware.php';
require_once 'app/controllers/sesionController.php';
require_once 'app/controllers/OrdenSesionController.php';
require_once 'app/controllers/SolicitudController.php';
require_once 'app/controllers/SolicitantesController.php';
require_once 'app/controllers/InvitadosController.php';
require_once 'app/controllers/MiembrosController.php';
require_once 'app/controllers/ActasController.php';
require_once 'app/controllers/AsistenciaMiembrosController.php';
require_once 'app/controllers/AsistenciaInvitadosController.php';
require_once 'app/controllers/DescripcionesController.php';
require_once 'app/controllers/TareasController.php';
require_once 'app/controllers/EncargadosTareasController.php';
require_once 'app/controllers/ProposicionesController.php';
require_once 'core/Router.php';

$router = new Router();
// Rutas públicas (sin autenticación)
$router->add('POST','/login', 'AuthController@login',true);
$router->add('POST','/logout', 'AuthController@logout');
$router->add('POST','/register', 'AuthController@register',true);
$router->add('PUT','/CambiarPasswordUsuario', 'AuthController@CambiarPasswordUsuario');

$router->add('POST','/InsertarTema', 'OrdenSesionController@InsertarTema');
$router->add('POST','/InsertarSesion', 'sesionController@InsertarSesion');
$router->add('POST','/InsertarActa', 'ActasController@InsertarActa');
$router->add('POST','/InsertarSolicitud', 'SolicitudController@InsertarSolicitud');
$router->add('POST','/InsertarTarea', 'TareasController@InsertarTarea');
$router->add('POST','/InsertarActa', 'ActasController@InsertarActa');
$router->add('POST','/AsignarResponsable', 'EncargadosTareasController@AsignarResponsable');
$router->add('POST','/RegistrarInvitado', 'AuthController@RegistrarInvitado');
$router->add('POST','/RegistrarMiembro', 'AuthController@RegistrarMiembro');
$router->add('POST','/RegistrarSolicitante', 'AuthController@RegistrarSolicitante',true);
$router->add('POST','/cargarMiembros', 'AsistenciaMiembrosController@cargarMiembros');
$router->add('POST','/AgregarInvitado', 'AsistenciaInvitadosController@AgregarInvitado');

$router->add('POST','/crearProposicion', 'ProposicionesController@crearProposicion',true);

$router->add('PUT','/AprobarActa', 'ActasController@AprobarActa');
$router->add('PUT','/ActualizarTema', 'OrdenSesionController@ActualizarTema');
$router->add('PUT','/ActualizarSolicitud', 'SolicitudController@ActualizarSolicitud');
$router->add('PUT','/ResponderSolicitud', 'SolicitudController@ResponderSolicitud');
$router->add('PUT','/cambiarEstadoTarea', 'EncargadosTareasController@cambiarEstadoTarea');
$router->add('PUT','/ActualizarTarea', 'TareasController@ActualizarTarea');
$router->add('PUT','/actualizarAsistenciaMiembro', 'AsistenciaMiembrosController@actualizarAsistenciaMiembro');
$router->add('PUT','/actualizarAsistenciaInvitados', 'AsistenciaInvitadosController@actualizarAsistenciaInvitados');

$router->add('DELETE','/EliminarTema/{id}', 'OrdenSesionController@EliminarTema');
$router->add('DELETE','/EliminarSolicitud/{id}', 'SolicitudController@EliminarSolicitud');
$router->add('DELETE','/DesasignarResponsable/{IdMiembros}/{IdTareas}', 'EncargadosTareasController@DesasignarResponsable');
$router->add('DELETE','/EliminarTarea/{id}', 'TareasController@EliminarTarea');
$router->add('DELETE','/EliminarAsistenciaInvitado/{sesionId}/{invitadoId}', 'AsistenciaInvitadosController@EliminarAsistenciaInvitado');

$router->add('GET','/MotrarTareasIDSesion/{id}', 'TareasController@MotrarTareasIDSesion');
$router->add('GET','/MotrarTareas', 'TareasController@MotrarTareas');
$router->add('GET','/MostrarMiembros', 'MiembrosController@MostrarMiembros');
$router->add('GET','/MiembrosAsignadosTarea/{id}', 'MiembrosController@MiembrosAsignadosTarea');
$router->add('GET','/MostrarDescripciones', 'DescripcionesController@MostrarDescripciones');
$router->add('GET','/mostrarSesiones', 'sesionController@mostrarSesiones');
$router->add('GET','/mostrarSolicitantesConSolicitudes', 'SolicitantesController@mostrarSolicitantesConSolicitudes');
$router->add('GET','/mostrarSesionesSolicitante/{id}', 'sesionController@mostrarSesionesSolicitante');
$router->add('GET','/mostrarSesioneIDSesion/{id}', 'sesionController@mostrarSesioneIDSesion');
$router->add('GET','/MostrarSesionSelect', 'sesionController@MostrarSesionSelect');
$router->add('GET','/proponerFecha', 'sesionController@proponerFecha');
$router->add('GET','/TemasSesion/{id}', 'OrdenSesionController@TemasSesion');
$router->add('GET','/datosOrdenSesion/{tema}', 'OrdenSesionController@datosOrdenSesion');
$router->add('GET','/MostrarSolicitantes', 'SolicitantesController@MostrarSolicitantes');
$router->add('GET','/SolicitanteIDsolicitud/{id}', 'SolicitantesController@SolicitanteIDsolicitud');
$router->add('GET','/buscarSolicitante/{id}', 'SolicitantesController@buscarSolicitante');
$router->add('GET','/mostrarSolicitantesPorTipo/{id}', 'SolicitantesController@mostrarSolicitantesPorTipo');
$router->add('GET','/SolicitudesSesion/{id}', 'SolicitudController@SolicitudesSesion');
$router->add('GET','/solicitudesSesionSolicitante/{idSesion}/{idsolicitante}', 'SolicitudController@solicitudesSesionSolicitante');
$router->add('GET','/SolicitudSelecionada/{id}', 'SolicitudController@SolicitudSelecionada');
$router->add('GET','/UltimaSesion', 'sesionController@UltimaSesion');
$router->add('GET','/MostrarActasFirmadas', 'ActasController@MostrarActasFirmadas');
$router->add('GET','/ActaAnterior/{id}', 'ActasController@ActaAnterior');
$router->add('GET','/MotrarTareasSesioAnterior/{id}', 'EncargadosTareasController@MotrarTareasSesioAnterior');
$router->add('GET','/asistenciaMiembroSesion/{id}', 'AsistenciaMiembrosController@asistenciaMiembroSesion');
$router->add('GET','/Todoscitados/{id}', 'AsistenciaMiembrosController@Todoscitados');
$router->add('GET','/MostrarInvitados', 'InvitadosController@MostrarInvitados');
$router->add('GET','/AsistenciaInvitadosSesion/{id}', 'AsistenciaInvitadosController@AsistenciaInvitadosSesion');
$router->add('GET','/obtenerInvitadosNoAsistentes/{id}', 'AsistenciaInvitadosController@obtenerInvitadosNoAsistentes');
$router->add('GET','/DetallesUsuarioInvitado', 'InvitadosController@DetallesUsuarioInvitado');
$router->add('GET','/DetallesUsuarioMiembro', 'MiembrosController@DetallesUsuarioMiembro');
$router->add('GET','/DetallesUsuarioSolicitante', 'SolicitantesController@DetallesUsuarioSolicitante');
$router->add('GET','/getIDolicitante', 'SolicitantesController@getIDolicitante');
$router->add('GET','/getIMiembro', 'MiembrosController@getIMiembro');
//proposicioones

$router->add('GET','/listarProposiciones',  'ProposicionesController@listarProposiciones');
$router->add('GET','/listarProposicionesSesion/{id}',  'ProposicionesController@listarProposicionesSesion');
$router->add('GET','/IDMiembro',  'AuthController@IDMiembro');
// filtros 
$router->add('GET','/BuscarActas/{id}', 'ActasController@BuscarActas');
$router->add('GET','/BuscarActasFecha/{Year}/{Month}', 'ActasController@BuscarActasFecha');
$router->add('GET','/BuscarActaPorYear/{Year}', 'ActasController@BuscarActaPorYear');
$router->add('GET','/BuscarActasTemaFecha/{Year}/{Month}/{Tema}', 'ActasController@BuscarActasTemaFecha');


return $router;
