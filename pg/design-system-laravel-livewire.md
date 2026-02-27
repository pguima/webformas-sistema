# Design System - Laravel 12 + Livewire 4

## 1. Visão Geral do Projeto

### Role & Missão
Arquiteto de Design System e Lead UI/UX Engineer, especialista em interfaces SaaS de luxo e dashboards administrativas de alta performance. Missão: projetar um ecossistema visual que transmita sobriedade, modernidade e profissionalismo extremo.

### Objetivo
Criar um Design System completo para Laravel 12 e Livewire 4, onde a estética seja pautada pela elegância técnica, minimalismo e refinamento de detalhes. O resultado deve ser visualmente superior a dashboards genéricas, assemelhando-se a produtos premium (ex: Stripe, Linear, Notion).

### Stack Técnico
- **Framework**: Laravel 12 + Livewire 4 (SPA mode via `wire:navigate`)
- **Estilização**: TailwindCSS estruturado com Design Tokens
- **Gráficos**: Larapex Charts personalizados
- **Reatividade**: Alpine.js para componentes de UI (modais, dropdowns, tooltips)
- **Ícones**: Lucide, Heroicons ou Phosphor (outline/line, stroke: 2px)

---

## 2. Design Tokens

### 2.1 Paleta de Cores

#### Cores Primárias
```css
--color-primary: #009d46;          /* verde principal */
--color-primary-hover: #008a3d;
--color-primary-light: #E6F5ED;
--color-primary-dark: #007a35;
```

#### Cores de Superfície
```css
--surface-page: #FAFAFA;           /* fundo principal */
--surface-card: #FFFFFF;           /* cards, modais */
--surface-sidebar: #F7F7F7;
--surface-elevated: #FFFFFF;       /* elementos elevados */
--surface-hover: #F5F5F5;
--surface-selected: #E6F5ED;
```

#### Cores de Texto
```css
--text-primary: #1A1A1A;
--text-secondary: #6B6B6B;
--text-muted: #9E9E9E;
--text-on-primary: #FFFFFF;
--text-on-dark: #FFFFFF;
--text-link: #009d46;
```

#### Cores de Borda
```css
--border-default: #E5E5E5;
--border-subtle: #F0F0F0;
--border-focus: #009d46;
--border-hover: #D4D4D4;
```

#### Cores de Status
```css
--status-success: #10B981;         /* verde */
--status-success-light: #D1FAE5;
--status-warning: #F59E0B;         /* amarelo/laranja */
--status-warning-light: #FEF3C7;
--status-error: #EF4444;           /* vermelho */
--status-error-light: #FEE2E2;
--status-info: #3B82F6;            /* azul */
--status-info-light: #DBEAFE;
```

#### Cores de Tags/Labels
```css
--tag-purple: #E6F5ED;
--tag-purple-text: #009d46;
--tag-green: #D1FAE5;
--tag-green-text: #059669;
--tag-blue: #DBEAFE;
--tag-blue-text: #2563EB;
--tag-pink: #FCE7F3;
--tag-pink-text: #DB2777;
--tag-orange: #FED7AA;
--tag-orange-text: #EA580C;
--tag-red: #FEE2E2;
--tag-red-text: #DC2626;
```

### 2.2 Tipografia

#### Família de Fontes
```css
--font-family-base: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', 'Roboto', sans-serif;
--font-family-mono: 'SF Mono', Monaco, 'Cascadia Code', monospace;
```

#### Tamanhos (Scale)
```css
--text-xs: 11px;    /* 0.688rem */
--text-sm: 13px;    /* 0.813rem */
--text-base: 14px;  /* 0.875rem */
--text-md: 15px;    /* 0.938rem */
--text-lg: 16px;    /* 1rem */
--text-xl: 18px;    /* 1.125rem */
--text-2xl: 24px;   /* 1.5rem */
--text-3xl: 32px;   /* 2rem */
--text-4xl: 40px;   /* 2.5rem */
```

