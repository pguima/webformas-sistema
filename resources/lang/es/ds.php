<?php

$base = require __DIR__ . '/../en/ds.php';

return array_replace_recursive($base, [
    'title' => 'Sistema de Diseño',
    'brand' => 'Sistema',
    'actions' => [
        'close' => 'Cerrar',
        'back' => 'Volver',
        'dark' => 'Oscuro',
        'light' => 'Claro',
    ],
    'navbar' => [
        'search_placeholder' => 'Buscar',
        'notifications' => 'Notificaciones',
        'my_profile' => 'Mi Perfil',
        'logout' => 'Salir',
        'language' => 'Idioma',
        'locales' => [
            'pt_BR' => 'Português',
            'en' => 'English',
            'es' => 'Español',
        ],
        'view_all' => 'Ver todo',
    ],
    'sidebar' => [
        'title' => 'Menú',
        'groups' => [
            'application' => 'Aplicación',
            'ui_elements' => 'Componentes',
        ],
        'items' => [
            'kanban' => 'Kanban',
        ],
    ],

    'pages' => [
        'kanban' => [
            'title' => 'Kanban',
            'subtitle' => 'Componentes / Kanban',
            'sections' => [
                'board_title' => 'Tablero',
                'board_description' => 'Arrastra y suelta tarjetas entre columnas.',
            ],
            'columns' => [
                'todo' => 'Por hacer',
                'doing' => 'En progreso',
                'done' => 'Hecho',
            ],
            'tasks' => [
                't1' => ['title' => 'Revisar design tokens', 'tag' => 'UI'],
                't2' => ['title' => 'Implementar estados del sidebar', 'tag' => 'Arquitectura'],
                't3' => ['title' => 'Auditar claves de i18n', 'tag' => 'i18n'],
                't4' => ['title' => 'Construir columna Kanban', 'tag' => 'Componente'],
                't5' => ['title' => 'Refinar feedback de drag', 'tag' => 'UX'],
                't6' => ['title' => 'Publicar documentación', 'tag' => 'Docs'],
            ],
            'labels' => [
                'due' => 'Vencimiento',
                'assigned' => 'Asignado',
            ],
            'actions' => [
                'add_task' => 'Agregar tarea',
            ],
            'hints' => [
                'drag_hint' => 'Consejo: haz clic y arrastra una tarjeta para reordenarla en la misma columna o moverla a otra columna.',
            ],
            'docs' => [
                'title' => 'Documentación',
                'links_component' => [
                    'title' => 'Componente de Link',
                    'subtitle' => 'Usa <x-ds::link> para mantener estilos consistentes de links en todas las páginas del DS.',
                    'example_code_title' => 'Ejemplo de código',
                    'props_title' => 'Props',
                    'props' => [
                        'href' => 'URL del link.',
                        'variant' => 'primary, secondary, muted, danger, ghost.',
                        'size' => 'sm, md, lg.',
                        'icon' => 'Nombre del ícono (Iconify) (opcional).',
                        'icon_position' => 'left o right.',
                        'external' => 'Si es true, abre en nueva pestaña y muestra flecha externa.',
                        'underline' => 'none, hover, always.',
                        'disabled' => 'Si es true, deshabilita interacciones y reduce opacidad.',
                    ],
                ],
            ],
        ],
    ],
]);
