<?php

return [
    'sidebar' => [
        'dashboard' => 'Panel',
        'clients' => 'Clientes',
        'employees' => 'Empleados',
        'folders' => 'Carpetas',
        'files' => 'Archivos',
        'management' => 'Gestión',
        'users' => 'Usuarios',
        'widgets' => 'Widgets',
        'leads' => 'Leads',
        'api_tokens' => 'Tokens API',
        'profile' => 'Perfil',
        'settings' => 'Configuración',
        'system' => 'Sistema',
        'design_system' => 'Sistema de Diseño',
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
            'services' => 'Servicios',
            'value' => 'Valor',
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
