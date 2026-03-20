# API Bíblia – Documentação

API central do módulo Bible. Todas as rotas estão sob o prefixo **`/api/v1/bible`**. Respostas seguem o formato `{ "data": ... }`. Throttle: 60 requisições por minuto por IP.

## Endpoints

### GET `/api/v1/bible/versions`

Lista versões ativas da Bíblia (ordenadas: padrão primeiro, depois nome).

**Query:** nenhuma.

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Nova Versão Internacional",
      "abbreviation": "NVI",
      "is_default": true
    }
  ]
}
```

---

### GET `/api/v1/bible/books`

Livros de uma versão.

**Query:**

| Parâmetro    | Tipo   | Obrigatório | Descrição                          |
|-------------|--------|-------------|------------------------------------|
| `version_id`| integer| Não         | ID da versão. Se omitido, usa a padrão. |

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gênesis",
      "abbreviation": "Gn",
      "book_number": 1,
      "testament": "old"
    }
  ]
}
```

---

### GET `/api/v1/bible/chapters`

Capítulos de um livro.

**Query:**

| Parâmetro   | Tipo   | Obrigatório | Descrição |
|------------|--------|-------------|-----------|
| `book_id`  | integer| Sim*        | ID do livro. |
| `book_name`| string | Sim*        | Nome do livro (usado com `version_id`). |
| `version_id` | integer | Não      | ID da versão (obrigatório se usar `book_name`). |

