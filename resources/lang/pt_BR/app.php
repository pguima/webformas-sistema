<?php

return [
    'common' => [
        'dash' => '—',
    ],

    'sidebar' => [
        'dashboard' => 'Painel',
        'users' => 'Usuários',
        'widgets' => 'Widgets',
        'leads' => 'Leads',
        'api_tokens' => 'Tokens API',
        'profile' => 'Perfil',
        'settings' => 'Configurações',
        'system' => 'Sistema',
        'design_system' => 'Design System',
        'clients' => 'Clientes',
        'services' => 'Serviços',
        'plans' => 'Planos',
        'employees' => 'Funcionários',
        'folders' => 'Pastas',
        'files' => 'Arquivos',
        'management' => 'Gestão',
    ],

    'clients' => [
        'title' => 'Clientes',
        'subtitle' => 'Gerencie clientes e seus dados básicos.',
        'add' => '+ Cliente',
        'search_placeholder' => 'Buscar clientes...',
        'per_page' => 'Por página',
        'no_results' => 'Nenhum cliente encontrado para ":search".',

        'profile' => [
            'title' => 'Perfil do cliente',
            'subtitle' => 'Detalhes de :name',
            'back' => 'Voltar',
            'tabs' => [
                'profile' => 'Perfil',
                'web' => 'Web',
            ],

            'fields' => [
                'name' => 'Nome',
                'cnpj' => 'CNPJ',
                'category' => 'Categoria',
            ],

            'cards' => [
                'about' => [
                    'title' => 'Informações',
                    'description' => 'Dados cadastrais do cliente.',
                ],
                'contracted_services' => [
                    'title' => 'Serviços contratados',
                    'description' => 'Aqui vamos gerenciar os serviços e planos contratados.',
                    'empty' => 'Nenhum serviço contratado vinculado ainda.',
                ],
                'timeline' => [
                    'title' => 'Linha do tempo',
                    'description' => 'Eventos e movimentações relacionados ao cliente.',
                    'empty' => 'Nenhum evento registrado ainda.',
                ],
            ],
        ],

        'table' => [
            'name' => 'Nome',
            'cnpj' => 'CNPJ',
            'category' => 'Categoria',
            'actions' => 'Ações',
        ],

        'offcanvas' => [
            'create_title' => 'Novo cliente',
            'create_description' => 'Cadastre um novo cliente.',
            'edit_title' => 'Editar cliente',
            'edit_description' => 'Atualize os dados do cliente.',
        ],

        'form' => [
            'name' => 'Nome',
            'cnpj' => 'CNPJ',
            'category' => 'Categoria',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'delete' => [
            'title' => 'Excluir cliente',
            'warning' => 'Atenção: esta ação não pode ser desfeita.',
            'confirm_help' => 'Para confirmar a exclusão, digite :word abaixo:',
            'placeholder' => 'Digite :word',
            'delete_permanently' => 'Excluir definitivamente',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Cliente criado com sucesso!',
            'updated_success' => 'Cliente atualizado com sucesso!',
            'deleted_success' => 'Cliente excluído com sucesso!',
            'error_not_found' => 'Cliente não encontrado.',
        ],
    ],

    'webs' => [
        'title' => 'Webs',
        'subtitle' => 'Gerencie sites e landing pages.',
        'add' => '+ Web',
        'search_placeholder' => 'Buscar...',
        'per_page' => 'Por página',
        'no_results' => 'Nenhum registro encontrado para ":search".',

        'client_tab' => [
            'title' => 'Web',
            'subtitle' => 'Sites e landing pages de :name',
        ],

        'table' => [
            'name' => 'Nome',
            'client' => 'Cliente',
            'url' => 'URL',
            'status' => 'Status',
            'actions' => 'Ações',
        ],

        'offcanvas' => [
            'create_title' => 'Novo Web',
            'create_description' => 'Cadastre um novo site/landing page.',
            'edit_title' => 'Editar Web',
            'edit_description' => 'Atualize os dados do site/landing page.',
        ],

        'form' => [
            'client' => 'Cliente',
            'client_placeholder' => 'Selecione um cliente',
            'name' => 'Nome',
            'url' => 'URL',
            'type' => 'Tipo',
            'objective' => 'Objetivo',
            'cta_main' => 'CTA Principal',
            'platform' => 'Plataforma',
            'status' => 'Status',
            'responsible' => 'Responsável',
            'site_created_at' => 'Data de Criação',
            'site_updated_at' => 'Última Atualização',
            'hosting' => 'Hospedagem',
            'domain_until' => 'Domínio até',
            'ssl' => 'SSL',
            'certificate_until' => 'Certificado até',
            'gtm_analytics' => 'GTM/Analytics',
            'pagespeed_mobile' => 'PageSpeed (mob)',
            'pagespeed_desktop' => 'PageSpeed (desk)',
            'seo_score' => 'SEO Score',
            'priority' => 'Prioridade',
            'notes' => 'Observações',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'types' => [
            'Institucional' => 'Institucional',
            'Blog' => 'Blog',
            'E-commerce' => 'E-commerce',
            'Portal/App' => 'Portal/App',
            'Landing Page' => 'Landing Page',
            'Hotsite' => 'Hotsite',
            'Sistemas' => 'Sistemas',
            'Outro' => 'Outro',
        ],

        'objectives' => [
            'Geração de Leads' => 'Geração de Leads',
            'Vendas' => 'Vendas',
            'Inscrições' => 'Inscrições',
            'Trial/Cadastro' => 'Trial/Cadastro',
            'Branding' => 'Branding',
            'Pesquisa' => 'Pesquisa',
            'Download' => 'Download',
        ],

        'platforms' => [
            'WordPress' => 'WordPress',
            'HTML' => 'HTML',
        ],

        'statuses' => [
            'Ativo' => 'Ativo',
            'Em revisão' => 'Em revisão',
            'Inativo' => 'Inativo',
            'Em desenvolvimento' => 'Em desenvolvimento',
            'Pausado' => 'Pausado',
        ],

        'delete' => [
            'title' => 'Excluir',
            'description' => 'Esta ação não pode ser desfeita.',
            'confirmation_label' => 'Confirmação',
            'placeholder' => 'Digite ":word" para confirmar',
            'cancel' => 'Cancelar',
            'confirm' => 'Excluir',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Registro criado com sucesso!',
            'updated_success' => 'Registro atualizado com sucesso!',
            'deleted_success' => 'Registro excluído com sucesso!',
            'error_not_found' => 'Registro não encontrado.',
            'create_disabled' => 'A criação não está disponível nesta página.',
        ],
    ],

    'services' => [
        'title' => 'Serviços',
        'subtitle' => 'Gerencie serviços e seus preços.',
        'add' => '+ Serviço',
        'search_placeholder' => 'Buscar serviços...',
        'per_page' => 'Por página',
        'no_results' => 'Nenhum serviço encontrado para ":search".',

        'table' => [
            'name' => 'Nome',
            'price' => 'Preço',
            'actions' => 'Ações',
        ],

        'offcanvas' => [
            'create_title' => 'Novo serviço',
            'create_description' => 'Cadastre um novo serviço.',
            'edit_title' => 'Editar serviço',
            'edit_description' => 'Atualize os dados do serviço.',
        ],

        'form' => [
            'name' => 'Nome',
            'price' => 'Preço',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'delete' => [
            'title' => 'Excluir serviço',
            'warning' => 'Atenção: esta ação não pode ser desfeita.',
            'confirm_help' => 'Para confirmar a exclusão, digite :word abaixo:',
            'placeholder' => 'Digite :word',
            'delete_permanently' => 'Excluir definitivamente',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Serviço criado com sucesso!',
            'updated_success' => 'Serviço atualizado com sucesso!',
            'deleted_success' => 'Serviço excluído com sucesso!',
            'error_not_found' => 'Serviço não encontrado.',
        ],
    ],

    'plans' => [
        'title' => 'Planos',
        'subtitle' => 'Gerencie planos, preços e serviços vinculados.',
        'add' => '+ Plano',
        'search_placeholder' => 'Buscar planos...',
        'per_page' => 'Por página',
        'no_results' => 'Nenhum plano encontrado para ":search".',

        'table' => [
            'name' => 'Nome',
            'price' => 'Preço',
            'services' => 'Serviços',
            'actions' => 'Ações',
        ],

        'card' => [
            'services_count' => ':count serviços',
        ],

        'offcanvas' => [
            'create_title' => 'Novo plano',
            'create_description' => 'Cadastre um novo plano.',
            'edit_title' => 'Editar plano',
            'edit_description' => 'Atualize os dados do plano.',
        ],

        'form' => [
            'name' => 'Nome',
            'price' => 'Preço do plano',
            'services' => 'Serviços vinculados',
            'services_placeholder' => 'Selecione serviços...',
            'services_helper' => 'Você pode selecionar vários serviços.',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'delete' => [
            'title' => 'Excluir plano',
            'warning' => 'Atenção: esta ação não pode ser desfeita.',
            'confirm_help' => 'Para confirmar a exclusão, digite :word abaixo:',
            'placeholder' => 'Digite :word',
            'delete_permanently' => 'Excluir definitivamente',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Plano criado com sucesso!',
            'updated_success' => 'Plano atualizado com sucesso!',
            'deleted_success' => 'Plano excluído com sucesso!',
            'error_not_found' => 'Plano não encontrado.',
        ],
    ],

    'leads' => [
        'title' => 'Leads',
        'subtitle' => 'Gerencie leads em formato Kanban.',
        'add' => '+ Lead',
        'empty' => 'Nenhum lead encontrado.',

        'kanban' => [
            'title' => 'Kanban',
            'description' => 'Arraste os cards entre colunas para atualizar a etapa.',
        ],

        'list' => [
            'title' => 'Lista',
            'description' => 'Visualize os leads em formato de tabela.',
        ],

        'table' => [
            'name' => 'Nome',
            'whatsapp' => 'Whatsapp',
            'stage' => 'Etapa',
            'responsible' => 'Responsável',
            'updated_at' => 'Atualizado em',
            'actions' => 'Ações',
        ],

        'card' => [
            'whatsapp' => 'Whatsapp',
            'plan' => 'Plano',
            'services' => 'Serviços',
            'value' => 'Valor',
            'responsible' => 'Responsável',
            'origin' => 'Origem',
            'campaign' => 'Campanha',
        ],

        'offcanvas' => [
            'create_title' => 'Novo lead',
            'create_description' => 'Cadastre um novo lead.',
            'edit_title' => 'Editar lead',
            'edit_description' => 'Atualize os dados do lead.',
        ],

        'form' => [
            'name' => 'Nome',
            'whatsapp' => 'Whatsapp',
            'plan' => 'Plano',
            'plan_custom' => 'Personalizado',
            'services' => 'Serviços',
            'services_placeholder' => 'Selecione serviços...',
            'services_helper' => 'Você pode selecionar vários serviços.',
            'value_base' => 'Valor base',
            'discount_type' => 'Tipo de desconto',
            'discount_type_value' => 'Valor',
            'discount_type_percent' => 'Percentual',
            'discount_value' => 'Valor do desconto',
            'value_final' => 'Valor final',
            'responsible' => 'Usuário responsável',
            'responsible_empty' => 'Sem responsável',
            'origin' => 'Origem',
            'campaign' => 'Campanha',
            'stage' => 'Etapa',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Lead criado com sucesso!',
            'updated_success' => 'Lead atualizado com sucesso!',
            'deleted_success' => 'Lead excluído com sucesso!',
            'error_not_found' => 'Lead não encontrado.',
        ],
    ],

    'api_tokens' => [
        'title' => 'Tokens de API',
        'subtitle' => 'Gerencie tokens para integrações externas.',
        'add' => '+ Token',
        'empty' => 'Nenhum token encontrado.',

        'list' => [
            'title' => 'Tokens',
            'description' => 'Crie e revogue tokens com permissões específicas.',
        ],

        'table' => [
            'name' => 'Nome',
            'abilities' => 'Permissões',
            'last_used_at' => 'Último uso',
            'created_at' => 'Criado em',
            'actions' => 'Ações',
        ],

        'offcanvas' => [
            'create_title' => 'Novo token',
            'create_description' => 'Gere um token para autenticação via Bearer.',
        ],

        'form' => [
            'name' => 'Nome',
            'abilities' => 'Permissões (CSV)',
            'abilities_help' => 'Ex.: leads.read, leads.write',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'token_once' => [
            'title' => 'Copie este token agora',
            'description' => 'Por segurança, este token será exibido apenas uma vez.',
        ],

        'delete' => [
            'title' => 'Revogar token',
            'warning' => 'Atenção: esta ação não pode ser desfeita.',
            'confirm_help' => 'Para confirmar a revogação, digite :word abaixo:',
            'placeholder' => 'Digite :word',
            'delete_permanently' => 'Revogar token',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Token criado com sucesso!',
            'deleted_success' => 'Token revogado com sucesso!',
            'error_not_found' => 'Token não encontrado.',
        ],
    ],

    'dashboard' => [
        'title' => 'Painel',
        'subtitle' => 'Visão geral do seu desempenho e projetos recentes.',
        'export_report' => 'Exportar relatório',
        'new_project' => 'Novo projeto',
        'total_revenue' => 'Receita total',
        'from_last_month' => 'em relação ao mês passado',
        'active_users' => 'Usuários ativos',
        'from_last_week' => 'em relação à semana passada',
        'active_projects' => 'Projetos ativos',
        'from_yesterday' => 'em relação a ontem',
        'bounce_rate' => 'Taxa de rejeição',
        'recent_projects' => 'Projetos recentes',
        'activity_feed' => 'Atividades',
        'new_order' => 'Novo pedido #8932',
        'server_maintenance' => 'Manutenção do servidor',
        'deploy_successful' => 'Deploy realizado com sucesso',
        'release_v2_4_0' => 'A versão v2.4.0 inclui novos recursos no painel.',
        'meeting_with_client' => 'Reunião com cliente',
        'yesterday' => 'Ontem',
        'quick_actions' => 'Ações rápidas',
        'add_user' => 'Adicionar usuário',
        'create_invoice' => 'Criar fatura',
        'view_all_projects' => 'Ver todos os projetos',
    ],

    'users' => [
        'title' => 'Usuários',
        'subtitle' => 'Gerencie acessos do sistema e membros da equipe.',
        'export_csv' => 'CSV',
        'pdf' => 'PDF',
        'add_user' => '+ Usuário',
        'search_placeholder' => 'Buscar usuários...',
        'per_page' => 'Por página',
        'no_results' => 'Nenhum usuário encontrado para ":search".',

        'validation' => [
            'email_unique' => 'Este e-mail já está cadastrado.',
        ],

        'table' => [
            'user' => 'Usuário',
            'role' => 'Função',
            'status' => 'Status',
            'actions' => 'Ações',
        ],

        'offcanvas' => [
            'create_title' => 'Criar usuário',
            'create_description' => 'Adicione um novo usuário ao sistema.',
            'edit_title' => 'Editar usuário',
            'edit_description' => 'Atualize os dados do usuário.',
        ],

        'form' => [
            'full_name' => 'Nome completo',
            'email' => 'E-mail',
            'role' => 'Função',
            'status' => 'Status',
            'cancel' => 'Cancelar',
            'save_user' => 'Salvar usuário',
        ],

        'delete' => [
            'title' => 'Excluir usuário',
            'warning' => 'Atenção: esta ação não pode ser desfeita.',
            'confirm_help' => 'Para confirmar a exclusão, digite :word abaixo:',
            'placeholder' => 'Digite :word',
            'delete_permanently' => 'Excluir definitivamente',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'created_success' => 'Usuário criado com sucesso!',
            'updated_success' => 'Usuário atualizado com sucesso!',
            'deleted_success' => 'Usuário excluído com sucesso!',
            'cannot_delete_superadmin' => 'SuperAdmin não pode ser excluído.',
            'cannot_delete_self' => 'Um usuário não pode se excluir.',
            'error_not_found' => 'Usuário não encontrado.',
        ],
    ],

    'widgets' => [
        'title' => 'Widgets',
        'subtitle' => 'Gerencie e catalogue widgets para o sistema.',
        'add' => '+ Widget',
        'search_placeholder' => 'Buscar widgets...',
        'per_page' => 'Por página',
        'no_results' => 'Nenhum widget encontrado para ":search".',

        'grid' => [
            'no_image' => 'Sem imagem',
        ],

        'table' => [
            'widget' => 'Widget',
            'category' => 'Categoria',
            'updated_at' => 'Atualizado em',
            'actions' => 'Ações',
        ],

        'offcanvas' => [
            'create_title' => 'Criar widget',
            'create_description' => 'Cadastre um novo widget.',
            'edit_title' => 'Editar widget',
            'edit_description' => 'Atualize os dados do widget.',
        ],

        'form' => [
            'name' => 'Nome',
            'author' => 'Autor',
            'category' => 'Categoria',
            'image' => 'Imagem do Widget',
            'image_helper' => 'PNG, JPG, WEBP ou GIF (máx 4MB)',
            'preview' => 'Preview',
            'json_code' => 'Código Json',
            'cancel' => 'Cancelar',
            'save' => 'Salvar',
        ],

        'validation' => [
            'json_invalid' => 'O Código Json informado é inválido.',
        ],

        'delete' => [
            'title' => 'Excluir widget',
            'warning' => 'Atenção: esta ação não pode ser desfeita.',
            'confirm_help' => 'Para confirmar a exclusão, digite :word abaixo:',
            'placeholder' => 'Digite :word',
            'delete_permanently' => 'Excluir definitivamente',
        ],

        'messages' => [
            'success_title' => 'Sucesso',
            'error_title' => 'Erro',
            'json_copied' => 'JSON copiado para a área de transferência.',
            'copy_failed' => 'Não foi possível copiar o JSON. Tente novamente ou use HTTPS.',
            'created_success' => 'Widget criado com sucesso!',
            'updated_success' => 'Widget atualizado com sucesso!',
            'deleted_success' => 'Widget excluído com sucesso!',
            'error_not_found' => 'Widget não encontrado.',
        ],
    ],

    'profile' => [
        'updated' => 'Perfil atualizado.',
        'password_updated' => 'Senha atualizada.',
        'edit_title' => 'Editar perfil',
        'edit_description' => 'Atualize seu nome, e-mail e foto do perfil.',
        'edit_button' => 'Editar',
        'picture' => 'Foto do perfil',
        'upload' => 'Enviar',
        'upload_helper' => 'PNG, JPG, GIF (máx 2MB)',
        'name' => 'Nome',
        'email' => 'E-mail',
        'save' => 'Salvar',
        'saving' => 'Salvando...',
        'change_password_title' => 'Alterar senha',
        'change_password_description' => 'Escolha uma senha forte para manter sua conta segura.',
        'security_button' => 'Segurança',
        'current_password' => 'Senha atual',
        'current_password_incorrect' => 'A senha atual está incorreta.',
        'new_password' => 'Nova senha',
        'confirm_new_password' => 'Confirmar nova senha',
        'update' => 'Atualizar',
        'updating' => 'Atualizando...',
        'avatar_alt' => 'Avatar',
    ],

    'auth' => [
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'subtitle' => 'Acesse sua conta.',
            'email' => 'E-mail',
            'password' => 'Senha',
            'forgot_password' => 'Esqueceu sua senha?',
            'remember_me' => 'Lembrar-me',
            'submit' => 'Entrar',
            'no_account' => 'Ainda não tem uma conta?',
            'register_link' => 'Cadastrar',
        ],

        'register' => [
            'title' => 'Cadastro',
            'heading' => 'Cadastro',
            'subtitle' => 'Crie sua conta.',
            'name' => 'Nome completo',
            'email' => 'E-mail',
            'password' => 'Senha',
            'password_confirmation' => 'Confirmar senha',
            'submit' => 'Criar conta',
            'have_account' => 'Já tem uma conta?',
            'login_link' => 'Login',
        ],

        'forgot_password' => [
            'title' => 'Esqueci a senha',
            'heading' => 'Esqueceu sua senha?',
            'subtitle' => 'Informe seu e-mail para receber o link de redefinição de senha.',
            'email' => 'E-mail',
            'submit' => 'Enviar link',
            'back_to_login' => 'Voltar para o login',
            'status_sent' => 'Se este e-mail estiver cadastrado, enviaremos um link para redefinir sua senha.',
            'status_error' => 'Não foi possível enviar o link de redefinição. Tente novamente.',
        ],

        'reset_password' => [
            'title' => 'Redefinir senha',
            'heading' => 'Redefinir senha',
            'subtitle' => 'Escolha uma nova senha para sua conta.',
            'email' => 'E-mail',
            'password' => 'Nova senha',
            'password_confirmation' => 'Confirmar nova senha',
            'submit' => 'Redefinir senha',
        ],

        'verify_email' => [
            'title' => 'Verificar e-mail',
            'heading' => 'Verifique seu e-mail',
            'subtitle' => 'Enviamos um link de verificação para o seu e-mail. Verifique para continuar.',
            'status_sent' => 'Um novo link de verificação foi enviado para o seu e-mail.',
            'resend' => 'Reenviar e-mail de verificação',
            'logout' => 'Sair',
        ],
    ],

    'settings' => [
        'title' => 'Configurações',
        'subtitle' => 'Gerencie informações da empresa e identidade visual.',
        'save' => 'Salvar',
        'saved' => 'Configurações salvas.',
        'company_name' => 'Nome da empresa',
        'logo_light' => 'Logo (Claro)',
        'logo_dark' => 'Logo (Escuro)',
        'favicon' => 'Favicon',
        'auth_side_image' => 'Imagem lateral (Autenticação)',
        'current' => 'Atual',
    ],
];
