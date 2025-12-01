<?php

declare(strict_types=1);

return [
    'filesystem' => [
        'path_required' => 'Proporciona una ruta para continuar.',
        'not_directory' => 'La ruta ":path" no es un directorio.',
        'permission_denied' => 'No tienes permiso para acceder a ":path".',
        'read_failed' => 'No se pudo leer el contenido del directorio.',
        'list_summary' => 'Se encontraron :count de :total elementos en "**:path**"',
        'list_failed' => 'No se pudo listar el contenido del directorio.',
        'not_found' => 'No se encontró el archivo o directorio ":path".',
        'info_retrieved' => 'Información obtenida para ":name".',
        'info_failed' => 'No se pudo obtener la información del archivo.',
        'pattern_required' => 'Proporciona un patrón de búsqueda.',
        'search_summary' => 'Se encontraron :count resultados para "**:**pattern**" en "**:**path**"',
        'search_failed' => 'La operación de búsqueda falló.',
    ],
    'user_profile' => [
        'fields' => [
            'user_name' => 'nombre',
            'user_description' => 'descripción',
            'response_detail' => 'nivel de detalle',
            'response_tone' => 'tono de respuesta',
            'timezone' => 'zona horaria',
        ],
        'profile_complete' => 'Aquí tienes la información de tu perfil, :name.',
        'profile_incomplete' => 'Faltan datos en tu perfil: :fields. ¿Quieres proporcionarlos?',
        'profile_updated' => 'Perfil actualizado correctamente: :fields.',
        'errors' => [
            'get_failed' => 'No se pudo obtener la información del perfil.',
            'update_failed' => 'No se pudo actualizar el perfil.',
            'no_updates' => 'No se proporcionó información para actualizar.',
            'validation_failed' => 'Algunas actualizaciones fallaron: :errors',
            'name_too_long' => 'El nombre debe tener 100 caracteres o menos.',
            'description_too_long' => 'La descripción debe tener 1000 caracteres o menos.',
            'invalid_detail' => 'Nivel de detalle inválido. Opciones válidas: :valid',
            'invalid_tone' => 'Tono de respuesta inválido. Opciones válidas: :valid',
            'invalid_timezone' => 'Zona horaria inválida. Usa el formato IANA (ej., America/New_York).',
        ],
    ],
];