#### Pesos
```css
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
```

#### Line Heights
```css
--leading-tight: 1.2;
--leading-normal: 1.5;
--leading-relaxed: 1.75;
```

**Diretrizes**: O espaçamento entre linhas e o kerning devem ser otimizados para legibilidade em dashboards de dados densos.

### 2.3 Espaçamentos (Grid Base: 8px)
```css
--space-0: 0px;
--space-1: 4px;
--space-2: 8px;
--space-3: 12px;
--space-4: 16px;
--space-5: 20px;
--space-6: 24px;
--space-8: 32px;
--space-10: 40px;
--space-12: 48px;
--space-16: 64px;
--space-20: 80px;
--space-24: 96px;
```

### 2.4 Bordas

#### Border Radius
```css
--radius-none: 0px;
--radius-sm: 4px;
--radius-md: 6px;
--radius-lg: 8px;
--radius-xl: 12px;
--radius-2xl: 16px;
--radius-full: 9999px;
```

#### Border Width
```css
--border-width-0: 0px;
--border-width-1: 1px;
--border-width-2: 2px;
--border-width-4: 4px;
```

**Diretrizes**: Evite bordas pesadas; utilize raios de borda consistentes e sombras extremamente suaves.

### 2.5 Sombras
```css
--shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
--shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.08), 
             0 1px 2px -1px rgba(0, 0, 0, 0.08);
--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 
             0 2px 4px -2px rgba(0, 0, 0, 0.08);
--shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 
             0 4px 6px -4px rgba(0, 0, 0, 0.08);
--shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 
             0 8px 10px -6px rgba(0, 0, 0, 0.08);
```

### 2.6 Estados Interativos

#### Transições
```css
--transition-fast: 150ms ease;
--transition-base: 200ms ease;
--transition-slow: 300ms ease;
```

#### Opacidades
```css
--opacity-disabled: 0.4;
--opacity-muted: 0.6;
--opacity-hover: 0.8;
```

#### Estados Globais
```css
disabled:
  opacity: var(--opacity-disabled);
  cursor: not-allowed;
  pointer-events: none;

loading:
  opacity: var(--opacity-muted);
  cursor: wait;
```

**Diretrizes**: Cada componente deve possuir estados visuais refinados (hover, active, disabled) com transições de opacidade e escala quase imperceptíveis, mas que confirmem a ação do usuário.

### 2.7 Layout & Grid

#### Container
```css
max-width: 1440px;
margin: 0 auto;
padding: 0 var(--space-6);
```

#### Grid Gap
```css
--gap-sm: var(--space-3);
--gap-md: var(--space-4);
--gap-lg: var(--space-6);
--gap-xl: var(--space-8);
```

#### Breakpoints (Responsivo)
```css
--breakpoint-sm: 640px;
--breakpoint-md: 768px;
--breakpoint-lg: 1024px;
--breakpoint-xl: 1280px;
--breakpoint-2xl: 1536px;
```

---

## 3. Componentes

### 3.1 Botões

#### Primary Button
```css
background: var(--color-primary);
color: var(--text-on-primary);
padding: var(--space-2) var(--space-4);
border-radius: var(--radius-md);
font-weight: var(--font-medium);
font-size: var(--text-sm);
height: 36px;
border: none;
box-shadow: var(--shadow-xs);

hover:
  background: var(--color-primary-hover);
  box-shadow: var(--shadow-sm);
```

#### Secondary Button
```css
background: var(--surface-card);
color: var(--text-primary);
padding: var(--space-2) var(--space-4);
border-radius: var(--radius-md);
border: 1px solid var(--border-default);
font-weight: var(--font-medium);
font-size: var(--text-sm);
height: 36px;

hover:
  background: var(--surface-hover);
  border-color: var(--border-hover);
```

#### Icon Button
```css
width: 32px;
height: 32px;
border-radius: var(--radius-md);
background: transparent;
color: var(--text-secondary);

hover:
  background: var(--surface-hover);
  color: var(--text-primary);
```

