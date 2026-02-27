### ROLE
Atue como um Engenheiro de Software Sênior e Arquiteto de Sistemas Fullstack, especialista no ecossistema Laravel (v12) e TALL Stack (Tailwind, Alpine.js, Laravel, Livewire v4). Possui domínio profundo em Design Systems escaláveis, Atomic Design e arquitetura de componentes reutilizáveis.

### OBJETIVO
Projetar e codificar um Design System (DS) completo e funcional para uma Dashboard Administrativa SaaS, garantindo separação total entre os ativos do DS e a lógica de negócio futura. O sistema deve ser puramente declarativo, utilizando `Route::view` e Blade Components anônimos para maximizar a performance e portabilidade.

### CONTEXTO TÉCNICO E STACK
- Framework: Laravel 12 (especificações de vanguarda).
- Reatividade: Livewire 4 (utilizando novos hooks e persistência de estado).
- Estilização: TailwindCSS estruturado exclusivamente via CSS Variables (Design Tokens).
- Gráficos: Larapex Charts (integração dinâmica com suporte a temas).
- Navegação: Single Page Application (SPA) feel via wire:navigate.

### DIRETRIZES DE ARQUITETURA E REGRAS RÍGIDAS
1. ESCOPO DO DS: As regras abaixo aplicam-se apenas ao Design System (páginas e componentes em `resources/views/design-system/**` e rotas em `/design-system/*`). O aplicativo real pode ter rotas e código com lógica de negócio (ex: `/users`).
2. SEM LOGICA DE BACKEND (NO DS): Proibida a criação de Controllers ou Classes Livewire dentro do escopo do Design System. Toda a interatividade do DS deve ser resolvida via Alpine.js ou propriedades nativas do Livewire 4 nos templates Blade.
3. ROTAS PURAS (NO DS): Definir rotas do DS no formato `Route::view('/design-system', 'design-system.index');`.
3. COMPONENTIZAÇÃO: Criar Blade Components sem classe (anônimos) localizados em `resources/views/design-system/components/`.
4. DESIGN TOKENS: O arquivo CSS deve mapear cores (primary, secondary, success, danger, warning, surface, background) para variáveis CSS no `:root` e na classe `.dark`.
5. ESTADO VISUAL: Implementar `wire:cloak` e transições suaves de layout para evitar Layout Shift.

### COMPONENTES REQUISITADOS (BIBLIOTECA)
O Design System deve conter páginas de demonstração para:
- Navegação: Sidebar colapsável (mobile-first), Navbar funcional com dropdowns de perfil e internacionalização (i18n).
- Inputs e Forms: Suporte a validações visuais nativas, Toggle Switches, Selects avançados e File Upload.
- Dados: Tabelas responsivas (modos List/Grid), Badges de status e Progress Bars.
- Feedback: Modais, Offcanvas, Toasts e Tooltips (via Alpine.js).
- Visualização: Integração completa de Larapex Charts (Line, Area, Bar, Pie, Radial) sincronizados com o tema (Light/Dark).
- Avançado: Componente Kanban funcional (drag-and-drop conceitual via Alpine).

### ESTRUTURA DE ARQUIVOS ESPERADA
- resources/views/layouts/layout-ds.blade.php (Layout base)
- resources/views/design-system/index.blade.php (Dashboard principal)
- resources/views/design-system/pages/ (Subpáginas por categoria de componente)
- resources/views/design-system/components/ (Biblioteca de UI anônima)

### PROTOCOLO DE SAÍDA (DEFINITION OF DONE)
1. Código compacto e modular, seguindo as PSRs e padrões modernos do Tailwind.
2. Uso obrigatório de helpers de tradução `__('key')` em 100% das strings.
3. Implementação de Toggle de Tema (Light/Dark) persistente.
4. Documentação inline mínima explicando como instanciar cada componente.

### ANTI-ALUCINAÇÃO E SEGURANÇA
- Caso uma funcionalidade do Laravel 12 ou Livewire 4 seja ambígua, priorize a sintaxe de maior compatibilidade futura documentada ou utilize o padrão estável mais recente.
- Não invente bibliotecas externas; utilize apenas as especificadas (Laravel, Livewire, Alpine, Larapex).