<?php

return [
    'common' => [
        'dash' => '—',
    ],

    'sidebar' => [
        'dashboard' => 'Dashboard',
        'clients' => 'Clients',
        'services' => 'Services',
        'plans' => 'Plans',
        'employees' => 'Employees',
        'folders' => 'Folders',
        'files' => 'Files',
        'management' => 'Management',
        'users' => 'Users',
        'widgets' => 'Widgets',
        'leads' => 'Leads',
        'api_tokens' => 'API Tokens',
        'profile' => 'Profile',
        'settings' => 'Settings',
        'system' => 'System',
        'design_system' => 'Design System',
    ],

    'clients' => [
        'title' => 'Clients',
        'subtitle' => 'Manage clients and their basic data.',
        'add' => '+ Client',
        'search_placeholder' => 'Search clients...',
        'per_page' => 'Per page',
        'no_results' => 'No clients found matching ":search".',

        'profile' => [
            'title' => 'Client profile',
            'subtitle' => 'Details for :name',
            'back' => 'Back',
            'tabs' => [
                'profile' => 'Profile',
                'web' => 'Web',
            ],

            'fields' => [
                'name' => 'Name',
                'cnpj' => 'CNPJ',
                'category' => 'Category',
            ],

            'cards' => [
                'about' => [
                    'title' => 'About',
                    'description' => 'Client registration details.',
                ],
                'contracted_services' => [
                    'title' => 'Contracted services',
                    'description' => 'This area will manage contracted services and plans.',
                    'empty' => 'No contracted services linked yet.',
                ],
                'timeline' => [
                    'title' => 'Timeline',
                    'description' => 'Events and movements related to this client.',
                    'empty' => 'No events recorded yet.',
                ],
            ],
        ],

        'table' => [
            'name' => 'Name',
            'cnpj' => 'CNPJ',
            'category' => 'Category',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'New client',
            'create_description' => 'Create a new client.',
            'edit_title' => 'Edit client',
            'edit_description' => 'Update client details.',
        ],

        'form' => [
            'name' => 'Name',
            'cnpj' => 'CNPJ',
            'category' => 'Category',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'delete' => [
            'title' => 'Delete client',
            'warning' => 'Warning: This action cannot be undone.',
            'confirm_help' => 'To confirm deletion, please type :word below:',
            'placeholder' => 'Type :word',
            'delete_permanently' => 'Delete permanently',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Client created successfully!',
            'updated_success' => 'Client updated successfully!',
            'deleted_success' => 'Client deleted successfully!',
            'error_not_found' => 'Client not found.',
        ],
    ],

    'webs' => [
        'title' => 'Web',
        'subtitle' => 'Manage websites and landing pages.',
        'add' => '+ Web',
        'search_placeholder' => 'Search...',
        'per_page' => 'Per page',
        'no_results' => 'No results found for ":search".',

        'client_tab' => [
            'title' => 'Web',
            'subtitle' => 'Websites and landing pages for :name',
        ],

        'table' => [
            'name' => 'Name',
            'client' => 'Client',
            'url' => 'URL',
            'status' => 'Status',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'New web',
            'create_description' => 'Create a new website/landing page.',
            'edit_title' => 'Edit web',
            'edit_description' => 'Update the website/landing page details.',
        ],

        'form' => [
            'client' => 'Client',
            'client_placeholder' => 'Select a client',
            'name' => 'Name',
            'url' => 'URL',
            'type' => 'Type',
            'objective' => 'Objective',
            'cta_main' => 'Main CTA',
            'platform' => 'Platform',
            'status' => 'Status',
            'responsible' => 'Owner',
            'site_created_at' => 'Creation date',
            'site_updated_at' => 'Last update',
            'hosting' => 'Hosting',
            'domain_until' => 'Domain until',
            'ssl' => 'SSL',
            'certificate_until' => 'Certificate until',
            'gtm_analytics' => 'GTM/Analytics',
            'pagespeed_mobile' => 'PageSpeed (mobile)',
            'pagespeed_desktop' => 'PageSpeed (desktop)',
            'seo_score' => 'SEO score',
            'priority' => 'Priority',
            'notes' => 'Notes',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'types' => [
            'Institucional' => 'Institutional',
            'Blog' => 'Blog',
            'E-commerce' => 'E-commerce',
            'Portal/App' => 'Portal/App',
            'Landing Page' => 'Landing Page',
            'Hotsite' => 'Hotsite',
            'Sistemas' => 'Systems',
            'Outro' => 'Other',
        ],

        'objectives' => [
            'Geração de Leads' => 'Lead generation',
            'Vendas' => 'Sales',
            'Inscrições' => 'Signups',
            'Trial/Cadastro' => 'Trial/Signup',
            'Branding' => 'Branding',
            'Pesquisa' => 'Survey',
            'Download' => 'Download',
        ],

        'platforms' => [
            'WordPress' => 'WordPress',
            'HTML' => 'HTML',
        ],

        'statuses' => [
            'Ativo' => 'Active',
            'Em revisão' => 'In review',
            'Inativo' => 'Inactive',
            'Em desenvolvimento' => 'In development',
            'Pausado' => 'Paused',
        ],

        'delete' => [
            'title' => 'Delete',
            'description' => 'This action cannot be undone.',
            'confirmation_label' => 'Confirmation',
            'placeholder' => 'Type ":word" to confirm',
            'cancel' => 'Cancel',
            'confirm' => 'Delete',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Record created successfully!',
            'updated_success' => 'Record updated successfully!',
            'deleted_success' => 'Record deleted successfully!',
            'error_not_found' => 'Record not found.',
            'create_disabled' => 'Creation is not available on this page.',
        ],
    ],

    'services' => [
        'title' => 'Services',
        'subtitle' => 'Manage services and their prices.',
        'add' => '+ Service',
        'search_placeholder' => 'Search services...',
        'per_page' => 'Per page',
        'no_results' => 'No services found matching ":search".',

        'table' => [
            'name' => 'Name',
            'price' => 'Price',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'New service',
            'create_description' => 'Create a new service.',
            'edit_title' => 'Edit service',
            'edit_description' => 'Update service details.',
        ],

        'form' => [
            'name' => 'Name',
            'price' => 'Price',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'delete' => [
            'title' => 'Delete service',
            'warning' => 'Warning: This action cannot be undone.',
            'confirm_help' => 'To confirm deletion, please type :word below:',
            'placeholder' => 'Type :word',
            'delete_permanently' => 'Delete permanently',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Service created successfully!',
            'updated_success' => 'Service updated successfully!',
            'deleted_success' => 'Service deleted successfully!',
            'error_not_found' => 'Service not found.',
        ],
    ],

    'plans' => [
        'title' => 'Plans',
        'subtitle' => 'Manage plans, prices and linked services.',
        'add' => '+ Plan',
        'search_placeholder' => 'Search plans...',
        'per_page' => 'Per page',
        'no_results' => 'No plans found matching ":search".',

        'table' => [
            'name' => 'Name',
            'price' => 'Price',
            'services' => 'Services',
            'actions' => 'Actions',
        ],

        'card' => [
            'services_count' => ':count services',
        ],

        'offcanvas' => [
            'create_title' => 'New plan',
            'create_description' => 'Create a new plan.',
            'edit_title' => 'Edit plan',
            'edit_description' => 'Update plan details.',
        ],

        'form' => [
            'name' => 'Name',
            'price' => 'Plan price',
            'services' => 'Linked services',
            'services_placeholder' => 'Select services...',
            'services_helper' => 'You can select multiple services.',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'delete' => [
            'title' => 'Delete plan',
            'warning' => 'Warning: This action cannot be undone.',
            'confirm_help' => 'To confirm deletion, please type :word below:',
            'placeholder' => 'Type :word',
            'delete_permanently' => 'Delete permanently',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Plan created successfully!',
            'updated_success' => 'Plan updated successfully!',
            'deleted_success' => 'Plan deleted successfully!',
            'error_not_found' => 'Plan not found.',
        ],
    ],

    'dashboard' => [
        'title' => 'Dashboard',
        'subtitle' => 'Overview of your latest performance and projects.',
        'export_report' => 'Export Report',
        'new_project' => 'New Project',
        'total_revenue' => 'Total Revenue',
        'from_last_month' => 'from last month',
        'active_users' => 'Active Users',
        'from_last_week' => 'from last week',
        'active_projects' => 'Active Projects',
        'from_yesterday' => 'from yesterday',
        'bounce_rate' => 'Bounce Rate',
        'recent_projects' => 'Recent Projects',
        'activity_feed' => 'Activity Feed',
        'new_order' => 'New order #8932',
        'server_maintenance' => 'Server maintenance',
        'deploy_successful' => 'Deploy successful',
        'release_v2_4_0' => 'Release v2.4.0 includes new dashboard features.',
        'meeting_with_client' => 'Meeting with client',
        'quick_actions' => 'Quick Actions',
        'add_user' => '+ User',
        'create_invoice' => 'Create Invoice',
        'view_all_projects' => 'View all projects',
    ],

    'users' => [
        'title' => 'Users',
        'subtitle' => 'Manage system access and team members.',
        'export_csv' => 'CSV',
        'pdf' => 'PDF',
        'add_user' => '+ User',
        'search_placeholder' => 'Search users...',
        'per_page' => 'Per page',
        'no_results' => 'No users found matching ":search".',

        'validation' => [
            'email_unique' => 'This email is already registered.',
        ],

        'table' => [
            'user' => 'User',
            'role' => 'Role',
            'status' => 'Status',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'Create User',
            'create_description' => 'Add a new user to the system.',
            'edit_title' => 'Edit User',
            'edit_description' => 'Update user details.',
        ],

        'form' => [
            'full_name' => 'Full Name',
            'email' => 'Email Address',
            'role' => 'Role',
            'status' => 'Status',
            'cancel' => 'Cancel',
            'save_user' => 'Save User',
        ],

        'delete' => [
            'title' => 'Delete User',
            'warning' => 'Warning: This action cannot be undone.',
            'confirm_help' => 'To confirm deletion, please type :word below:',
            'placeholder' => 'Type :word',
            'delete_permanently' => 'Delete Permanently',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'User created successfully!',
            'updated_success' => 'User updated successfully!',
            'deleted_success' => 'User deleted successfully!',
            'cannot_delete_superadmin' => 'SuperAdmin cannot be deleted.',
            'cannot_delete_self' => 'A user cannot delete themselves.',
            'error_not_found' => 'User not found.',
        ],
    ],

    'widgets' => [
        'title' => 'Widgets',
        'subtitle' => 'Manage and catalog widgets for the system.',
        'add' => '+ Widget',
        'search_placeholder' => 'Search widgets...',
        'per_page' => 'Per page',
        'no_results' => 'No widgets found matching ":search".',

        'grid' => [
            'no_image' => 'No image',
        ],

        'table' => [
            'widget' => 'Widget',
            'category' => 'Category',
            'updated_at' => 'Updated at',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'Create widget',
            'create_description' => 'Create a new widget.',
            'edit_title' => 'Edit widget',
            'edit_description' => 'Update widget details.',
        ],

        'form' => [
            'name' => 'Name',
            'author' => 'Author',
            'category' => 'Category',
            'image' => 'Widget image',
            'image_helper' => 'PNG, JPG, WEBP or GIF (max 4MB)',
            'preview' => 'Preview',
            'json_code' => 'JSON code',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'validation' => [
            'json_invalid' => 'The provided JSON code is invalid.',
        ],

        'delete' => [
            'title' => 'Delete widget',
            'warning' => 'Warning: This action cannot be undone.',
            'confirm_help' => 'To confirm deletion, please type :word below:',
            'placeholder' => 'Type :word',
            'delete_permanently' => 'Delete permanently',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'json_copied' => 'JSON copied to clipboard.',
            'copy_failed' => 'Could not copy JSON. Please try again or use HTTPS.',
            'created_success' => 'Widget created successfully!',
            'updated_success' => 'Widget updated successfully!',
            'deleted_success' => 'Widget deleted successfully!',
            'error_not_found' => 'Widget not found.',
        ],
    ],

    'leads' => [
        'title' => 'Leads',
        'subtitle' => 'Manage leads in a Kanban board.',
        'add' => '+ Lead',
        'empty' => 'No leads found.',

        'kanban' => [
            'title' => 'Kanban',
            'description' => 'Drag cards between columns to update the stage.',
        ],

        'list' => [
            'title' => 'List',
            'description' => 'View leads in a table format.',
        ],

        'table' => [
            'name' => 'Name',
            'whatsapp' => 'Whatsapp',
            'stage' => 'Stage',
            'responsible' => 'Responsible',
            'updated_at' => 'Updated at',
            'actions' => 'Actions',
        ],

        'card' => [
            'whatsapp' => 'Whatsapp',
            'plan' => 'Plan',
            'services' => 'Services',
            'value' => 'Value',
            'responsible' => 'Responsible',
            'origin' => 'Origin',
            'campaign' => 'Campaign',
        ],

        'offcanvas' => [
            'create_title' => 'New lead',
            'create_description' => 'Create a new lead.',
            'edit_title' => 'Edit lead',
            'edit_description' => 'Update lead details.',
        ],

        'form' => [
            'name' => 'Name',
            'whatsapp' => 'Whatsapp',
            'plan' => 'Plan',
            'plan_custom' => 'Custom',
            'services' => 'Services',
            'services_placeholder' => 'Select services...',
            'services_helper' => 'You can select multiple services.',
            'value_base' => 'Base value',
            'discount_type' => 'Discount type',
            'discount_type_value' => 'Value',
            'discount_type_percent' => 'Percent',
            'discount_value' => 'Discount value',
            'value_final' => 'Final value',
            'responsible' => 'Responsible user',
            'responsible_empty' => 'No responsible',
            'origin' => 'Origin',
            'campaign' => 'Campaign',
            'stage' => 'Stage',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Lead created successfully!',
            'updated_success' => 'Lead updated successfully!',
            'deleted_success' => 'Lead deleted successfully!',
            'error_not_found' => 'Lead not found.',
        ],
    ],

    'api_tokens' => [
        'title' => 'API Tokens',
        'subtitle' => 'Manage tokens for external integrations.',
        'add' => '+ Token',
        'empty' => 'No tokens found.',

        'list' => [
            'title' => 'Tokens',
            'description' => 'Create and revoke tokens with specific permissions.',
        ],

        'table' => [
            'name' => 'Name',
            'abilities' => 'Abilities',
            'last_used_at' => 'Last used',
            'created_at' => 'Created at',
            'actions' => 'Actions',
        ],

        'offcanvas' => [
            'create_title' => 'New token',
            'create_description' => 'Generate a token for Bearer authentication.',
        ],

        'form' => [
            'name' => 'Name',
            'abilities' => 'Abilities (CSV)',
            'abilities_help' => 'Example: leads.read, leads.write',
            'cancel' => 'Cancel',
            'save' => 'Save',
        ],

        'token_once' => [
            'title' => 'Copy this token now',
            'description' => 'For security, this token will be shown only once.',
        ],

        'delete' => [
            'title' => 'Revoke token',
            'warning' => 'Warning: This action cannot be undone.',
            'confirm_help' => 'To confirm revocation, please type :word below:',
            'placeholder' => 'Type :word',
            'delete_permanently' => 'Revoke token',
        ],

        'messages' => [
            'success_title' => 'Success',
            'error_title' => 'Error',
            'created_success' => 'Token created successfully!',
            'deleted_success' => 'Token revoked successfully!',
            'error_not_found' => 'Token not found.',
        ],
    ],

    'profile' => [
        'updated' => 'Profile updated.',
        'password_updated' => 'Password updated.',
        'edit_title' => 'Edit profile',
        'edit_description' => 'Update your name, email and profile picture.',
        'edit_button' => 'Edit',
        'picture' => 'Profile picture',
        'upload' => 'Upload',
        'upload_helper' => 'PNG, JPG, GIF (max 2MB)',
        'name' => 'Name',
        'email' => 'Email',
        'save' => 'Save',
        'saving' => 'Saving...',
        'change_password_title' => 'Change password',
        'change_password_description' => 'Choose a strong password to keep your account secure.',
        'security_button' => 'Security',
        'current_password' => 'Current Password',
        'current_password_incorrect' => 'The current password is incorrect.',
        'new_password' => 'New Password',
        'confirm_new_password' => 'Confirm New Password',
        'update' => 'Update',
        'updating' => 'Updating...',
        'avatar_alt' => 'Avatar',
    ],

    'auth' => [
        'login' => [
            'title' => 'Login',
            'heading' => 'Login',
            'subtitle' => 'Access your account.',
            'email' => 'Email',
            'password' => 'Password',
            'forgot_password' => 'Forgot your password?',
            'remember_me' => 'Remember me',
            'submit' => 'Login',
            'no_account' => 'Don’t have an account?',
            'register_link' => 'Register',
        ],

        'register' => [
            'title' => 'Register',
            'heading' => 'Register',
            'subtitle' => 'Create your account.',
            'name' => 'Full Name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Confirm Password',
            'submit' => 'Create account',
            'have_account' => 'Already have an account?',
            'login_link' => 'Login',
        ],

        'forgot_password' => [
            'title' => 'Forgot password',
            'heading' => 'Forgot your password?',
            'subtitle' => 'Enter your email to receive a password reset link.',
            'email' => 'Email',
            'submit' => 'Send reset link',
            'back_to_login' => 'Back to login',
            'status_sent' => 'If this email is registered, we will send you a link to reset your password.',
            'status_error' => 'Unable to send the reset link. Please try again.',
        ],

        'reset_password' => [
            'title' => 'Reset password',
            'heading' => 'Reset password',
            'subtitle' => 'Choose a new password for your account.',
            'email' => 'Email',
            'password' => 'New Password',
            'password_confirmation' => 'Confirm New Password',
            'submit' => 'Reset password',
        ],

        'verify_email' => [
            'title' => 'Verify email',
            'heading' => 'Verify your email',
            'subtitle' => 'We sent a verification link to your email address. Please verify to continue.',
            'status_sent' => 'A new verification link has been sent to your email.',
            'resend' => 'Resend verification email',
            'logout' => 'Logout',
        ],
    ],

    'settings' => [
        'title' => 'Settings',
        'subtitle' => 'Manage company information and branding.',
        'save' => 'Save',
        'saved' => 'Settings saved.',
        'company_name' => 'Company name',
        'logo_light' => 'Logo (Light)',
        'logo_dark' => 'Logo (Dark)',
        'favicon' => 'Favicon',
        'auth_side_image' => 'Side image (Authentication)',
        'current' => 'Current',
    ],
];
