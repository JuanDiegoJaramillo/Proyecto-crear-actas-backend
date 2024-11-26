<?php
return [
    'Coordinador' => [
        'GET' => [
            '/mostrarSesiones',
            '/proponerFecha',
            '/TemasSesion/{id}',
            '/datosOrdenSesion/{tema}',
            '/UltimaSesion',
            '/MostrarActasFirmadas',
            '/ActaAnterior/{id}',
            '/MostrarMiembros',
            '/MostrarInvitados',
            '/MostrarDescripciones',
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
