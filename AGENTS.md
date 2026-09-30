# AGENTS.md — GOSKI Laravel

Regras obrigatórias para qualquer agente (principal ou subagente) trabalhando neste repositório.

## 1. Modelo dos subagentes

Os arquivos em `.opencode/agents/` (`planner`, `builder`, `reviewer`, `orchestrator`)
**não** fixam modelo (`model:`). Sem essa chave, o subagente herda o modelo
escolhido no chat da sessão principal. Nunca reintroduza `model:` fixo nesses
arquivos — foi a causa de falhas `Model unavailable` no passado.

## 2. Skills — quando usar

Carregue a skill com a ferramenta `skill` sempre que a tarefa corresponder.
Elas vivem em `.opencode/skills/`:

| Skill | Quando usar |
|---|---|
| `goski-rules` | SEMPRE que criar branch, commitar, abrir PR ou discutir workflow git |
| `goski-testing` | SEMPRE que escrever ou rodar testes (PHPUnit) |
| `goski-supabase` | Qualquer mudança em services, queries, schema `laravel`, RLS ou storage |
| `goski-components` | Qualquer mudança em Blade/Tailwind |
| `goski-pipeline` | Quando o usuário pedir `pipeline`, `workflow`, `full cycle` ou implementação de ponta a ponta |

## 3. Pipeline (ciclo completo)

Ordem fixa, sem pular etapas: **Plan → Build → Review → Orchestrate → Verify CI**.

1. **Plan** (`planner`, read-only): breakdown atômico ordenado. Apresentar ao
   usuário e só avançar com aprovação.
2. **Build** (`builder`, por sub-tarefa): TDD — teste primeiro (Red), código
   mínimo (Green), refactor. Rodar testes após cada sub-tarefa; só avançar
   com tudo verde.
3. **Review** (`reviewer`): roda testes + checa as 4 skills. Apresentar o
   relatório (APPROVED/FAILED) ao usuário. FAILED crítico → volta ao Build.
4. **Orchestrate** (`orchestrator`, só git): branch, staging, apresenta commits
   para aprovação, commita, abre PR. **Nunca commitar sem aprovação explícita.**
5. **Verify CI**: aguardar checks verdes na PR. **NENHUM merge sem CI verde.**
   Merge na `main` dispara o CD (`deploy.yml`) — só após confirmação.

## 4. Git — nomenclatura e workflow

Branch: `@{username}/{issue-number}/{type}/{kebab-case-name}`
Tipos: `feat`, `fix`, `chore`, `refactor`, `style`.
Ex.: `@CarlosEGoulart/75/fix/carto-api-key-required`

Commit (Conventional Commits): `<type>(<scope>): <subject>`
Tipos: `feat`, `fix`, `chore`, `refactor`, `style`, `build`, `test`, `docs`.
Commits atômicos mínimos (um `fix:` por bugfix, `test:` separado só para
testes de código já lançado).

Proibido: push direto na `main`, `git add .`, `--force`, `--amend`,
`--no-verify`, rebase em branch compartilhada. O usuário faz push manual;
crie a branch local + PR e aguarde.

Toda PR de correção fecha a issue correspondente (`Closes #N` no corpo).

## 5. Testes (PHPUnit)

- `tests/Feature/` e `tests/Unit/`; SQLite in-memory (`phpunit.xml`).
- `Http::fake()` para **todo** HTTP externo (Supabase REST, Nominatim, CARTO).
  Nunca chamar serviços reais em teste.
- Comandos: `php vendor/bin/phpunit --filter="NomeDoTeste"` (rápido) ou
  `php artisan test` (completo).
- Testes de view que renderizam layout exigem stub de manifest Vite igual ao
  CI (`ci.yml` etapa "Ensure Vite manifest exists"); sem ele, falhas de
  `Vite manifest not found` são esperadas e não relacionadas à mudança.
- Padrão de qualidade: `vendor/bin/pint --test` (114+ arquivos PASS) e
  `vendor/bin/phpstan analyse -c phpstan.neon` sem erros antes de qualquer PR.

## 6. Supabase / dados

- Todas as tabelas no schema `laravel`. Em queries diretas:
  `$prefix = DB::getDriverName() === 'pgsql' ? 'laravel.' : ''`.
- Leitura complexa: `DB::table()` direto. Escrita: REST via `$this->client()`
  de `SupabaseBaseService` (headers `Accept-Profile`/`Content-Profile: laravel`).
- Inserts sempre com `created_at` explícito. Lógica de negócio em
  `app/Services/`, nunca em controllers/views.
- Contagens: usar `Prefer: count=exact` (ler `Content-Range`) — **nunca**
  baixar listas inteiras para contar em PHP.
- Ações com múltiplos HTTPs sequenciais (ex.: toggle like = has+write+count)
  devem ser reduzidas ao mínimo de round-trips.
- Migrations novas seguem o padrão de prefixo condicional pgsql/sqlite e
  precisam subir/descer em sqlite (teste local).

## 7. Frontend (Blade + Tailwind v4)

Paleta zinc, `rounded-xl`, `dark:` em TODAS as classes de cor, ícones SVG
inline `currentColor`. Imagens de conteúdo: `loading="lazy"` + dimensões
para não quebrar CLS; nunca servir originais de 10MB sem thumbnail quando
houver alternativa.

## 8. Segredos e ambiente

- Nunca ler, exibir ou commitar valores de `.env` (local ou VPS).
  Verificações só por **nome** da variável
  (`cut -d= -f1 .env | grep ...`, `config --no-interpolate`).
- `.env` é gitignored e dockerignored. Novas envs exigem: entrada em
  `.env.example`, leitura com default seguro em `config/`, repasse em
  `docker-compose.yml` (`app` **e** `worker`) e documentação.
- Produção lê env do host via compose + `config:cache` no entrypoint:
  mudança de env exige recriar containers (`up -d`), coberto pelo CD.

## 9. Verificação antes de declarar pronto

Toda entrega passa por: testes alvo → suite relevante → `pint --test` →
`phpstan` → diff revisado (`git diff main...HEAD --stat`, sem segredos) →
CI verde na PR. Só então merge.