### 3.2 Cards

#### Card Base
```css
background: var(--surface-card);
border-radius: var(--radius-lg);
padding: var(--space-6);
box-shadow: var(--shadow-sm);
border: 1px solid var(--border-subtle);

hover:
  box-shadow: var(--shadow-md);
  border-color: var(--border-default);
```

**Diretrizes**: Cards com bordas sutis e, se apropriado ao tema, fundo "glassmorphism" leve.

#### Card de Projeto (Grande)
```css
padding: var(--space-6);
border-radius: var(--radius-xl);
min-height: 240px;
display: flex;
flex-direction: column;
gap: var(--space-4);
```

#### Card de Tarefa (Kanban)
```css
padding: var(--space-4);
border-radius: var(--radius-lg);
background: var(--surface-card);
box-shadow: var(--shadow-sm);
border: 1px solid var(--border-subtle);
margin-bottom: var(--space-3);
```

### 3.3 Tags/Badges
```css
padding: var(--space-1) var(--space-3);
border-radius: var(--radius-md);
font-size: var(--text-xs);
font-weight: var(--font-medium);
display: inline-flex;
align-items: center;
gap: var(--space-1);

/* Variantes de cor conforme --tag-* definidos acima */
```

**Diretrizes**: Micro-badges minimalistas em sidebars e componentes.

### 3.4 Progress Bar
```css
height: 8px;
border-radius: var(--radius-full);
background: var(--surface-hover);
overflow: hidden;

.progress-fill:
  height: 100%;
  border-radius: var(--radius-full);
  transition: width 0.3s ease;
  /* cor depende do projeto/status */
```

### 3.5 Inputs
```css
height: 40px;
padding: var(--space-2) var(--space-3);
border: 1px solid var(--border-default);
border-radius: var(--radius-md);
font-size: var(--text-sm);
background: var(--surface-card);
color: var(--text-primary);

placeholder:
  color: var(--text-muted);

focus:
  outline: none;
  border-color: var(--border-focus);
  box-shadow: 0 0 0 3px rgba(107, 92, 231, 0.1);
```

**Diretrizes**: Inputs de foco destacado com validações inline elegantes.

### 3.6 Avatares
```css
width: 32px;
height: 32px;
border-radius: var(--radius-full);
border: 2px solid var(--surface-card);
overflow: hidden;

/* Quando empilhados: margin-left: -8px */
```

### 3.7 Sidebar/Navigation
```css
background: var(--surface-sidebar);
padding: var(--space-6) var(--space-4);
border-right: 1px solid var(--border-subtle);
width: 240px;

.nav-item:
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  
  hover:
    background: var(--surface-hover);
    color: var(--text-primary);
  
  active:
    background: var(--color-primary-light);
    color: var(--color-primary);
```

**Diretrizes**: Sidebar colapsável com ícones minimalistas e micro-badges.

### 3.8 Navbar
**Diretrizes**: Navbar com navegação breadcrumb elegante e menus de perfil.

### 3.9 Coluna Kanban
```css
background: var(--surface-hover);
border-radius: var(--radius-lg);
padding: var(--space-4);
min-width: 280px;
max-width: 320px;

.column-header:
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding-bottom: var(--space-3);
  margin-bottom: var(--space-3);
  border-bottom: 1px solid var(--border-subtle);
```

### 3.10 Dropdown/Select
```css
min-width: 180px;
padding: var(--space-2);
background: var(--surface-card);
border: 1px solid var(--border-default);
border-radius: var(--radius-md);
box-shadow: var(--shadow-md);

.dropdown-item:
  padding: var(--space-2) var(--space-3);
  border-radius: var(--radius-sm);
  font-size: var(--text-sm);
  
  hover:
    background: var(--surface-hover);
```

### 3.11 Tabelas
**Diretrizes**: Tabelas com zebra-striping suave e ações rápidas no hover.

