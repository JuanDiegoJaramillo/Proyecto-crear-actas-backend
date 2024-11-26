<?php
return [
    'coordinador' => [
        'GET' => [
            '/mostrarSesiones',
            '/mostrarSesioneIDSesion/{id}',
            '/proponerFecha',
            '/TemasSesion/{id}',
            '/datosOrdenSesion/{tema}',
            '/UltimaSesion',
            '/MostrarActasFirmadas',
            '/ActaAnterior/{id}',
            '/MostrarMiembros',
            '/MostrarInvitados',
            '/MostrarDescripciones',
            '/MostrarSesionSelect'
        ],
        'POST' => [
            '/InsertarSesion',
            '/InsertarActa',
            '/InsertarTema',
            '/AgregarInvitado',
            '/cargarMiembros',
        ],
        'PUT' => [
            '/ActualizarTema',
            '/actualizarAsistenciaMiembro',
            '/actualizarAsistenciaInvitados',
        ],
        'DELETE' => [
            '/EliminarTema/{id}',
            '/EliminarAsistenciaInvitado/{sesionId}/{invitadoId}',
        ],
    ],
];
