# Tutorial — API de Leads

Este documento descreve como consumir a **API de Leads** do sistema.

## 1) Autenticação

A API é protegida pelo middleware `api_token`.

Envie o token em um dos formatos abaixo:

### 1.1) Authorization Bearer (recomendado)

- Header: `Authorization: Bearer SEU_TOKEN`

### 1.2) Header alternativo

- Header: `X-API-Token: SEU_TOKEN`

## 2) Permissões (abilities)

Os tokens podem ter permissões (abilities). Para Leads, as permissões usadas são:

- `leads.read`
  - Permite listar leads.
- `leads.write`
  - Permite criar, atualizar e deletar leads.

Na tela de tokens (`/settings/api-tokens`), informe as abilities como CSV, por exemplo:

```text
leads.read, leads.write
```

## 3) Modelo de dados (Lead)

Um lead (card) possui os campos principais:

- `name`
- `whatsapp`
- `plan`
- `services`
- `value`
- `responsible_user_id`
- `origin`
- `campaign`
- `stage`
- `position`
- `external_id`
- `payload`

### 3.1) Etapas (stage) válidas

O Kanban usa colunas fixas. O campo `stage` deve ser um destes valores:

- `Novo`
- `Em Contato`
- `Reunião Marcada`
- `Proposta`
- `Ganho`
- `Perdido`

## 4) Endpoints

> Observação: os exemplos abaixo assumem o servidor rodando em `http://webformas-sistema.test`.

### 4.1) Listar leads

- Método: `GET`
- Rota: `/api/leads`
- Ability necessária: `leads.read`

#### 4.1.1) Filtros (pesquisa)

Você pode filtrar/pesquisar leads usando *query params* na própria rota `/api/leads`.

Parâmetros suportados:

- `whatsapp` (match exato)
- `external_id` (match exato)
- `stage` (match exato)
- `campaign` (match exato)
- `origin` (match exato)
- `name` (parcial / `LIKE`)

Exemplos:

Buscar por WhatsApp específico:

```bash
curl -H "Authorization: Bearer SEU_TOKEN" \
  "http://webformas-sistema.test/api/leads?whatsapp=5511999999999"
```

Buscar por `external_id`:

```bash
curl -H "Authorization: Bearer SEU_TOKEN" \
  "http://webformas-sistema.test/api/leads?external_id=ABC123"
```

Buscar por nome (parcial):

```bash
curl -H "Authorization: Bearer SEU_TOKEN" \
  "http://webformas-sistema.test/api/leads?name=joao"
```

> Observação: o retorno continua sendo uma lista no formato `{ "data": [...] }`.
> Se o filtro resultar em apenas um lead, ele virá como um único item dentro de `data`.

Exemplo:

```bash
curl -H "Authorization: Bearer SEU_TOKEN" \
  http://webformas-sistema.test/api/leads
```

### 4.2) Criar lead

- Método: `POST`
- Rota: `/api/leads`
- Ability necessária: `leads.write`
- Content-Type: `application/json`

Body (campos):

- Obrigatório:
  - `name` (string)
- Opcionais:
  - `whatsapp` (string)
  - `plan` (string)
  - `services` (string)
  - `value` (number)
  - `responsible_user_id` (int)
  - `origin` (string)
  - `campaign` (string)
  - `stage` (string; default: `Novo`)
  - `external_id` (string)
  - `payload` (object)

Exemplo:

```bash
curl -X POST http://webformas-sistema.test/api/leads \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "João da Silva",
    "whatsapp": "5511999999999",
    "plan": "Gold",
    "services": "Site + SEO",
    "value": 1999.90,
    "origin": "Meta Ads",
    "campaign": "BlackFriday",
    "stage": "Novo"
  }'
```

### 4.3) Atualizar lead

- Método: `PUT`
- Rota: `/api/leads/{lead}`
- Ability necessária: `leads.write`

Exemplo:

```bash
curl -X PUT http://webformas-sistema.test/api/leads/1 \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "stage": "Em Contato",
    "responsible_user_id": 2
  }'
```

### 4.4) Deletar lead

- Método: `DELETE`
- Rota: `/api/leads/{lead}`
- Ability necessária: `leads.write`

Exemplo:

```bash
curl -X DELETE http://webformas-sistema.test/api/leads/1 \
  -H "Authorization: Bearer SEU_TOKEN"
```

## 5) Ordenação (Kanban)

A UI do Kanban controla ordenação com:

- `stage`: coluna atual
- `position`: posição do card dentro da coluna

Se sua integração precisar definir ordem, você pode enviar `position` no `PUT`.

## 6) Erros comuns

- **401 / 403**
  - Token ausente/ inválido, ou abilities insuficientes.
- **422**
  - Erro de validação (ex.: `name` ausente, `responsible_user_id` inválido).

## 7) Checklist rápido para integração

- Gerar token em `/settings/api-tokens` (SuperAdmin)
- Incluir `Authorization: Bearer ...` nas requisições
- Usar `leads.read` para leitura e `leads.write` para escrita
- Respeitar os valores fixos de `stage`
