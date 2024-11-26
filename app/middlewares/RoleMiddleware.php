<?php

class RoleMiddleware
{
    private $rolePermissions = [
        'coordinador' => [
            'GET' => [
                '/AsistenciaInvitadosSesion/{id}',
                '/asistenciaMiembroSesion/{id}',
                '/MotrarTareasIDSesion/{id}',
                '/MotrarTareas',
                '/MotrarTareasSesioAnterior/{id}',
                '/MostrarMiembros',
                '/MiembrosAsignadosTarea/{id}',
                '/Todoscitados/{id}',
                '/MostrarDescripciones',
                '/MostrarInvitados',
                '/mostrarSesiones',
                '/mostrarSesioneIDSesion/{id}',
                '/MostrarSesionSelect',
                '/proponerFecha',
                '/TemasSesion/{id}',
                '/datosOrdenSesion/{tema}',
                '/MostrarSolicitantes',
                '/mostrarSesionesSolicitante/{id}',
                '/mostrarSolicitantesConSolicitudes',
                'mostrarSolicitantesPorTipo/{id}',
                '/solicitudesSesionSolicitante/{idSesion}/{idsolicitante}', 
                '/buscarSolicitante/{id}',
                '/SolicitanteIDsolicitud/{id}',
                '/SolicitudesSesion/{id}',
                '/SolicitudSelecionada/{id}',
                '/obtenerInvitadosNoAsistentes/{id}',
                '/UltimaSesion',

                '/MostrarActasFirmadas',
                '/ActaAnterior/{id}',

                '/BuscarActas/{id}',
                '/BuscarActaPorYear/{Year}',
                '/BuscarActasFecha/{Year}/{Month}',
                '/BuscarActasTemaFecha/{Year}/{Month}/{Tema}'
            ],
            'POST' => [
                '/login',
                '/register',
                '/InsertarTema',
                '/InsertarSesion',
                '/InsertarActa',
                '/InsertarSolicitud',
                '/InsertarTarea',
                '/InsertarActa',
                '/AsignarResponsable',
                '/RegistrarInvitado',
                '/RegistrarMiembro',
                '/AgregarInvitado',
                '/cargarMiembros',
                '/logout'
            ],
            'PUT' => [

                '/AprobarActa',
                '/ActualizarTema',
                '/ActualizarSolicitud',
                '/ResponderSolicitud',
                '/cambiarEstadoTarea',
                '/ActualizarTarea',

                '/actualizarAsistenciaMiembro',
                'actualizarAsistenciaInvitados'
            ],
            'DELETE' => [
                '/EliminarTema/{id}',
                '/EliminarSolicitud/{id}',
                '/DesasignarResponsable/{IdMiembros}/{IdTareas}',
                '/EliminarTarea/{id}',
                '/EliminarAsistenciaInvitado/{sesionId}/{invitadoId}'
            ],
        ],
        'invitado' => [
            'GET' => [
                '/DetallesUsuarioInvitado',
                
            ],
            'PUT' => [
                '/CambiarPasswordUsuario',
            ],

            'POST' => [
                '/logout'
            ]
        ],
        'miembro' => [
            'GET' => [
                '/DetallesUsuarioMiembro',
                '/getIMiembro',
                '/listarProposicionesSesion/{id}',
            ],
            'PUT' => [
                '/CambiarPasswordUsuario',
            ],

            'POST' => [
                '/logout',
               '/crearProposicion'
            ]
        ],
        'solicitante' => [
            'GET' => [
                '/DetallesUsuarioSolicitante',
                '/getIDolicitante',
                '/MostrarDescripciones',
                '/MostrarSesionSelect',
                '/mostrarSesionesSolicitante/{id}',
                '/solicitudesSesionSolicitante/{idSesion}/{idsolicitante}', 
            ],
            'PUT' => [
                '/CambiarPasswordUsuario',
            ],

            'POST' => [
                '/logout',   
                '/InsertarSolicitud'         
            ]
        ],

    ];


    public function handle2($role, $method, $uri)
    {
        if (!isset($this->rolePermissions[$role])) {
            http_response_code(403);
            echo json_encode(['message' => 'Rol no autorizado']);
            exit;
        }

        $allowedRoutes = $this->rolePermissions[$role][strtoupper($method)] ?? [];
        $isAuthorized = false;

        foreach ($allowedRoutes as $routePattern) {
            // Convertir patrones como `/users/{id}` a expresiones regulares
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '[a-zA-Z0-9_]+', $routePattern);
            $pattern = "#^" . trim($pattern, '/') . "$#";

            if (preg_match($pattern, trim($uri, '/'))) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized) {
            http_response_code(403);
            echo json_encode(['message' => 'No tienes permiso para realizar esta acción']);
            exit;
        }
    }

    public function handle($role, $method, $uri)
    {
        if (!isset($this->rolePermissions[$role])) {
            http_response_code(403);
            echo json_encode(['message' => 'Rol no autorizado']);
            exit;
        }

        $allowedRoutes = $this->rolePermissions[$role][strtoupper($method)] ?? [];
        $isAuthorized = false;

        foreach ($allowedRoutes as $routePattern) {
            // Convertir patrones como `/users/{id}` a expresiones regulares
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '[a-zA-Z0-9_]+', $routePattern);
            $pattern = "#^" . trim($pattern, '/') . "$#";

            if (preg_match($pattern, trim($uri, '/'))) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized) {
            http_response_code(403);
            echo json_encode(['message' => 'No tienes permiso para realizar esta acción']);
            exit;
        }
    }
}