### 3.12 Gráficos (Larapex Charts)
**Diretrizes**: Configurados com gradientes e tooltips customizados, fontes e cores sincronizadas com o tema.

---

## 4. Diretrizes de Estética e Design (UX/UI)

### Estilo Visual
**Abordagem "Clean & Professional"**: Evite bordas pesadas; utilize raios de borda (border-radius) consistentes e sombras extremamente suaves.

### Design Tokens
Todas as cores, espaçamentos e sombras devem ser declarados via CSS Variables no `:root` e `.dark`. Utilize uma paleta de neutros (slates/grays) equilibrada com cores de destaque (accent colors) vibrantes, porém profissionais.

### Tema Dark/Light Mode
- Implementar toggle Dark/Light mode com persistência de estado
- Transição suave de cores entre modos
- Todas as variáveis CSS devem ter versões para `:root` e `.dark`

### Interatividade
Cada componente deve possuir estados visuais refinados (hover, active, disabled) com transições de opacidade e escala quase imperceptíveis, mas que confirmem a ação do usuário.

---

## 5. Regras de Implementação

### 5.1 Estrutura de Rotas
- **Escopo do DS**: Estas regras aplicam-se apenas às rotas e páginas do Design System em `/design-system/*`.
- **Rotas (DS)**: Apenas `Route::view` para o Design System em `web.php`.
- **App real**: Rotas fora do escopo do DS podem usar classes Livewire e lógica de negócio (ex: `/users`).

### 5.2 Componentes
- **Blade Components** anônimos em `resources/views/design-system/components/`
- Estrutura de pastas organizada

### 5.3 Internacionalização (i18n)
- Todo texto deve utilizar as chaves de tradução `__()`

### 5.4 Código
- Código limpo, modular e documentado internamente
- Definição completa do arquivo `tailwind.config.js` ou CSS de entrada com os Design Tokens

---

## 6. Formato de Saída

### Estrutura de Pastas Sugerida
```
resources/views/design-system/
├── components/
│   ├── buttons/
│   ├── cards/
│   ├── forms/
│   ├── navigation/
│   ├── tables/
│   └── charts/
├── layouts/
└── pages/
```

### Arquivo de Tokens CSS
Criar arquivo base com todas as CSS Variables no `:root` e versões `.dark` para cada token de cor, mantendo tokens de espaçamento, tipografia e outros inalterados entre temas.

### TailwindCSS Config
Configurar `tailwind.config.js` para utilizar os Design Tokens definidos, garantindo consistência em toda a aplicação.

---

## 7. Checklist de Componentes Requisitados

- [ ] Sidebar colapsável com ícones minimalistas e micro-badges
- [ ] Navbar com navegação breadcrumb elegante e menus de perfil
- [ ] Cards com bordas sutis e fundo "glassmorphism" leve (se apropriado)
- [ ] Formulários com inputs de foco destacado e validações inline elegantes
- [ ] Tabelas com zebra-striping suave e ações rápidas no hover
- [ ] Larapex Charts configurados com gradientes e tooltips customizados
- [ ] Botões (Primary, Secondary, Icon)
- [ ] Tags/Badges
- [ ] Progress Bar
- [ ] Avatares
- [ ] Colunas Kanban
- [ ] Dropdowns/Selects

---

## 8. Referências de Qualidade

O resultado final deve ser visualmente superior a dashboards genéricas, assemelhando-se a produtos premium como:
- **Stripe Dashboard**
- **Linear**
- **Notion**

---

## 9. Ícones

### Especificações
- **Tamanho padrão**: 16px ou 20px
- **Stroke width**: 2px
- **Estilo**: outline/line (não filled)
- **Biblioteca sugerida**: Lucide, Heroicons, ou Phosphor

---

**Este documento serve como especificação completa do Design System para Laravel 12 + Livewire 4, unificando diretrizes estéticas, tokens de design e padrões de implementação.**