\* É obrigatório enviar `book_id` **ou** (`book_name` e `version_id`).

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "chapter_number": 1,
      "total_verses": 31
    }
  ]
}
```

---

### GET `/api/v1/bible/verses`

Versículos de um capítulo.

**Query:**

| Parâmetro        | Tipo   | Obrigatório | Descrição |
|-----------------|--------|-------------|-----------|
| `chapter_id`    | integer| Sim*        | ID do capítulo. |
| `book_id`       | integer| Sim*        | ID do livro (usado com `chapter_number`). |
| `chapter_number`| integer| Sim*        | Número do capítulo. |
| `verse_range`   | string | Não         | Ex.: `1-5`, `1,3,5-10` para filtrar versículos. |

\* É obrigatório enviar `chapter_id` **ou** (`book_id` e `chapter_number`).

**Resposta 200:**
```json
{
  "data": [
    {
      "id": 1,
      "verse_number": 1,
      "text": "No princípio Deus criou os céus e a terra."
    }
  ]
}
```

---

### GET `/api/v1/bible/find`

Busca por referência (ex.: "João 3:16", "Salmos 23:1-3").

**Query:**

| Parâmetro | Tipo  | Obrigatório | Descrição |
|-----------|-------|-------------|-----------|
| `ref`     | string| Sim         | Referência (livro + capítulo + versículo ou intervalo). |

**Resposta 200:**
```json
{
  "data": {
    "reference": "João 3:16",
    "book": "João",
    "chapter": 3,
    "verses": [
      {
        "id": 123,
        "verse_number": 16,
        "text": "Porque Deus amou o mundo..."
      }
    ],
    "full_chapter_url": "https://..."
  }
}
```

**Resposta 404:** referência não encontrada ou formato inválido.

---

### GET `/api/v1/bible/context`

Contexto avançado para popover/UI: texto consolidado na versão padrão, termos originais (interlinear + Strong), panorama do livro e placeholder de comentário oficial.

**Query:**

| Parâmetro | Tipo  | Obrigatório | Descrição |
|-----------|-------|-------------|-----------|
| `ref`     | string| Sim         | Mesmo formato de `/find` (ex.: `João 3:16`, `Salmos 23:1-3`). |

**Resposta 200:**
```json
{
  "data": {
    "reference": "João 3:16",
    "book": "João",
    "book_number": 43,
    "chapter": 3,
    "text": "Porque Deus amou o mundo...",
    "original_language": [
      {
        "verse_id": 123,
        "position": 1,
        "word_surface": "...",
        "strong_number_raw": "G2316",
        "strong_number": "G2316",
        "morphology": "...",
        "lang": "gr",
        "lexicon": {
          "number": "G2316",
          "lang": "gr",
          "lemma": "...",
          "description_pt": "..."
        }
      }
    ],
    "panorama": {
      "author": "...",
      "date_written": "...",
      "theme_central": "...",
      "recipients": "...",
      "testament": "new"
    },
    "official_commentary": null,
    "full_chapter_url": "https://..."
  }
}
```

**Resposta 404:** referência não encontrada ou formato inválido.

---

### GET `/api/v1/bible/search`

Busca por texto ou referência. Tenta referência exata primeiro; se não achar, faz busca por texto no conteúdo.

**Query:**

| Parâmetro | Tipo  | Obrigatório | Descrição |
|-----------|-------|-------------|-----------|
| `q`       | string| Sim         | Texto ou referência. |

**Resposta 200 (referência exata):**
```json
{
  "data": {
    "type": "exact",
    "reference": "João 3:16",
    "verses": [...],
    "full_chapter_url": "https://..."
  }
}
```

**Resposta 200 (busca por texto):**
```json
{
  "data": [
    {
      "id": 123,
      "reference": "João 3:16",
      "text": "...",
      "type": "search"
    }
  ]
}
```

---

### GET `/api/v1/bible/random`

Retorna um versículo aleatório.

**Query:**

| Parâmetro    | Tipo   | Obrigatório | Descrição |
|-------------|--------|-------------|-----------|
| `version_id`| integer| Não         | Restringe à versão. |

**Resposta 200:**
```json
{
  "data": {
    "id": 123,
    "verse_number": 16,
    "text": "...",
    "chapter": { "id": 1, "chapter_number": 3 },
    "book": { "id": 43, "name": "João", "abbreviation": "Jo", "book_number": 43 }
  }
}
```

---

### GET `/api/v1/bible/compare`

Compara o mesmo trecho em duas versões.

**Query:**

| Parâmetro     | Tipo   | Obrigatório | Descrição |
|--------------|--------|-------------|-----------|
| `v1`         | mixed  | Sim         | ID ou abreviatura da versão 1. |
| `v2`         | mixed  | Sim         | ID ou abreviatura da versão 2. |
| `book_number`| integer| Sim         | Número do livro. |
| `chapter`    | integer| Sim         | Número do capítulo. |
| `verse`      | integer| Não         | Versículo específico; se omitido, retorna o capítulo inteiro. |

**Resposta 200:**
```json
{
  "data": {
    "v1": {
      "abbreviation": "NVI",
      "name": "Nova Versão Internacional",
      "verses": [...]
    },
    "v2": {
      "abbreviation": "ARA",
      "name": "Almeida Revista e Atualizada",
      "verses": [...]
    }
  }
}
```

---

## Uso interno (backend)

Para leitura no backend (ex.: HomePage, Academy, Sermons), use o **`BibleApiService`**:

```php
use Modules\Bible\App\Services\BibleApiService;

$bibleApi = app(BibleApiService::class);

$versions = $bibleApi->getVersions();
$books = $bibleApi->getBooks($versionId);
$chapters = $bibleApi->getChapters($bookId);
$verses = $bibleApi->getVerses($chapterId, null, null, '1-5');
$result = $bibleApi->findByReference('João 3:16');
$verse = $bibleApi->getRandomVerse($versionId);
```

Para **detectar citações em texto livre** e gerar HTML com botões `.bible-reference-link` (popover global no painel de membros), use o **`BibleReferenceParserService`**:

```php
use Modules\Bible\App\Services\BibleReferenceParserService;

