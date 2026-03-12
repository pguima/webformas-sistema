<?php

return [
    'common' => [
        'dash' => '—',
    ],

    'sidebar' => [
        'dashboard' => 'Panel',
        'clients' => 'Clientes',
        'services' => 'Servicios',
        'plans' => 'Planes',
        'employees' => 'Empleados',
        'folders' => 'Carpetas',
        'files' => 'Archivos',
        'management' => 'Gestión',
        'users' => 'Usuarios',
        'widgets' => 'Widgets',
        'leads' => 'Leads',
        'contacts' => 'Contactos',
        'api_tokens' => 'Tokens API',
        'profile' => 'Perfil',
        'settings' => 'Configuración',
        'system' => 'Sistema',
        'design_system' => 'Sistema de Diseño',
        'campaigns' => 'Campañas',
    ],

    'clients' => [
        'title' => 'Clientes',
        'subtitle' => 'Administra clientes y sus datos básicos.',
        'add' => '+ Cliente',
        'search_placeholder' => 'Buscar clientes...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron clientes para ":search".',

        'profile' => [
            'title' => 'Perfil del cliente',
            'subtitle' => 'Detalles de :name',
            'back' => 'Volver',
            'tabs' => [
                'profile' => 'Perfil',
                'web' => 'Web',
                'campaign' => 'Campañas',
                'contacts' => 'Contactos',
            ],

            'fields' => [
                'name' => 'Nombre',
                'cnpj' => 'CNPJ',
                'category' => 'Categoría',
            ],

            'cards' => [
                'about' => [
                    'title' => 'Información',
                    'description' => 'Datos de registro del cliente.',
                ],
                'contracted_services' => [
                    'title' => 'Servicios contratados',
                    'description' => 'Aquí gestionaremos los servicios y planes contratados.',
                    'empty' => 'Aún no hay servicios contratados vinculados.',
                ],
                'timeline' => [
                    'title' => 'Línea de tiempo',
                    'description' => 'Eventos y movimientos relacionados con este cliente.',
                    'empty' => 'Aún no hay eventos registrados.',
                ],
                'campaign' => [
                    'title' => 'Campaña (Integración)',
                    'description' => 'Configura los IDs para cargar los datos del dashboard.',
                ],
            ],
        ],

        'table' => [
            'name' => 'Nombre',
            'cnpj' => 'CNPJ',
            'category' => 'Categoría',
            'actions' => 'Acciones',
        ],

        'offcanvas' => [
            'create_title' => 'Nuevo cliente',
            'create_description' => 'Crea un nuevo cliente.',
            'edit_title' => 'Editar cliente',
            'edit_description' => 'Actualiza los datos del cliente.',
        ],

        'form' => [
            'name' => 'Nombre',
            'cnpj' => 'CNPJ',
            'category' => 'Categoría',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'delete' => [
            'title' => 'Eliminar cliente',
            'warning' => 'Advertencia: esta acción no se puede deshacer.',
            'confirm_help' => 'Para confirmar la eliminación, escribe :word abajo:',
            'placeholder' => 'Escribe :word',
            'delete_permanently' => 'Eliminar definitivamente',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Cliente creado con éxito!',
            'updated_success' => '¡Cliente actualizado con éxito!',
            'deleted_success' => '¡Cliente eliminado con éxito!',
            'error_not_found' => 'Cliente no encontrado.',
        ],
    ],

    'webs' => [
        'title' => 'Webs',
        'subtitle' => 'Administra sitios web y landing pages.',
        'add' => '+ Web',
        'search_placeholder' => 'Buscar...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron resultados para ":search".',

        'client_tab' => [
            'title' => 'Web',
            'subtitle' => 'Sitios y landing pages de :name',
        ],

        'table' => [
            'name' => 'Nombre',
            'client' => 'Cliente',
            'url' => 'URL',
            'status' => 'Estado',
            'actions' => 'Acciones',
        ],

        'offcanvas' => [
            'create_title' => 'Nuevo web',
            'create_description' => 'Crea un nuevo sitio/landing page.',
            'edit_title' => 'Editar web',
            'edit_description' => 'Actualiza los datos del sitio/landing page.',
        ],

        'form' => [
            'client' => 'Cliente',
            'client_placeholder' => 'Selecciona un cliente',
            'name' => 'Nombre',
            'url' => 'URL',
            'type' => 'Tipo',
            'objective' => 'Objetivo',
            'cta_main' => 'CTA principal',
            'platform' => 'Plataforma',
            'status' => 'Estado',
            'responsible' => 'Responsable',
            'site_created_at' => 'Fecha de creación',
            'site_updated_at' => 'Última actualización',
            'hosting' => 'Hosting',
            'domain_until' => 'Dominio hasta',
            'ssl' => 'SSL',
            'certificate_until' => 'Certificado hasta',
            'gtm_analytics' => 'GTM/Analytics',
            'pagespeed_mobile' => 'PageSpeed (móvil)',
            'pagespeed_desktop' => 'PageSpeed (escritorio)',
            'seo_score' => 'SEO score',
            'priority' => 'Prioridad',
            'notes' => 'Observaciones',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'types' => [
            'Institucional' => 'Institucional',
            'Blog' => 'Blog',
            'E-commerce' => 'E-commerce',
            'Portal/App' => 'Portal/App',
            'Landing Page' => 'Landing Page',
            'Hotsite' => 'Hotsite',
            'Sistemas' => 'Sistemas',
            'Outro' => 'Otro',
        ],

        'objectives' => [
            'Geração de Leads' => 'Generación de leads',
            'Vendas' => 'Ventas',
            'Inscrições' => 'Inscripciones',
            'Trial/Cadastro' => 'Trial/Registro',
            'Branding' => 'Branding',
            'Pesquisa' => 'Encuesta',
            'Download' => 'Descarga',
        ],

        'platforms' => [
            'WordPress' => 'WordPress',
            'HTML' => 'HTML',
        ],

        'statuses' => [
            'Ativo' => 'Activo',
            'Em revisão' => 'En revisión',
            'Inativo' => 'Inactivo',
            'Em desenvolvimento' => 'En desarrollo',
            'Pausado' => 'Pausado',
        ],

        'delete' => [
            'title' => 'Eliminar',
            'description' => 'Esta acción no se puede deshacer.',
            'confirmation_label' => 'Confirmación',
            'placeholder' => 'Escribe ":word" para confirmar',
            'cancel' => 'Cancelar',
            'confirm' => 'Eliminar',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Registro creado con éxito!',
            'updated_success' => '¡Registro actualizado con éxito!',
            'deleted_success' => '¡Registro eliminado con éxito!',
            'error_not_found' => 'Registro no encontrado.',
            'create_disabled' => 'La creación no está disponible en esta página.',
        ],
    ],

    'campaigns' => [
        'title' => 'Campañas',
        'subtitle' => 'Administra las integraciones de campañas por cliente.',
        'search_placeholder' => 'Buscar...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron resultados para ":search".',

        'fields' => [
            'manager_customer_id' => 'Manager Customer ID',
            'client_customer_id' => 'Client Customer ID',
        ],

        'table' => [
            'client' => 'Cliente',
            'actions' => 'Acciones',
            'name' => 'Campaña',
            'channel' => 'Canal',
            'status' => 'Estado',
            'impressions' => 'Impresiones',
            'interactions' => 'Interacciones',
            'ctr' => 'CTR',
            'conversions' => 'Conversiones',
            'conversions_rate' => 'Tasa de conversión',
            'cost' => 'Costo',
        ],

        'form' => [
            'client_placeholder' => 'Selecciona un cliente',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'offcanvas' => [
            'edit_title' => 'Editar campaña',
            'edit_description' => 'Actualiza los IDs de integración.',
        ],

        'delete' => [
            'title' => 'Eliminar',
            'description' => 'Esta acción no se puede deshacer.',
            'confirmation_label' => 'Confirmación',
            'placeholder' => 'Escribe ":word" para confirmar',
            'cancel' => 'Cancelar',
            'confirm' => 'Eliminar',
        ],

        'profile_card' => [
            'title' => 'Campaña (Integración)',
            'description' => 'Configura los IDs para cargar los datos del dashboard.',
        ],

        'client_tab' => [
            'title' => 'Campaña',
            'subtitle' => 'Dashboard de campaña de :name',
        ],

        'dashboard' => [
            'title' => 'Dashboard de campaña',
            'subtitle' => 'Datos de :name',
            'back' => 'Volver',
            'period' => 'Período',
            'refresh' => 'Actualizar',
            'loading' => 'Cargando...',
            'table_title' => 'Campañas',
            'top_title' => 'Top campañas',
            'top_subtitle' => 'Clics vs impresiones',
            'status_title' => 'Estado',
            'status_subtitle' => 'Distribución',
            'cost_title' => 'Mayores Gastos',
            'cost_subtitle' => 'Top campañas por costo',
            'channel_title' => 'Tipos de Canal',
            'channel_subtitle' => 'Distribución por tipo de canal',
            'conversions_title' => 'Conversiones y Tasa',
            'conversions_subtitle' => 'Top campañas por conversiones',
            'total_of' => ':total total',
            'empty' => 'Sin datos para el período seleccionado.',
            'missing_ids' => 'Completa los IDs (Manager/Client Customer ID) en el perfil del cliente para cargar el dashboard.',
            'error' => 'Error al cargar datos de la integración.',
            'series' => [
                'clicks' => 'Clics',
                'impressions_100' => 'Impresiones / 100',
                'cost' => 'Costo (R$)',
            ],
            'status' => [
                'active' => 'Activas',
                'paused' => 'Pausadas',
                'removed' => 'Eliminadas',
            ],
        ],

        'periods' => [
            'All Time' => 'All Time',
            'Today' => 'Today',
            'Yesterday' => 'Yesterday',
            'Last 7 Days' => 'Last 7 Days',
            'Last 14 Days' => 'Last 14 Days',
            'Last 30 Days' => 'Last 30 Days',
            'Last Business Week' => 'Last Business Week',
            'This Month' => 'This Month',
            'Last Month' => 'Last Month',
        ],

        'metrics' => [
            'period' => 'Período: :period',
            'cost' => 'Costo',
            'conversions' => 'Conversiones',
            'impressions' => 'Impresiones',
            'interactions' => 'Interacciones',
            'video_views' => 'Vistas de video',
            'cpc' => 'CPC prom.',
            'cpa' => 'CPA',
            'avg_cpm' => 'CPM prom.',
            'budget' => 'Presupuesto/día',
            'optimization' => 'Score de Opt.',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'updated_success' => '¡Campaña actualizada con éxito!',
            'deleted_success' => '¡Campaña eliminada con éxito!',
            'error_not_found' => 'Campaña no encontrada.',
            'create_disabled' => 'La creación no está disponible en esta página.',
        ],
    ],

    'services' => [
        'title' => 'Servicios',
        'subtitle' => 'Administra servicios y sus precios.',
        'add' => '+ Servicio',
        'search_placeholder' => 'Buscar servicios...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron servicios para ":search".',

        'table' => [
            'name' => 'Nombre',
            'price' => 'Precio',
            'actions' => 'Acciones',
        ],

        'offcanvas' => [
            'create_title' => 'Nuevo servicio',
            'create_description' => 'Crea un nuevo servicio.',
            'edit_title' => 'Editar servicio',
            'edit_description' => 'Actualiza los datos del servicio.',
        ],

        'form' => [
            'name' => 'Nombre',
            'price' => 'Precio',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'delete' => [
            'title' => 'Eliminar servicio',
            'warning' => 'Advertencia: esta acción no se puede deshacer.',
            'confirm_help' => 'Para confirmar la eliminación, escribe :word abajo:',
            'placeholder' => 'Escribe :word',
            'delete_permanently' => 'Eliminar definitivamente',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Servicio creado con éxito!',
            'updated_success' => '¡Servicio actualizado con éxito!',
            'deleted_success' => '¡Servicio eliminado con éxito!',
            'error_not_found' => 'Servicio no encontrado.',
        ],
    ],

    'plans' => [
        'title' => 'Planes',
        'subtitle' => 'Administra planes, precios y servicios vinculados.',
        'add' => '+ Plan',
        'search_placeholder' => 'Buscar planes...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron planes para ":search".',

        'table' => [
            'name' => 'Nombre',
            'price' => 'Precio',
            'services' => 'Servicios',
            'actions' => 'Acciones',
        ],

        'card' => [
            'services_count' => ':count servicios',
        ],

        'offcanvas' => [
            'create_title' => 'Nuevo plan',
            'create_description' => 'Crea un nuevo plan.',
            'edit_title' => 'Editar plan',
            'edit_description' => 'Actualiza los datos del plan.',
        ],

        'form' => [
            'name' => 'Nombre',
            'price' => 'Precio del plan',
            'services' => 'Servicios vinculados',
            'services_placeholder' => 'Selecciona servicios...',
            'services_helper' => 'Puedes seleccionar varios servicios.',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'delete' => [
            'title' => 'Eliminar plan',
            'warning' => 'Advertencia: esta acción no se puede deshacer.',
            'confirm_help' => 'Para confirmar la eliminación, escribe :word abajo:',
            'placeholder' => 'Escribe :word',
            'delete_permanently' => 'Eliminar definitivamente',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Plan creado con éxito!',
            'updated_success' => '¡Plan actualizado con éxito!',
            'deleted_success' => '¡Plan eliminado con éxito!',
            'error_not_found' => 'Plan no encontrado.',
        ],
    ],

    'dashboard' => [
        'title' => 'Panel',
        'subtitle' => 'Resumen de tu rendimiento y proyectos recientes.',
        'export_report' => 'Exportar informe',
        'new_project' => 'Nuevo proyecto',
        'total_revenue' => 'Ingresos totales',
        'from_last_month' => 'desde el mes pasado',
        'active_users' => 'Usuarios activos',
        'from_last_week' => 'desde la semana pasada',
        'active_projects' => 'Proyectos activos',
        'from_yesterday' => 'desde ayer',
        'bounce_rate' => 'Tasa de rebote',
        'recent_projects' => 'Proyectos recientes',
        'activity_feed' => 'Actividad',
        'new_order' => 'Nuevo pedido #8932',
        'server_maintenance' => 'Mantenimiento del servidor',
        'deploy_successful' => 'Despliegue exitoso',
        'release_v2_4_0' => 'La versión v2.4.0 incluye nuevas funciones del panel.',
        'meeting_with_client' => 'Reunión con el cliente',
        'yesterday' => 'Ayer',
        'quick_actions' => 'Acciones rápidas',
        'add_user' => '+ Usuario',
        'create_invoice' => 'Crear factura',
        'view_all_projects' => 'Ver todos los proyectos',
    ],

    'users' => [
        'title' => 'Usuarios',
        'subtitle' => 'Administra accesos del sistema y miembros del equipo.',
        'export_csv' => 'CSV',
        'pdf' => 'PDF',
        'add_user' => '+ Usuario',
        'search_placeholder' => 'Buscar usuarios...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron usuarios para ":search".',

        'validation' => [
            'email_unique' => 'Este correo ya está registrado.',
        ],

        'table' => [
            'user' => 'Usuario',
            'role' => 'Rol',
            'status' => 'Estado',
            'actions' => 'Acciones',
        ],

        'offcanvas' => [
            'create_title' => 'Crear usuario',
            'create_description' => 'Agrega un nuevo usuario al sistema.',
            'edit_title' => 'Editar usuario',
            'edit_description' => 'Actualiza los datos del usuario.',
        ],

        'form' => [
            'full_name' => 'Nombre completo',
            'email' => 'Correo electrónico',
            'role' => 'Rol',
            'status' => 'Estado',
            'cancel' => 'Cancelar',
            'save_user' => 'Guardar usuario',
        ],

        'delete' => [
            'title' => 'Eliminar usuario',
            'warning' => 'Advertencia: esta acción no se puede deshacer.',
            'confirm_help' => 'Para confirmar la eliminación, escribe :word abajo:',
            'placeholder' => 'Escribe :word',
            'delete_permanently' => 'Eliminar definitivamente',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Usuario creado con éxito!',
            'updated_success' => '¡Usuario actualizado con éxito!',
            'deleted_success' => '¡Usuario eliminado con éxito!',
            'cannot_delete_superadmin' => 'No se puede eliminar al SuperAdmin.',
            'cannot_delete_self' => 'Un usuario no puede eliminarse a sí mismo.',
            'error_not_found' => 'Usuario no encontrado.',
        ],
    ],

    'widgets' => [
        'title' => 'Widgets',
        'subtitle' => 'Administra y cataloga widgets para el sistema.',
        'add' => '+ Widget',
        'search_placeholder' => 'Buscar widgets...',
        'per_page' => 'Por página',
        'no_results' => 'No se encontraron widgets para ":search".',

        'grid' => [
            'no_image' => 'Sin imagen',
        ],

        'table' => [
            'widget' => 'Widget',
            'category' => 'Categoría',
            'updated_at' => 'Actualizado en',
            'actions' => 'Acciones',
        ],

        'offcanvas' => [
            'create_title' => 'Crear widget',
            'create_description' => 'Crea un nuevo widget.',
            'edit_title' => 'Editar widget',
            'edit_description' => 'Actualiza los datos del widget.',
        ],

        'form' => [
            'name' => 'Nombre',
            'author' => 'Autor',
            'category' => 'Categoría',
            'image' => 'Imagen del widget',
            'image_helper' => 'PNG, JPG, WEBP o GIF (máx 4MB)',
            'preview' => 'Vista previa',
            'json_code' => 'Código JSON',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'validation' => [
            'json_invalid' => 'El código JSON proporcionado no es válido.',
        ],

        'delete' => [
            'title' => 'Eliminar widget',
            'warning' => 'Advertencia: esta acción no se puede deshacer.',
            'confirm_help' => 'Para confirmar la eliminación, escribe :word abajo:',
            'placeholder' => 'Escribe :word',
            'delete_permanently' => 'Eliminar definitivamente',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'json_copied' => 'JSON copiado al portapapeles.',
            'copy_failed' => 'No se pudo copiar el JSON. Inténtalo de nuevo o usa HTTPS.',
            'created_success' => '¡Widget creado con éxito!',
            'updated_success' => '¡Widget actualizado con éxito!',
            'deleted_success' => '¡Widget eliminado con éxito!',
            'error_not_found' => 'Widget no encontrado.',
        ],
    ],

    'leads' => [
        'title' => 'Leads',
        'subtitle' => 'Administra leads en un tablero Kanban.',
        'add' => '+ Lead',
        'empty' => 'No se encontraron leads.',

        'kanban' => [
            'title' => 'Kanban',
            'description' => 'Arrastra cards entre columnas para actualizar la etapa.',
        ],

        'list' => [
            'title' => 'Lista',
            'description' => 'Visualiza los leads en formato de tabla.',
        ],

        'table' => [
            'name' => 'Nombre',
            'whatsapp' => 'Whatsapp',
            'stage' => 'Etapa',
            'responsible' => 'Responsable',
            'updated_at' => 'Actualizado',
            'actions' => 'Acciones',
        ],

        'card' => [
            'whatsapp' => 'Whatsapp',
            'plan' => 'Plan',
            'services' => 'Servicios',
            'value' => 'Valor',
            'responsible' => 'Responsable',
            'origin' => 'Origen',
            'campaign' => 'Campaña',
        ],

        'offcanvas' => [
            'create_title' => 'Nuevo lead',
            'create_description' => 'Crea un nuevo lead.',
            'edit_title' => 'Editar lead',
            'edit_description' => 'Actualiza los datos del lead.',
        ],

        'form' => [
            'name' => 'Nombre',
            'whatsapp' => 'Whatsapp',
            'plan' => 'Plan',
            'plan_custom' => 'Personalizado',
            'services' => 'Servicios',
            'services_placeholder' => 'Selecciona servicios...',
            'services_helper' => 'Puedes seleccionar varios servicios.',
            'value_base' => 'Valor base',
            'discount_type' => 'Tipo de descuento',
            'discount_type_value' => 'Valor',
            'discount_type_percent' => 'Porcentaje',
            'discount_value' => 'Valor del descuento',
            'value_final' => 'Valor final',
            'responsible' => 'Usuario responsable',
            'responsible_empty' => 'Sin responsable',
            'origin' => 'Origen',
            'campaign' => 'Campaña',
            'stage' => 'Etapa',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Lead creado con éxito!',
            'updated_success' => '¡Lead actualizado con éxito!',
            'deleted_success' => '¡Lead eliminado con éxito!',
            'error_not_found' => 'Lead no encontrado.',
        ],
    ],

    'api_tokens' => [
        'title' => 'Tokens API',
        'subtitle' => 'Administra tokens para integraciones externas.',
        'add' => '+ Token',
        'empty' => 'No se encontraron tokens.',

        'list' => [
            'title' => 'Tokens',
            'description' => 'Crea y revoca tokens con permisos específicos.',
        ],

        'table' => [
            'name' => 'Nombre',
            'abilities' => 'Permisos',
            'last_used_at' => 'Último uso',
            'created_at' => 'Creado',
            'actions' => 'Acciones',
        ],

        'offcanvas' => [
            'create_title' => 'Nuevo token',
            'create_description' => 'Genera un token para autenticación Bearer.',
        ],

        'form' => [
            'name' => 'Nombre',
            'abilities' => 'Permisos (CSV)',
            'abilities_help' => 'Ej.: leads.read, leads.write',
            'cancel' => 'Cancelar',
            'save' => 'Guardar',
        ],

        'token_once' => [
            'title' => 'Copia este token ahora',
            'description' => 'Por seguridad, este token se mostrará solo una vez.',
        ],

        'delete' => [
            'title' => 'Revocar token',
            'warning' => 'Advertencia: esta acción no se puede deshacer.',
            'confirm_help' => 'Para confirmar la revocación, escribe :word abajo:',
            'placeholder' => 'Escribe :word',
            'delete_permanently' => 'Revocar token',
        ],

        'messages' => [
            'success_title' => 'Éxito',
            'error_title' => 'Error',
            'created_success' => '¡Token creado con éxito!',
            'deleted_success' => '¡Token revocado con éxito!',
            'error_not_found' => 'Token no encontrado.',
        ],
    ],

    'profile' => [
        'updated' => 'Perfil actualizado.',
        'password_updated' => 'Contraseña actualizada.',
        'edit_title' => 'Editar perfil',
        'edit_description' => 'Actualiza tu nombre, correo y foto de perfil.',
        'edit_button' => 'Editar',
        'picture' => 'Foto de perfil',
        'upload' => 'Subir',
        'upload_helper' => 'PNG, JPG, GIF (máx 2MB)',
        'name' => 'Nombre',
        'email' => 'Correo',
        'save' => 'Guardar',
        'saving' => 'Guardando...',
        'change_password_title' => 'Cambiar contraseña',
        'change_password_description' => 'Elige una contraseña segura para proteger tu cuenta.',
        'security_button' => 'Seguridad',
        'current_password' => 'Contraseña actual',
        'current_password_incorrect' => 'La contraseña actual es incorrecta.',
        'new_password' => 'Nueva contraseña',
        'confirm_new_password' => 'Confirmar nueva contraseña',
        'update' => 'Actualizar',
        'updating' => 'Actualizando...',
        'avatar_alt' => 'Avatar',
    ],

    'auth' => [
        'login' => [
            'title' => 'Login',
            'heading' => 'Iniciar sesión',
            'subtitle' => 'Accede a tu cuenta.',
            'email' => 'Correo',
            'password' => 'Contraseña',
            'forgot_password' => '¿Olvidaste tu contraseña?',
            'remember_me' => 'Recordarme',
            'submit' => 'Entrar',
            'no_account' => '¿No tienes una cuenta?',
            'register_link' => 'Registrarse',
        ],

        'register' => [
            'title' => 'Registro',
            'heading' => 'Registro',
            'subtitle' => 'Crea tu cuenta.',
            'name' => 'Nombre completo',
            'email' => 'Correo',
            'password' => 'Contraseña',
            'password_confirmation' => 'Confirmar contraseña',
            'submit' => 'Crear cuenta',
            'have_account' => '¿Ya tienes una cuenta?',
            'login_link' => 'Login',
        ],

        'forgot_password' => [
            'title' => 'Olvidé mi contraseña',
            'heading' => '¿Olvidaste tu contraseña?',
            'subtitle' => 'Ingresa tu correo para recibir un enlace para restablecer la contraseña.',
            'email' => 'Correo',
            'submit' => 'Enviar enlace',
            'back_to_login' => 'Volver al login',
            'status_sent' => 'Si este correo está registrado, te enviaremos un enlace para restablecer tu contraseña.',
            'status_error' => 'No se pudo enviar el enlace de restablecimiento. Inténtalo de nuevo.',
        ],

        'reset_password' => [
            'title' => 'Restablecer contraseña',
            'heading' => 'Restablecer contraseña',
            'subtitle' => 'Elige una nueva contraseña para tu cuenta.',
            'email' => 'Correo',
            'password' => 'Nueva contraseña',
            'password_confirmation' => 'Confirmar nueva contraseña',
            'submit' => 'Restablecer contraseña',
        ],

        'verify_email' => [
            'title' => 'Verificar correo',
            'heading' => 'Verifica tu correo',
            'subtitle' => 'Enviamos un enlace de verificación a tu correo. Verifica para continuar.',
            'status_sent' => 'Se ha enviado un nuevo enlace de verificación a tu correo.',
            'resend' => 'Reenviar correo de verificación',
            'logout' => 'Salir',
        ],
    ],

    'settings' => [
        'title' => 'Configuración',
        'subtitle' => 'Administra información de la empresa e identidad de marca.',
        'save' => 'Guardar',
        'saved' => 'Configuración guardada.',
        'company_name' => 'Nombre de la empresa',
        'logo_light' => 'Logo (Claro)',
        'logo_dark' => 'Logo (Oscuro)',
        'favicon' => 'Favicon',
        'auth_side_image' => 'Imagen lateral (Autenticación)',
        'current' => 'Actual',
    ],
];
