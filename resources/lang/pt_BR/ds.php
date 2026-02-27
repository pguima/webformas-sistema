<?php

$base = require __DIR__ . '/../en/ds.php';

return array_replace_recursive($base, [
    'title' => 'Sistema de Design',
    'brand' => 'Sistema',
    'actions' => [
        'close' => 'Fechar',
        'back' => 'Voltar',
        'dark' => 'Escuro',
        'light' => 'Claro',
    ],
    'navbar' => [
        'search_placeholder' => 'Pesquisar',
        'notifications' => 'Notificações',
        'my_profile' => 'Meu Perfil',
        'logout' => 'Sair',
        'language' => 'Idioma',
        'locales' => [
            'pt_BR' => 'Português',
            'en' => 'English',
            'es' => 'Español',
        ],
        'view_all' => 'Ver todas',
    ],
    'sidebar' => [
        'title' => 'Menu',
        'groups' => [
            'application' => 'Aplicação',
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
                'board_title' => 'Quadro',
                'board_description' => 'Arraste e solte cards entre colunas.',
            ],
            'columns' => [
                'todo' => 'A fazer',
                'doing' => 'Em andamento',
                'done' => 'Concluído',
            ],
            'tasks' => [
                't1' => ['title' => 'Revisar design tokens', 'tag' => 'UI'],
                't2' => ['title' => 'Implementar estados da sidebar', 'tag' => 'Arquitetura'],
                't3' => ['title' => 'Auditar chaves de i18n', 'tag' => 'i18n'],
                't4' => ['title' => 'Construir coluna do Kanban', 'tag' => 'Componente'],
                't5' => ['title' => 'Refinar feedback do drag', 'tag' => 'UX'],
                't6' => ['title' => 'Publicar documentação', 'tag' => 'Docs'],
            ],
            'labels' => [
                'due' => 'Prazo',
                'assigned' => 'Responsável',
            ],
            'actions' => [
                'add_task' => 'Adicionar tarefa',
            ],
            'hints' => [
                'drag_hint' => 'Dica: clique e arraste um card para reordenar na mesma coluna ou mover para outra coluna.',
            ],
            'docs' => [
                'title' => 'Documentação',
                'links_component' => [
                    'title' => 'Componente de Link',
                    'subtitle' => 'Use <x-ds::link> para manter estilos consistentes de links em todas as páginas do DS.',
                    'example_code_title' => 'Exemplo de código',
                    'props_title' => 'Props',
                    'props' => [
                        'href' => 'URL do link.',
                        'variant' => 'primary, secondary, muted, danger, ghost.',
                        'size' => 'sm, md, lg.',
                        'icon' => 'Nome do ícone (Iconify) (opcional).',
                        'icon_position' => 'left ou right.',
                        'external' => 'Se true, abre em nova aba e mostra seta externa.',
                        'underline' => 'none, hover, always.',
                        'disabled' => 'Se true, desabilita interações e reduz opacidade.',
                    ],
                ],
            ],
        ],
    ],
]);