$html = app(BibleReferenceParserService::class)->parseText($conteudoBrutoOuHtml);
```

Ou o trait **`HasBibleReferences`** em models: `parseBibleReferences($text)`.

Após import/atualização de dados, limpe o cache:

```php
$bibleApi->clearCache();
```

--------------- Proximos passos ---------------
# Épico: Módulo BIBLE como Coração do Ecossistema VEPL (Escola de Pastores)

Você é um Arquiteto de Software Sênior e Especialista em Laravel 12, Tailwind CSS v4.1 e Alpine.js.

Sua missão é dar um upgrade profundo no módulo `Bible`, transformando-o no **Motor Central** de toda a plataforma VEPL. O objetivo é que qualquer citação bíblica (ex: "Salmos 3:1-8" ou "@João 3:16") escrita em **QUALQUER** módulo (Academy, Sermons, Community, Intercessor) seja automaticamente reconhecida, convertida em um elemento interativo e, ao passar o mouse (ou clicar no mobile), exiba um Popover/Modal avançado com o texto, termos originais (Strong's), contexto e comentários oficiais, sem sair da página.

## Regra de Ouro (CRÍTICO)
**IMUTABILIDADE DA TRADUÇÃO:** As tabelas de textos bíblicos base (`bible_versions`, `books`, `chapters`, `verses`) são estritamente **READ-ONLY** na aplicação final. A edição no painel Admin é permitida APENAS para os metadados, dicionário Strong (`bible_strongs_lexicon`, `bible_strongs_corrections`), panoramas (`bible_book_panoramas`) e notas/comentários de estudo oficiais.

---

## PASSO 1: O Cérebro - `BibleReferenceParserService` (Backend)
1. Crie um serviço global no módulo Bible: `Modules/Bible/app/Services/BibleReferenceParserService.php`.
2. Este serviço deve ter um método `parseText(string $text): string`.
3. Use expressões regulares (Regex) otimizadas para o português para detectar padrões de referências bíblicas (ex: "1 João 3:16", "Salmos 23:1-4", "@Rm 8:1").
4. Substitua a string encontrada por um componente Blade ou HTML puro no formato:
   `<button type="button" class="bible-reference-link text-amber-600 hover:underline font-semibold cursor-pointer transition-colors" data-reference="1 João 3:16">1 João 3:16</button>`
5. Crie um Trait `HasBibleReferences` que possa ser adicionado em Models de outros módulos (ex: `Sermon`, `ForumTopic`, `Lesson`) para fazer o parse automático no mutator ou na view.

---

## PASSO 2: A API de Contexto Bíblico Avançado
1. Crie ou atualize o Controller `Modules/Bible/app/Http/Controllers/Api/V1/BibleContextApiController.php`.
2. Crie um endpoint (ex: `/api/v1/bible/context?ref=1 João 3:16`) que receba a string de referência e retorne um JSON rico contendo:
   - `reference`: O título da passagem.
   - `text`: O texto bíblico consolidado (da versão padrão, ex: Almeida Recebida).
   - `original_language`: Dados cruzados com as tabelas de interlinear e Strong (`bible_interlinear_notes`, `bible_strongs_lexicon`, `bible_strongs_corrections`), listando as palavras-chave hebraicas/gregas do versículo solicitado.
   - `panorama`: Resumo do livro (`bible_book_panoramas`).
   - `official_commentary`: Comentário teológico oficial aprovado pela plataforma (buscando de `bible_metadata` ou notas oficiais).

---

## PASSO 3: A Mágica do Front-end (Alpine.js + Tailwind v4.1)
1. Crie um componente global Blade: `Modules/Bible/resources/views/components/bible-popover.blade.php`.
2. Este arquivo deve usar Alpine.js (`x-data="biblePopover"`) para gerenciar o estado global de citações.
3. A lógica Alpine deve escutar eventos `mouseover` e `click` (para touch) em elementos com a classe `.bible-reference-link`.
4. Ao acionar:
   - Exibir um Popover/Modal flutuante (usando `absolute`, `z-50`, `bg-slate-900`, `shadow-xl`, `rounded-xl`).
   - Enquanto a API não responde, exibir um *Skeleton Loading* bonito.
   - Quando o JSON chegar do Passo 2, popular o Popover com abas (Tabs) internas (Ex: "Texto", "Original/Strongs", "Contexto/Comentário").
5. Garanta que este componente seja importado no layout master principal do painel de membros (`master.blade.php`) para que funcione globalmente.

---

## PASSO 4: Upgrade do Painel Admin (O Estúdio Teológico)
No módulo `Admin`, crie as telas e lógicas para gerenciar o ecossistema de estudo. Use os componentes Blade e o layout padrão do admin atual.
1. **Gestão do Dicionário Strong e Originais:** CRUD para `bible_strongs_lexicon` e `bible_strongs_corrections`. O admin deve poder corrigir a tradução do termo original ou adicionar significados ampliados.
2. **Gestão de Panoramas:** CRUD para `bible_book_panoramas`. Permita que a liderança VEPL cadastre o autor, data, tema central e contexto histórico de cada um dos 66 livros.
3. **Gestão de Categorias/Tags de Palavras:** CRUD para `bible_word_tags` (ex: Tag "Graça", Tag "Justificação").

---

## PASSO 5: Integração Global nos Módulos Core (Nepe Search e Exibição)
1. Atualize o `SearchEngineService` no módulo `NepeSearch` para que, ao buscar por uma palavra (ex: "Batismo"), ele traga também resultados diretos do Dicionário Strong (`bible_strongs_lexicon`) e das referências onde a palavra grega original aparece.
2. Instrua a implementação de que as Views do módulo `Sermons` (`show.blade.php`) e `Community` passem os textos pelo parser criado no Passo 1 (`app(BibleReferenceParserService::class)->parseText($text)`).

**Instruções Finais para a IA:**
Execute os Passos 1, 2 e 3 agora. Retorne o código do `BibleReferenceParserService`, do `BibleContextApiController` e do componente Blade/Alpine `bible-popover.blade.php`. Preste muita atenção na performance do parser (Regex) para não deixar a renderização lenta.


--------------- Aplicação dos passos ---------------
# Bible coração VEPL
overview: Implementar os Passos 1–3 com foco em parser performático, endpoint de contexto rico e popover Alpine global no painel de membros, sem violar a imutabilidade das tabelas base de texto bíblico.
todos:
  - id: build-parser-service
    content: Criar `BibleReferenceParserService` com regex estrita PT + normalização + substituição HTML button para refs no texto.
    status: pending
  - id: add-parser-trait
    content: Criar trait `HasBibleReferences` para uso em models/views com método utilitário de parse seguro.
    status: pending
  - id: create-context-api
    content: "Implementar `BibleContextApiController@context` com agregação de texto, original/Strong, panorama e `official_commentary: null`."
    status: pending
  - id: wire-context-route
    content: Adicionar rota `GET /api/v1/bible/context` no grupo Bible API v1 mantendo throttle e padrão `{ data }`.
    status: pending
  - id: build-popover-component
    content: Criar `bible-popover.blade.php` com Alpine global (hover/click), skeleton e tabs Texto/Original/Contexto.
    status: pending
  - id: inject-global-component
    content: Incluir componente no `master.blade.php` do MemberPanel para disponibilidade global.
    status: pending
  - id: validate-lints
    content: Revisar lints dos arquivos alterados após implementação.
    status: pending
isProject: false
---

# Plano de implementação — Passos 1–3 (Bible Motor Central)

## Objetivo

Entregar detecção global de referências bíblicas em texto livre, API de contexto avançado e UI popover/modal global, mantendo leitura estrita das tabelas base (`bible_versions`, `books`, `chapters`, `verses`).

## Decisões alinhadas

- `official_commentary`: nesta fase retornar `null` (placeholder contratual), conforme sua escolha.
- Escopo do parser: **estrito** (somente nomes/abreviações válidos de livros bíblicos) para minimizar falso positivo.

## Arquivos-alvo principais

- [c:\laragon\www\VEPL\Modules\Bible\app\Services\BibleReferenceParserService.php](../../../../../Users/Administrator/.cursor/plans/c:\laragon\www\VEPL\Modules\Bible\app\Services\BibleReferenceParserService.php)
- [c:\laragon\www\VEPL\Modules\Bible\app\Concerns\HasBibleReferences.php](../../../../../Users/Administrator/.cursor/plans/c:\laragon\www\VEPL\Modules\Bible\app\Concerns\HasBibleReferences.php)
- [c:\laragon\www\VEPL\Modules\Bible\app\Http\Controllers\Api\V1\BibleContextApiController.php](../../../../../Users/Administrator/.cursor/plans/c:\laragon\www\VEPL\Modules\Bible\app\Http\Controllers\Api\V1\BibleContextApiController.php)
- [c:\laragon\www\VEPL\routes\api.php](../../../../../Users/Administrator/.cursor/plans/c:\laragon\www\VEPL\routes\api.php)
- [c:\laragon\www\VEPL\Modules\Bible\resources\views\components\bible-popover.blade.php](../../../../../Users/Administrator/.cursor/plans/c:\laragon\www\VEPL\Modules\Bible\resources\views\components\bible-popover.blade.php)
- [c:\laragon\www\VEPL\Modules\MemberPanel\resources\views\components\layouts\master.blade.php](../../../../../Users/Administrator/.cursor/plans/c:\laragon\www\VEPL\Modules\MemberPanel\resources\views\components\layouts\master.blade.php)

## Estratégia técnica

- Criar parser com regex pré-compiladas e mapa canônico de livros PT/abreviações para reduzir custo por chamada e evitar backtracking excessivo.
- Fazer parse textual por `preg_replace_callback` substituindo referências por `<button class="bible-reference-link" data-reference="...">...</button>`.
- Reusar resolução de referência no endpoint `/context` para montar payload único (texto consolidado, Strong/original, panorama).
- Popover Alpine global no layout master, com `mouseover` (desktop) + `click` (mobile), skeleton loading e tabs internas.

## Contrato do endpoint novo

- `GET /api/v1/bible/context?ref=João 3:16`
- Resposta:
  - `reference`: string normalizada
  - `text`: texto consolidado dos versículos
  - `original_language`: lista de termos de `bible_word_tags` + dados de `bible_strongs_lexicon` + correções aprovadas mais recentes de `bible_strongs_corrections`
  - `panorama`: dados de `bible_book_panoramas` por `book_number`
  - `official_commentary`: `null` (placeholder nesta fase)

## Observações de compatibilidade

- Não alterar escrita das tabelas base bíblicas; somente leitura.
- Não quebrar endpoint atual `/find`; parser novo será adicional/reutilizável.
- Componente global será injetado no master de MemberPanel para funcionar em qualquer módulo que renderize `.bible-reference-link`.

## Snippets de referência existentes

```16:31:c:\laragon\www\VEPL\Modules\Intercessor\App\Services\BibleParser.php
return preg_replace_callback(
    '/@([\w\sáàâãéèêíïóôõöúçñÁÀÂÃÉÈÊÍÏÓÔÕÖÚÇÑ]+)\s+(\d+):(\d+)(?:-(\d+))?/u',
    function ($matches) {
        // ...dispatch open-bible-modal...
    },
    e($text)
);
```

```49:60:c:\laragon\www\VEPL\routes\api.php
Route::middleware(['throttle:60,1'])->prefix('v1/bible')->name('bible.api.')->group(function () use ($bibleV1) {
    Route::get('/versions', [$bibleV1, 'versions'])->name('versions');
    Route::get('/books', [$bibleV1, 'books'])->name('books');
    Route::get('/chapters', [$bibleV1, 'chapters'])->name('chapters');
    Route::get('/verses', [$bibleV1, 'verses'])->name('verses');
    Route::get('/find', [$bibleV1, 'find'])->name('find');
    // ...
});
```
---------- Proximos passos ---------------
# Fase Final: Conclusão do Ecossistema BIBLE (VEPL) - Admin, Comentários e NepeSearch

Você é um Arquiteto de Software Sênior e Especialista em Laravel 12, Tailwind CSS v4.1 e Arquitetura Modular (`nwidart/laravel-modules`).

Sua missão agora é concluir o módulo `Bible` de ponta a ponta. Atualmente, a API de contexto (`BibleContextApiController`) e o Parser global já funcionam, mas o painel Admin não tem as telas para gerenciar os dados avançados (Panoramas, Strongs, Tags) e o campo `official_commentary` está retornando `null`.

Execute os passos abaixo para entregar um sistema 100% completo, profissional e integrado.

## PASSO 1: O Comentário Oficial (Preenchendo o `null` da API)
O Comentário Oficial da VEPL é gerenciado pela tabela `bible_metadata` (ou equivalente que guarde metadados por versículo/capítulo).
1. **Backend (Admin):** Crie o `BibleMetadataController` no diretório `Modules/Bible/app/Http/Controllers/Admin/`. Implemente o CRUD completo permitindo que a equipe editorial da VEPL selecione um Livro, Capítulo e Versículo, e escreva o `official_commentary` (usando o componente de rich-editor já existente no sistema).
2. **Views (Admin):** Crie as views em `Modules/Bible/resources/views/admin/metadata/` (`index.blade.php`, `create.blade.php`, `edit.blade.php`). Design limpo, focado em edição de texto longo (Tailwind v4.1, `slate-900` e `amber-600`).
3. **API Update:** Abra o `Modules/Bible/app/Http/Controllers/Api/V1/BibleContextApiController.php`. Modifique a lógica que retorna `"official_commentary" => null`. Faça a query buscar na tabela de metadados o comentário oficial correspondente à passagem solicitada (cruzando `book_id`, `chapter_id`, `verse_id`) e retorne o HTML seguro.

## PASSO 2: O Estúdio Teológico no Admin (Panoramas, Strongs e Tags)
O admin precisa gerenciar a inteligência da Bíblia.
1. **Controllers (Admin):**
   - Crie `BibleBookPanoramaController` (para gerenciar autor, data, tema, contexto dos 66 livros).
   - Crie `StrongsLexiconController` (para visualização do dicionário grego/hebraico).
   - Crie `StrongsCorrectionsController` (para a liderança adicionar correções ou sobreposições de tradução oficiais).
   - Crie `BibleWordTagsController` (para gerenciar categorias de palavras, ex: "Graça", "Salvação").
2. **Views (Admin):** Gere as views essenciais (`index` e `edit/form`) para cada um desses controllers dentro de `Modules/Bible/resources/views/admin/`.
3. **Rotas:** Registre todas essas rotas no arquivo `Modules/Bible/routes/web.php` sob o prefixo `admin/bible` e o middleware de autenticação admin.

## PASSO 3: Sidebar e Navegação Completa (Integração Total)
O administrador não pode ficar caçando links.
1. Abra o arquivo da Sidebar do Admin (geralmente em `Modules/Admin/resources/views/components/sidebar.blade.php` ou `Modules/Bible/resources/views/admin/partials/nav.blade.php`).
2. Crie ou atualize um grupo de menu chamado **"Estúdio Teológico (Bíblia)"** com ícones coerentes (ex: `fa-book-bible`, `fa-language`, `fa-tags`).
3. Adicione links diretos para:
   - Visão Geral (Livros e Versões)
   - Comentários Oficiais (`bible_metadata`)
   - Panoramas dos Livros (`bible_book_panoramas`)
   - Dicionário Original / Strongs (`bible_strongs_lexicon` / `corrections`)
   - Categorias e Tags (`bible_word_tags`)

## PASSO 4: Integração com o NepeSearch (Busca Global Teológica)
O NepeSearch precisa ficar hiper-inteligente, buscando também na Bíblia Original.
1. Abra o `SearchEngineService.php` em `Modules/NepeSearch/app/Services/`.
2. Adicione lógicas de busca simultânea para as novas bases de inteligência:
   - **Busca no Léxico:** Se o termo pesquisado bater com a transliteração grega/hebraica ou com o significado (`bible_strongs_lexicon`), retorne no grupo "Dicionário Original".
   - **Busca em Panoramas:** Pesquisar no campo de tema ou contexto histórico de `bible_book_panoramas`.
3. Certifique-se de que os resultados dessas buscas no NepeSearch chamem o Componente Popover ou redirecionem para a leitura com a aba correspondente aberta.

## Restrições e Padrões (CRÍTICO)
- **Tradução Intocável:** Nas views de Admin geradas, certifique-se de que **não há botões de "Editar Texto do Versículo"**. A tabela `verses` continua Read-Only. O Admin edita apenas o material de apoio (Comentários, Panoramas, Tags e Correções de Strong).
- **UX/UI:** Use tabelas de dados modernas no Admin. Campos de comentário longo devem usar o `<x-rich-editor>`. Padrão de cores VEPL (fundo `slate-50`, cards `white`, botões primários em tons amadeirados/dourados `amber-600` a `amber-700`).
- **Nomes em Português na UI:** Todo o texto exibido para o usuário e administrador deve estar em Português do Brasil de forma extremamente profissional.

Comece a implementação fornecendo as alterações completas da API, a atualização da Sidebar e os Controllers/Views do Admin (especialmente o de Comentários Oficiais).


------------- Aplicação dos passos ---------------
# Conclusão Bible Admin+Search
overview: Finalizar o ecossistema Bible com CRUD editorial de comentário oficial, estúdio teológico no Admin, sidebar integrada e ampliação do NepeSearch para léxico/panoramas com destino navegável.
todos:
  - id: schema-official-commentary
    content: Criar migration/model para comentário oficial por versículo com índices e regras de integridade.
    status: pending
  - id: admin-metadata-crud
    content: Implementar BibleMetadataController + views index/create/edit com x-rich-editor e filtros editoriais.
    status: pending
  - id: api-context-commentary
    content: Atualizar BibleContextApiController para preencher official_commentary de forma segura.
    status: pending
  - id: admin-theological-studio-polish
    content: Padronizar controllers/views existentes de Panoramas, Strongs e Tags para UX PT-BR consistente.
    status: pending
  - id: routes-sidebar-integration
    content: Ajustar rotas admin.bible e atualizar sidebar com grupo Estúdio Teológico (Bíblia).
    status: pending
  - id: nepesearch-bible-intelligence
    content: Expandir SearchEngineService e command-palette para léxico/panoramas com resultados clicáveis.
    status: pending
  - id: final-validation
    content: Rodar validações rápidas (rotas, lints e consistência de navegação/API) e consolidar entrega.
    status: pending

# Conclusão do Ecossistema Bible (VEPL)

## Decisões arquiteturais adotadas

- Reutilizar e evoluir os controllers já existentes em `Bible` (menor risco de regressão e maior consistência com rotas atuais `admin.bible.*`).
- Criar storage dedicado para comentário oficial por versículo (tabela nova), preservando a função original de `bible_metadata` (metadados de capítulo/contagem).

## Escopo de implementação

### 1) Comentário Oficial (API + Admin)

- Criar migration de comentários oficiais por versículo (com `book_id`, `chapter_id`, `verse_id`, `official_commentary`, `is_published`, auditoria e índices únicos para evitar duplicidade por versículo).
- Criar model e controller administrativo para CRUD editorial com `x-rich-editor`.
- Adicionar views em layout admin Bible focadas em texto longo e usabilidade editorial:
  - `index` com filtros (Livro/Capítulo/Versículo/status) e ações.
  - `create` e `edit` com formulário rico.
- Atualizar `BibleContextApiController` para buscar o comentário oficial do versículo alvo da referência e retornar conteúdo sanitizado em `official_commentary`.

Arquivos alvo principais:

- [c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Api/V1/BibleContextApiController.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Api/V1/BibleContextApiController.php)
- [c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/BibleMetadataController.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/BibleMetadataController.php)
- [c:/laragon/www/VEPL/Modules/Bible/resources/views/admin/metadata/index.blade.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/resources/views/admin/metadata/index.blade.php)
- [c:/laragon/www/VEPL/Modules/Bible/resources/views/admin/metadata/create.blade.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/resources/views/admin/metadata/create.blade.php)
- [c:/laragon/www/VEPL/Modules/Bible/resources/views/admin/metadata/edit.blade.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/resources/views/admin/metadata/edit.blade.php)
- [c:/laragon/www/VEPL/Modules/Bible/database/migrations](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/database/migrations)
- [c:/laragon/www/VEPL/Modules/Bible/app/Models](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Models)

### 2) Estúdio Teológico no Admin (Panoramas, Strongs e Tags)

- Manter controllers atuais e completar lacunas de UX e telas essenciais:
  - Panoramas: manter `BibleBookPanoramaAdminController`, padronizar rótulos PT-BR e formulário.
  - Léxico: manter `StrongsLexiconController` e consolidar view de índice/edição para curadoria.
  - Correções: manter `StrongsCorrectionsController` com fluxo claro de aprovação/rejeição.
  - Tags: manter `BibleWordTagsController` com foco em categorias/etiquetas e filtros de gestão.
- Uniformizar design (cards/tabelas, `slate-50` + `white`, botões `amber-600/700`) e remover qualquer affordance de edição de tradução bíblica.

Arquivos alvo principais:

- [c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/BibleBookPanoramaAdminController.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/BibleBookPanoramaAdminController.php)
- [c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/StrongsLexiconController.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/StrongsLexiconController.php)
- [c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/StrongsCorrectionsController.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/StrongsCorrectionsController.php)
- [c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/BibleWordTagsController.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/app/Http/Controllers/Admin/BibleWordTagsController.php)
- [c:/laragon/www/VEPL/Modules/Bible/resources/views/admin](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Bible/resources/views/admin)

### 3) Rotas e Sidebar (integração total)

- Registrar/ajustar rotas no grupo `admin.bible.*` em `routes/admin.php` para comentários oficiais e refinamentos do estúdio teológico.
- Atualizar sidebar admin com grupo explícito “Estúdio Teológico (Bíblia)” e links diretos para:
  - Visão Geral
  - Comentários Oficiais
  - Panoramas
  - Dicionário Original / Correções
  - Categorias e Tags
- Padronizar ícones com `<x-icon ...>` e nomenclatura PT-BR profissional.

Arquivos alvo principais:

- [c:/laragon/www/VEPL/routes/admin.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/routes/admin.php)
- [c:/laragon/www/VEPL/Modules/Admin/resources/views/components/sidebar.blade.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/Admin/resources/views/components/sidebar.blade.php)

### 4) NepeSearch teológico (léxico + panorama)

- Expandir `SearchEngineService` para incluir dois grupos novos:
  - `bible_lexicon` (transliteração/lemma/significado PT do Strong).
  - `bible_panoramas` (tema central/contexto histórico).
- Normalizar payload de resultado com `title`, `description`, `url` e `meta` para permitir clique/navegação.
- Atualizar `command-palette` para tornar itens clicáveis e rotear para destino apropriado (página Bible/Admin correspondente), com base em `url` retornada.

Arquivos alvo principais:

- [c:/laragon/www/VEPL/Modules/NepeSearch/app/Services/SearchEngineService.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/NepeSearch/app/Services/SearchEngineService.php)
- [c:/laragon/www/VEPL/Modules/NepeSearch/resources/views/components/command-palette.blade.php](../../../../../Users/Administrator/.cursor/plans/c:/laragon/www/VEPL/Modules/NepeSearch/resources/views/components/command-palette.blade.php)

## Restrições que serão garantidas

- `verses` permanecerá somente leitura (sem botões ou fluxo de edição de texto bíblico).
- UI/labels integralmente em Português (tom profissional).
- Uso do `x-rich-editor` nos campos longos de comentário.

## Validação e fechamento

- Verificar rotas nomeadas e navegação da sidebar.
- Validar retorno real de `official_commentary` na API de contexto.
- Executar checagem de lint/erros nos arquivos alterados e corrigir eventuais regressões simples.
