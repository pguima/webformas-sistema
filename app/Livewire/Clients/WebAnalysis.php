<?php

namespace App\Livewire\Clients;

use App\Models\Web;
use App\Models\WebPagespeedHistory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Component;

class WebAnalysis extends Component
{
    // ── Core ──────────────────────────────────────────────────────────────────
    public int $webId;
    public ?Web $web = null;
    public ?string $errorMessage = null;

    // ── Platform detection ────────────────────────────────────────────────────
    public bool $isWordPress = false;
    public bool $platformDetected = false;

    // ── Module system ─────────────────────────────────────────────────────────
    /** @var array<string, bool> */
    public array $selectedModules = [
        'pagespeed'        => true,
        'security_headers' => true,
        'schema'           => true,
        'geo'              => false,
        'sitemap'          => true,
        'ai_content'       => false,
        'links'            => false,
        'wp_images'        => true,
        'wp_pages'         => true,
        'wp_security'      => true,
    ];

    /** @var list<string> Modules waiting to run */
    public array $moduleQueue = [];

    /** @var array<string, string> idle | running | done | error */
    public array $moduleStatus = [];

    /** @var array<string, bool> Whether result card is expanded */
    public array $moduleExpanded = [];

    // ── PageSpeed ─────────────────────────────────────────────────────────────
    public ?array $pageSpeed = null;
    public array $pageSpeedErrors = [];
    public ?string $pageSpeedLastRunAt = null;
    public array $history = [];
    public array $chartHistory = [];

    // ── Security Headers ──────────────────────────────────────────────────────
    public array $securityChecks = [];
    public int $securityScore = 0;

    // ── Schema / OG ───────────────────────────────────────────────────────────
    public array $schemaItems = [];
    public array $ogTags = [];
    public array $twitterTags = [];

    // ── GEO ───────────────────────────────────────────────────────────────────
    public array $geoChecks = [];
    public int $geoScore = 0;

    // ── Sitemap & Robots ──────────────────────────────────────────────────────
    public ?array $sitemapData = null;
    public ?array $robotsData = null;

    // ── AI Content ────────────────────────────────────────────────────────────
    public ?array $aiAnalysis = null;

    // ── Links ─────────────────────────────────────────────────────────────────
    public array $links = [];

    // ── WP Images ─────────────────────────────────────────────────────────────
    public array $images = [];
    public int $totalImages = 0;
    public int $imagesOk = 0;
    public int $imagesError = 0;
    public int $imagesNotWebp = 0;
    public int $imagesWithoutAlt = 0;
    public int $imagesWithoutDimensions = 0;
    public int $imagesLarge = 0;
    public array $imagePageMap = [];

    // ── WP Pages SEO ──────────────────────────────────────────────────────────
    public array $pages = [];
    public int $pagesWithoutMetaDescription = 0;
    public int $pagesWithoutMetaKeywords = 0;
    public int $pagesWithoutH1 = 0;
    public int $pagesWithoutTitleTag = 0;
    public int $pagesDescriptionTooLong = 0;
    public int $pagesDescriptionTooShort = 0;
    public int $pagesDuplicateDescription = 0;
    public int $pagesHtmlFetched = 0;

    // ── WP Security ───────────────────────────────────────────────────────────
    public array $wpSecurityChecks = [];
    public int $wpSecurityScore = 0;

    // ── WP General ────────────────────────────────────────────────────────────
    public ?array $general = null;
    public array $wpNamespaces = [];
    public int $inactivePlugins = 0;
    public bool $sslEnabled = false;

    // ── HTML accumulators (public para persistir entre requests Livewire) ───────
    public array $htmlImageAlts = [];
    public array $htmlImagePages = [];
    private array $htmlLinks = [];

    // ─────────────────────────────────────────────────────────────────────────
    // Module definitions
    // ─────────────────────────────────────────────────────────────────────────

    public function moduleDefs(): array
    {
        return [
            'pagespeed'        => ['label' => 'PageSpeed',              'icon' => 'solar:chart-2-linear',             'wp_only' => false],
            'security_headers' => ['label' => 'Headers de segurança',   'icon' => 'solar:shield-check-linear',        'wp_only' => false],
            'schema'           => ['label' => 'Schema / Open Graph',    'icon' => 'solar:code-square-linear',         'wp_only' => false],
            'geo'              => ['label' => 'GEO / IA',               'icon' => 'solar:magic-stick-3-linear',       'wp_only' => false],
            'sitemap'          => ['label' => 'Sitemap & Robots',       'icon' => 'solar:map-linear',                 'wp_only' => false],
            'ai_content'       => ['label' => 'Análise com IA',         'icon' => 'solar:stars-minimalistic-linear',  'wp_only' => false],
            'links'            => ['label' => 'Links quebrados',         'icon' => 'solar:link-minimalistic-2-linear', 'wp_only' => false],
            'wp_images'        => ['label' => 'Imagens',                'icon' => 'solar:gallery-linear',             'wp_only' => true],
            'wp_pages'         => ['label' => 'Páginas SEO',            'icon' => 'solar:document-text-linear',       'wp_only' => true],
            'wp_security'      => ['label' => 'Segurança WordPress',    'icon' => 'solar:shield-warning-linear',      'wp_only' => true],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Lifecycle
    // ─────────────────────────────────────────────────────────────────────────

    public function mount(int $webId): void
    {
        $this->webId = $webId;

        try {
            $this->web = Web::query()->with(['client:id,name'])->findOrFail($webId);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
            return;
        }

        if ($this->web->pagespeed_last_checked_at) {
            $this->pageSpeedLastRunAt = $this->web->pagespeed_last_checked_at->format('d/m/Y H:i');
        }

        $this->history = WebPagespeedHistory::where('web_id', $webId)
            ->orderBy('analyzed_at', 'desc')->limit(10)->get()->toArray();

        $this->chartHistory = WebPagespeedHistory::where('web_id', $webId)
            ->orderBy('analyzed_at', 'asc')->limit(50)
            ->get(['analyzed_at', 'performance_mobile', 'performance_desktop', 'seo_mobile', 'seo_desktop',
                   'accessibility_mobile', 'accessibility_desktop', 'best_practices_mobile', 'best_practices_desktop'])
            ->toArray();

        // Auto-detect platform
        $this->detectPlatform();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Platform detection
    // ─────────────────────────────────────────────────────────────────────────

    public function detectPlatform(): void
    {
        $base = $this->normalizeBaseUrl($this->web?->url);
        if (!$base) {
            $this->platformDetected = true;
            return;
        }

        // Check platform field first
        $platform = strtolower($this->web?->platform ?? '');
        if (str_contains($platform, 'wordpress') || str_contains($platform, 'wp')) {
            $this->isWordPress = true;
            $this->platformDetected = true;
            return;
        }

        // Try /wp-json/ endpoint
        try {
            $r = Http::timeout(8)->acceptJson()->get("{$base}/wp-json/");
            if ($r->successful()) {
                $json = $r->json();
                $this->isWordPress = isset($json['namespaces']) || isset($json['name']);
            }
        } catch (\Throwable) {
            $this->isWordPress = false;
        }

        $this->platformDetected = true;

        // Disable WP-only modules if not WP
        if (!$this->isWordPress) {
            $this->selectedModules['wp_images']   = false;
            $this->selectedModules['wp_pages']    = false;
            $this->selectedModules['wp_security'] = false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module queue runner (incremental — each module in its own Livewire request)
    // ─────────────────────────────────────────────────────────────────────────

    public function startAnalysis(): void
    {
        $defs = $this->moduleDefs();
        $this->moduleQueue = array_values(array_filter(
            array_keys($this->selectedModules),
            fn($k) => ($this->selectedModules[$k] ?? false)
                && isset($defs[$k])
                && (!($defs[$k]['wp_only'] ?? false) || $this->isWordPress)
        ));

        $this->runNextModule();
    }

    public function runNextModule(): void
    {
        if (empty($this->moduleQueue)) {
            return;
        }

        $module = array_shift($this->moduleQueue);
        $this->runModule($module);

        if (!empty($this->moduleQueue)) {
            $this->dispatch('analysis-continue');
        }
    }

    public function runModule(string $module): void
    {
        @set_time_limit(300);

        $this->moduleStatus[$module] = 'running';

        try {
            match ($module) {
                'pagespeed'        => $this->runPageSpeed(),
                'security_headers' => $this->runSecurityHeaders(),
                'schema'           => $this->runSchema(),
                'geo'              => $this->runGeo(),
                'sitemap'          => $this->runSitemap(),
                'ai_content'       => $this->runAiContent(),
                'links'            => $this->runLinks(),
                'wp_images'        => $this->runWpImages(),
                'wp_pages'         => $this->runWpPages(),
                'wp_security'      => $this->runWpSecurity(),
                default            => null,
            };
            $this->moduleStatus[$module] = 'done';
        } catch (\Throwable $e) {
            $this->moduleStatus[$module] = 'error';
            $this->dispatch('notify', message: "[{$module}] " . $e->getMessage(), variant: 'danger', title: 'Erro');
        }

        $this->moduleExpanded[$module] = true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Select/deselect helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function selectAll(): void
    {
        $defs = $this->moduleDefs();
        foreach ($defs as $key => $def) {
            if (!($def['wp_only'] ?? false) || $this->isWordPress) {
                $this->selectedModules[$key] = true;
            }
        }
    }

    public function selectNone(): void
    {
        foreach ($this->selectedModules as $key => $_) {
            $this->selectedModules[$key] = false;
        }
    }

    public function cancelAnalysis(): void
    {
        $this->moduleQueue = [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: PageSpeed
    // ─────────────────────────────────────────────────────────────────────────

    private function runPageSpeed(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $strategies = ['mobile', 'desktop'];
        $urls = [];
        foreach ($strategies as $s) {
            $urls[$s] = $this->buildPageSpeedUrl($baseUrl, $s);
        }

        $responses = Http::pool(function ($pool) use ($urls) {
            $out = [];
            foreach ($urls as $strategy => $url) {
                $out[$strategy] = $pool->as($strategy)->timeout(120)->acceptJson()->get($url);
            }
            return $out;
        });

        $out = [];
        foreach ($strategies as $strategy) {
            try {
                $response = $responses[$strategy] ?? null;
                if (!$response) {
                    continue;
                }
                $response->throw();
                $json = $response->json();
                if (is_array($json)) {
                    $out[$strategy] = $this->parsePageSpeedResponse($json);
                }
            } catch (RequestException $e) {
                $this->pageSpeedErrors[$strategy] = $e->getMessage() . ' (HTTP ' . ($e->response?->status() ?? '?') . ')';
            } catch (\Throwable $e) {
                $this->pageSpeedErrors[$strategy] = $e->getMessage();
            }
        }

        $this->pageSpeed = !empty($out) ? $out : null;

        if ($this->pageSpeed) {
            $this->pageSpeedLastRunAt = now()->format('d/m/Y H:i');

            $desktopScores = $this->pageSpeed['desktop']['scores'] ?? null;
            if (is_array($desktopScores)) {
                $this->web?->update([
                    'performance'              => $this->normalizeScore($desktopScores['performance'] ?? null),
                    'seo'                      => $this->normalizeScore($desktopScores['seo'] ?? null),
                    'accessibility'            => $this->normalizeScore($desktopScores['accessibility'] ?? null),
                    'best_practices'           => $this->normalizeScore($desktopScores['best_practices'] ?? null),
                    'pagespeed_last_checked_at' => now(),
                ]);
                $this->web?->refresh();
                $this->dispatch('client-webs-refresh');
            }

            try {
                $mobileScores  = $this->pageSpeed['mobile']['scores'] ?? [];
                $desktopScores = $this->pageSpeed['desktop']['scores'] ?? [];
                WebPagespeedHistory::create([
                    'web_id'                 => $this->webId,
                    'performance_mobile'     => $this->normalizeScore($mobileScores['performance'] ?? null),
                    'seo_mobile'             => $this->normalizeScore($mobileScores['seo'] ?? null),
                    'accessibility_mobile'   => $this->normalizeScore($mobileScores['accessibility'] ?? null),
                    'best_practices_mobile'  => $this->normalizeScore($mobileScores['best_practices'] ?? null),
                    'performance_desktop'    => $this->normalizeScore($desktopScores['performance'] ?? null),
                    'seo_desktop'            => $this->normalizeScore($desktopScores['seo'] ?? null),
                    'accessibility_desktop'  => $this->normalizeScore($desktopScores['accessibility'] ?? null),
                    'best_practices_desktop' => $this->normalizeScore($desktopScores['best_practices'] ?? null),
                    'analyzed_at'            => now(),
                ]);

                $this->history = WebPagespeedHistory::where('web_id', $this->webId)
                    ->orderBy('analyzed_at', 'desc')->limit(10)->get()->toArray();
                $this->chartHistory = WebPagespeedHistory::where('web_id', $this->webId)
                    ->orderBy('analyzed_at', 'asc')->limit(50)
                    ->get(['analyzed_at','performance_mobile','performance_desktop','seo_mobile','seo_desktop',
                           'accessibility_mobile','accessibility_desktop','best_practices_mobile','best_practices_desktop'])
                    ->toArray();
            } catch (\Throwable) {
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: Security Headers
    // ─────────────────────────────────────────────────────────────────────────

    private function runSecurityHeaders(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $response = Http::timeout(15)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($baseUrl);
        $headers  = array_change_key_case($response->headers(), CASE_LOWER);

        $getH = function (string $name) use ($headers): ?string {
            $val = $headers[$name] ?? null;
            if (is_array($val)) {
                $val = $val[0] ?? null;
            }
            return is_string($val) ? $val : null;
        };

        $checks = [];

        $checks[] = ['name' => 'HTTPS', 'status' => str_starts_with($baseUrl, 'https://') ? 'pass' : 'fail',
            'value' => str_starts_with($baseUrl, 'https://') ? 'Ativo' : 'Inativo', 'severity' => 'critical',
            'description' => 'Site acessível via HTTPS',
            'tip' => 'Instale SSL/TLS e force redirect HTTP→HTTPS.'];

        $hsts = $getH('strict-transport-security');
        $checks[] = ['name' => 'HSTS', 'status' => $hsts ? 'pass' : 'fail', 'value' => $hsts ?: 'Ausente',
            'severity' => 'high', 'description' => 'HTTP Strict Transport Security',
            'tip' => 'Adicione: Strict-Transport-Security: max-age=31536000; includeSubDomains'];

        $xfo = $getH('x-frame-options');
        $checks[] = ['name' => 'X-Frame-Options', 'status' => $xfo ? 'pass' : 'fail', 'value' => $xfo ?: 'Ausente',
            'severity' => 'medium', 'description' => 'Proteção contra Clickjacking',
            'tip' => 'Adicione: X-Frame-Options: SAMEORIGIN'];

        $xcto = $getH('x-content-type-options');
        $checks[] = ['name' => 'X-Content-Type-Options', 'status' => $xcto === 'nosniff' ? 'pass' : 'fail',
            'value' => $xcto ?: 'Ausente', 'severity' => 'medium', 'description' => 'Previne MIME sniffing',
            'tip' => 'Adicione: X-Content-Type-Options: nosniff'];

        $csp = $getH('content-security-policy');
        $checks[] = ['name' => 'Content-Security-Policy', 'status' => $csp ? 'pass' : 'fail',
            'value' => $csp ? Str::limit($csp, 80) : 'Ausente', 'severity' => 'high',
            'description' => 'Controla recursos carregáveis (previne XSS)',
            'tip' => "Adicione uma CSP. Ex: Content-Security-Policy: default-src 'self'"];

        $rp = $getH('referrer-policy');
        $checks[] = ['name' => 'Referrer-Policy', 'status' => $rp ? 'pass' : 'warn',
            'value' => $rp ?: 'Ausente', 'severity' => 'low', 'description' => 'Controla cabeçalho Referer',
            'tip' => 'Adicione: Referrer-Policy: strict-origin-when-cross-origin'];

        $xpb = $getH('x-powered-by');
        $checks[] = ['name' => 'X-Powered-By', 'status' => $xpb ? 'warn' : 'pass',
            'value' => $xpb ?: 'Oculto', 'severity' => 'low', 'description' => 'Não deve expor tecnologia usada',
            'tip' => 'Remova o header X-Powered-By.'];

        $this->securityChecks = $checks;

        $weights = ['critical' => 30, 'high' => 20, 'medium' => 15, 'low' => 5];
        $total = $earned = 0;
        foreach ($checks as $c) {
            $w = $weights[$c['severity']] ?? 5;
            $total += $w;
            if ($c['status'] === 'pass') {
                $earned += $w;
            } elseif ($c['status'] === 'warn') {
                $earned += (int) ($w * 0.5);
            }
        }
        $this->securityScore = $total > 0 ? (int) round(($earned / $total) * 100) : 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: Schema / Open Graph
    // ─────────────────────────────────────────────────────────────────────────

    private function runSchema(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $html = $this->fetchPageHtml($baseUrl);
        if (!$html) {
            throw new \RuntimeException('Não foi possível carregar o HTML.');
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $html);
        libxml_clear_errors();

        $requiredFields = [
            'Organization' => ['name', 'url'], 'WebSite' => ['name', 'url'],
            'LocalBusiness' => ['name', 'address', 'telephone'], 'Article' => ['headline', 'author', 'datePublished'],
            'BlogPosting' => ['headline', 'author', 'datePublished'], 'Product' => ['name', 'offers'],
            'FAQPage' => ['mainEntity'], 'BreadcrumbList' => ['itemListElement'], 'Person' => ['name'],
            'Event' => ['name', 'startDate', 'location'],
        ];

        $schemas = [];
        foreach ($dom->getElementsByTagName('script') as $script) {
            if (strtolower($script->getAttribute('type')) !== 'application/ld+json') {
                continue;
            }
            $decoded = json_decode(trim($script->textContent), true);
            if (!is_array($decoded)) {
                continue;
            }
            if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                foreach ($decoded['@graph'] as $item) {
                    if (is_array($item)) {
                        $schemas[] = $item;
                    }
                }
            } else {
                $schemas[] = $decoded;
            }
        }

        $this->schemaItems = [];
        foreach ($schemas as $schema) {
            $type = is_array($schema['@type'] ?? null) ? implode(', ', $schema['@type']) : ($schema['@type'] ?? 'Unknown');
            $required = $requiredFields[$type] ?? [];
            $missing = array_filter($required, fn($f) => !isset($schema[$f]) || (is_string($schema[$f]) && trim($schema[$f]) === ''));
            $this->schemaItems[] = [
                'type'           => $type,
                'name'           => $schema['name'] ?? $schema['headline'] ?? null,
                'valid'          => empty($missing),
                'missing_fields' => array_values($missing),
                'raw'            => json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
        }

        $og = [];
        $tw = [];
        foreach ($dom->getElementsByTagName('meta') as $meta) {
            $prop    = $meta->getAttribute('property');
            $name    = $meta->getAttribute('name');
            $content = $meta->getAttribute('content');
            if (str_starts_with($prop, 'og:')) {
                $og[substr($prop, 3)] = $content;
            }
            if (str_starts_with($name, 'twitter:')) {
                $tw[substr($name, 8)] = $content;
            }
        }
        $this->ogTags      = $og;
        $this->twitterTags = $tw;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: GEO
    // ─────────────────────────────────────────────────────────────────────────

    private function runGeo(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $html = $this->fetchPageHtml($baseUrl);
        $checks = [];

        // llms.txt
        try {
            $r       = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(rtrim($baseUrl, '/') . '/llms.txt');
            $hasLlms = $r->successful() && trim($r->body()) !== '';
        } catch (\Throwable) {
            $hasLlms = false;
        }
        $checks[] = ['name' => 'llms.txt', 'status' => $hasLlms ? 'pass' : 'fail',
            'value' => $hasLlms ? 'Encontrado' : 'Ausente', 'weight' => 15,
            'description' => 'Arquivo de instrução para LLMs',
            'tip' => 'Crie /llms.txt descrevendo o site e o que a IA pode ou não referenciar.'];

        // AI crawlers in robots.txt
        try {
            $r           = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(rtrim($baseUrl, '/') . '/robots.txt');
            $robotsBody  = $r->successful() ? $r->body() : '';
        } catch (\Throwable) {
            $robotsBody = '';
        }
        $aiCrawlers = ['GPTBot', 'ClaudeBot', 'PerplexityBot', 'anthropic-ai', 'ChatGPT-User'];
        $blocked = array_filter($aiCrawlers, fn($c) => preg_match('/User-agent:\s*' . preg_quote($c, '/') . '.*?Disallow:\s*\//is', $robotsBody));
        $checks[] = ['name' => 'Crawlers de IA', 'status' => empty($blocked) ? 'pass' : 'warn',
            'value' => empty($blocked) ? 'Todos permitidos' : 'Bloqueados: ' . implode(', ', $blocked),
            'weight' => 20, 'description' => 'GPTBot, ClaudeBot, PerplexityBot no robots.txt',
            'tip' => 'Certifique-se que crawlers de IA não estão bloqueados no robots.txt.'];

        // FAQ schema
        $hasFaq = $html && (str_contains($html, 'FAQPage') || str_contains($html, '"@type":"FAQPage"'));
        $checks[] = ['name' => 'FAQ Schema', 'status' => $hasFaq ? 'pass' : 'warn',
            'value' => $hasFaq ? 'FAQPage detectado' : 'Ausente', 'weight' => 15,
            'description' => 'Conteúdo Q&A é altamente citável por IA',
            'tip' => 'Adicione uma seção FAQ com FAQPage Schema.'];

        // Definition blocks
        $hasDefs = $html && preg_match('/<h[23][^>]*>(O que|Como|Por que|Quando|Qual|What is|How to)/i', $html);
        $checks[] = ['name' => 'Respostas diretas', 'status' => $hasDefs ? 'pass' : 'warn',
            'value' => $hasDefs ? 'Padrões detectados' : 'Não detectados', 'weight' => 10,
            'description' => 'Headings com perguntas favorecem AI Overviews',
            'tip' => 'Use headings como "O que é X?" seguidos de respostas concisas.'];

        // Author signals
        $hasAuthor = $html && (str_contains($html, '"author"') || str_contains($html, 'rel="author"'));
        $checks[] = ['name' => 'Sinais de autoria (E-E-A-T)', 'status' => $hasAuthor ? 'pass' : 'warn',
            'value' => $hasAuthor ? 'Detectados' : 'Ausentes', 'weight' => 10,
            'description' => 'Expertise, autoridade e confiabilidade',
            'tip' => 'Adicione schema de Person/Organization com especialidade.'];

        // Schema variety
        preg_match_all('/"@type"\s*:\s*"([^"]+)"/', (string) $html, $m);
        $schemaTypes = array_unique($m[1] ?? []);
        $checks[] = ['name' => 'Variedade de Schema', 'status' => count($schemaTypes) >= 2 ? 'pass' : ($schemaTypes ? 'warn' : 'fail'),
            'value' => !empty($schemaTypes) ? implode(', ', $schemaTypes) : 'Nenhum', 'weight' => 10,
            'description' => 'Múltiplos schemas ajudam IA a entender contexto',
            'tip' => 'Use Organization + WebSite + um tipo específico (Article, FAQPage, etc.).'];

        $this->geoChecks = $checks;
        $total = array_sum(array_column($checks, 'weight'));
        $earned = 0;
        foreach ($checks as $c) {
            if ($c['status'] === 'pass') {
                $earned += $c['weight'];
            } elseif ($c['status'] === 'warn') {
                $earned += (int) ($c['weight'] * 0.5);
            }
        }
        $this->geoScore = $total > 0 ? (int) round(($earned / $total) * 100) : 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: Sitemap & Robots
    // ─────────────────────────────────────────────────────────────────────────

    private function runSitemap(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $base = rtrim($baseUrl, '/');

        // robots.txt
        try {
            $r = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get("{$base}/robots.txt");
            if ($r->successful() && trim($r->body()) !== '') {
                $body  = $r->body();
                $lines = array_map('trim', explode("\n", $body));
                $rules = [];
                $sitemapUrls = [];
                $agent = '*';
                foreach ($lines as $line) {
                    if ($line === '' || str_starts_with($line, '#')) {
                        continue;
                    }
                    if (preg_match('/^User-agent:\s*(.+)$/i', $line, $m)) {
                        $agent = trim($m[1]);
                    } elseif (preg_match('/^(Allow|Disallow):\s*(.*)$/i', $line, $m)) {
                        $rules[] = ['agent' => $agent, 'directive' => strtolower($m[1]), 'path' => $m[2]];
                    } elseif (preg_match('/^Sitemap:\s*(.+)$/i', $line, $m)) {
                        $sitemapUrls[] = trim($m[1]);
                    }
                }
                $disallowAll = collect($rules)->first(fn($r) => $r['directive'] === 'disallow' && $r['path'] === '/' && $r['agent'] === '*');
                $this->robotsData = [
                    'found' => true, 'content' => Str::limit($body, 2000),
                    'rules_count' => count($rules), 'sitemap_urls' => $sitemapUrls,
                    'disallow_all' => (bool) $disallowAll,
                    'issues' => $disallowAll ? ['Disallow: / bloqueia todos os crawlers!'] : [],
                ];
            } else {
                $this->robotsData = ['found' => false, 'issues' => ['robots.txt não encontrado.']];
            }
        } catch (\Throwable $e) {
            $this->robotsData = ['found' => false, 'issues' => ['Erro: ' . $e->getMessage()]];
        }

        // sitemap.xml
        $sitemapUrls = array_merge($this->robotsData['sitemap_urls'] ?? [], ["{$base}/sitemap.xml", "{$base}/sitemap_index.xml"]);
        $found = null;
        foreach (array_unique($sitemapUrls) as $url) {
            try {
                $r = Http::timeout(15)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
                if ($r->successful()) {
                    $found = ['url' => $url, 'body' => $r->body()];
                    break;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        if ($found) {
            $body     = $found['body'];
            $urlCount = substr_count($body, '<url>');
            $isIndex  = str_contains($body, '<sitemapindex');
            preg_match_all('/<loc>(.*?)<\/loc>/s', $body, $locM);
            $sampleUrls = array_slice($locM[1] ?? [], 0, 10);
            $this->sitemapData = [
                'found'       => true, 'url' => $found['url'], 'is_index' => $isIndex,
                'url_count'   => $urlCount, 'has_lastmod' => str_contains($body, '<lastmod>'),
                'has_images'  => str_contains($body, 'image:image'),
                'sample_urls' => $sampleUrls,
                'issues'      => !str_contains($body, '<lastmod>') && !$isIndex
                    ? ['URLs sem <lastmod> — buscadores podem re-crawlar desnecessariamente.'] : [],
            ];
        } else {
            $this->sitemapData = ['found' => false, 'issues' => ['Nenhum sitemap.xml encontrado.']];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: AI Content Analysis
    // ─────────────────────────────────────────────────────────────────────────

    private function runAiContent(): void
    {
        $apiKey = config('services.openai.key');
        $model  = config('services.openai.model', 'gpt-4o-mini');

        if (!$apiKey) {
            throw new \RuntimeException('OPENAI_API_KEY não configurada no .env.');
        }

        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        // Collect content from pages or homepage
        $textContent = '';
        foreach (array_slice($this->pages, 0, 5) as $page) {
            $title = $page['title'] ?? '';
            $desc  = $page['meta_description'] ?? '';
            $h1    = $page['h1'] ?? '';
            if ($title || $desc || $h1) {
                $textContent .= "Página: {$title}\nH1: {$h1}\nDescrição: {$desc}\n\n";
            }
        }

        if (!$textContent) {
            $html = $this->fetchPageHtml($baseUrl);
            if ($html) {
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $html);
                libxml_clear_errors();
                foreach (['script', 'style', 'nav', 'footer'] as $tag) {
                    foreach (iterator_to_array($dom->getElementsByTagName($tag)) as $node) {
                        $node->parentNode?->removeChild($node);
                    }
                }
                $textContent = Str::limit(trim(preg_replace('/\s+/', ' ', $dom->textContent) ?? ''), 3000);
            }
        }

        if (!$textContent) {
            throw new \RuntimeException('Não foi possível extrair conteúdo textual do site.');
        }

        $siteName  = $this->web?->name ?? $baseUrl;
        $siteType  = $this->web?->type ?? 'site';
        $objective = $this->web?->objective ?? '';

        $response = Http::timeout(60)->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $model,
                'messages'    => [
                    ['role' => 'system', 'content' => 'Especialista em SEO e conteúdo. Retorne SOMENTE JSON válido, sem markdown.'],
                    ['role' => 'user', 'content' => "Analise o site \"{$siteName}\" (tipo: {$siteType}, objetivo: {$objective}).\n\nConteúdo:\n{$textContent}\n\nRetorne JSON:\n{\"resumo\":\"...\",\"tom_de_voz\":\"...\",\"publico_alvo_estimado\":\"...\",\"palavras_chave_detectadas\":[],\"palavras_chave_sugeridas\":[],\"pontos_fortes\":[],\"oportunidades_melhoria\":[],\"legibilidade\":\"...\",\"intencao_de_busca\":\"...\",\"recomendacoes_conteudo\":[],\"score_qualidade\":75}"],
                ],
                'temperature'     => 0.3,
                'max_tokens'      => 1000,
                'response_format' => ['type' => 'json_object'],
            ]);

        $response->throw();
        $parsed = json_decode($response->json('choices.0.message.content', '{}'), true);

        if (!is_array($parsed)) {
            throw new \RuntimeException('Resposta JSON inválida da OpenAI.');
        }

        $this->aiAnalysis = $parsed;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: Links
    // ─────────────────────────────────────────────────────────────────────────

    private function runLinks(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        // Collect links from already fetched pages or crawl homepage
        if (empty($this->htmlLinks)) {
            $html = $this->fetchPageHtml($baseUrl);
            if ($html) {
                $data = $this->parseHtmlData($html);
                foreach ($data['links'] ?? [] as $href) {
                    $this->htmlLinks[$href] = true;
                }
            }
        }

        $uniqueUrls = [];
        foreach (array_keys($this->htmlLinks) as $href) {
            if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
                $uniqueUrls[$href] = $href;
            } elseif (str_starts_with($href, '/')) {
                $parsed = parse_url($baseUrl);
                $origin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
                $uniqueUrls[$origin . $href] = $origin . $href;
            }
        }
        $uniqueUrls = array_slice($uniqueUrls, 0, 150);

        if (empty($uniqueUrls)) {
            $this->links = [];
            return;
        }

        $results  = [];
        $batches  = array_chunk($uniqueUrls, 20, true);
        $order    = ['broken' => 0, 'error' => 1, 'redirect' => 2, 'ok' => 3, 'unknown' => 4];

        foreach ($batches as $batch) {
            $batchResults = Http::pool(function ($pool) use ($batch) {
                $out = [];
                foreach ($batch as $key => $url) {
                    $out[$key] = $pool->as($key)->timeout(10)
                        ->withHeaders(['User-Agent' => 'Mozilla/5.0'])->head($url);
                }
                return $out;
            });

            foreach ($batch as $key => $url) {
                try {
                    $status   = $batchResults[$key]?->status();
                    $type     = match(true) {
                        $status === null                    => 'error',
                        $status >= 200 && $status < 300    => 'ok',
                        $status >= 300 && $status < 400    => 'redirect',
                        $status >= 400 && $status < 500    => 'broken',
                        $status >= 500                      => 'error',
                        default                             => 'unknown',
                    };
                    $results[] = ['url' => $url, 'status' => $status, 'type' => $type,
                        'internal' => str_starts_with($url, $baseUrl)];
                } catch (\Throwable) {
                    $results[] = ['url' => $url, 'status' => null, 'type' => 'error',
                        'internal' => str_starts_with($url, $baseUrl)];
                }
            }
        }

        usort($results, fn($a, $b) => ($order[$a['type']] ?? 9) <=> ($order[$b['type']] ?? 9));
        $this->links = $results;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: WP Images
    // ─────────────────────────────────────────────────────────────────────────

    private function runWpImages(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $this->images = $this->fetchWpImages($baseUrl);

        if (empty($this->htmlImageAlts) && !empty($this->pages)) {
            // Already enriched by wp_pages run
        }

        $this->enrichImageAltsFromHtml();
        $this->buildImagePageMap();
        $this->computeImageCounters();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: WP Pages SEO
    // ─────────────────────────────────────────────────────────────────────────

    private function runWpPages(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        if (empty($this->general)) {
            $this->general = $this->fetchWpGeneral($baseUrl);
        }

        $this->pages = $this->fetchWpPages($baseUrl);
        $this->computePageCounters();

        if (!empty($this->images)) {
            $this->buildImagePageMap();
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Module: WP Security
    // ─────────────────────────────────────────────────────────────────────────

    private function runWpSecurity(): void
    {
        $baseUrl = $this->normalizeBaseUrl($this->web?->url);
        if (!$baseUrl) {
            throw new \RuntimeException('URL inválida.');
        }

        $base   = rtrim($baseUrl, '/');
        $checks = [];

        $checkUrl = function (string $url) use ($base): int {
            try {
                $r = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
                return $r->status();
            } catch (\Throwable) {
                return 0;
            }
        };

        $s = $checkUrl("{$base}/readme.html");
        $checks[] = ['name' => 'readme.html', 'status' => $s === 200 ? 'fail' : 'pass',
            'value' => $s === 200 ? "HTTP {$s} — expõe versão do WP" : 'Bloqueado', 'severity' => 'medium',
            'tip' => 'Bloqueie o acesso ao readme.html via .htaccess.'];

        $s = $checkUrl("{$base}/xmlrpc.php");
        $checks[] = ['name' => 'xmlrpc.php', 'status' => in_array($s, [200, 405]) ? 'fail' : 'pass',
            'value' => in_array($s, [200, 405]) ? "HTTP {$s} — vetor de força bruta" : 'Bloqueado', 'severity' => 'high',
            'tip' => 'Desabilite o xmlrpc.php via plugin ou .htaccess.'];

        try {
            $r       = Http::timeout(8)->withOptions(['allow_redirects' => false])
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get("{$base}/?author=1");
            $loc     = $r->header('Location') ?? '';
            $enumerable = $r->status() === 301 && preg_match('/\/author\/[^\/]+\/?$/', $loc);
        } catch (\Throwable) {
            $enumerable = false;
        }
        $checks[] = ['name' => 'Enumeração de usuários (?author=1)', 'status' => $enumerable ? 'fail' : 'pass',
            'value' => $enumerable ? 'Usuários enumeráveis' : 'Protegido', 'severity' => 'high',
            'tip' => 'Bloqueie ?author= queries via .htaccess ou plugin de segurança.'];

        try {
            $r            = Http::timeout(8)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get("{$base}/wp-json/wp/v2/users");
            $usersExposed = $r->successful() && is_array($r->json()) && !empty($r->json());
        } catch (\Throwable) {
            $usersExposed = false;
        }
        $checks[] = ['name' => 'REST API lista usuários', 'status' => $usersExposed ? 'fail' : 'pass',
            'value' => $usersExposed ? 'Usuários listados em /wp-json/wp/v2/users' : 'Protegido', 'severity' => 'high',
            'tip' => 'Desative listagem pública de usuários via plugin.'];

        $html = $this->fetchPageHtml($baseUrl);
        $debugMode = $html && preg_match('/Fatal error|WordPress database error/i', $html);
        $checks[] = ['name' => 'WP_DEBUG em produção', 'status' => $debugMode ? 'fail' : 'pass',
            'value' => $debugMode ? 'Erros visíveis no HTML' : 'Sem erros detectados', 'severity' => 'critical',
            'tip' => 'Defina WP_DEBUG=false no wp-config.php.'];

        $wpVersion = null;
        if (!$html) {
            $html = $this->fetchPageHtml($baseUrl);
        }
        if ($html && preg_match('/WordPress\s+([\d.]+)/i', $html, $m)) {
            $wpVersion = $m[1];
        }
        $checks[] = ['name' => 'Versão WP exposta (meta generator)', 'status' => $wpVersion ? 'warn' : 'pass',
            'value' => $wpVersion ? "WordPress {$wpVersion} detectado" : 'Versão não exposta', 'severity' => 'low',
            'tip' => 'Adicione remove_action("wp_head","wp_generator"); no functions.php.'];

        $this->wpSecurityChecks = $checks;

        $weights = ['critical' => 30, 'high' => 20, 'medium' => 15, 'low' => 5];
        $total = $earned = 0;
        foreach ($checks as $c) {
            $w = $weights[$c['severity']] ?? 5;
            $total += $w;
            if ($c['status'] === 'pass') {
                $earned += $w;
            } elseif ($c['status'] === 'warn') {
                $earned += (int) ($w * 0.5);
            }
        }
        $this->wpSecurityScore = $total > 0 ? (int) round(($earned / $total) * 100) : 0;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // WordPress data fetchers (shared helpers)
    // ─────────────────────────────────────────────────────────────────────────

    private function fetchWpGeneral(string $baseUrl): array
    {
        $general = ['url' => $baseUrl, 'wp_version' => null, 'active_theme' => null, 'plugins' => null, 'notes' => []];

        try {
            $root = $this->httpGetJson($this->wpApiUrl($baseUrl, ''));
            if (isset($root['namespaces'])) {
                $this->wpNamespaces = array_values(array_filter(array_map(fn($n) => is_string($n) ? $n : null, $root['namespaces'])));
            }
            $general['wp'] = ['name' => $root['name'] ?? null, 'url' => $root['url'] ?? null];
        } catch (\Throwable $e) {
            $general['notes'][] = 'Falha ao ler /wp-json: ' . $e->getMessage();
        }

        try {
            $html = $this->fetchPageHtml($baseUrl);
            if ($html && preg_match('/WordPress\s+([\d.]+)/i', $html, $m)) {
                $general['wp_version'] = $m[1];
            }
        } catch (\Throwable) {
        }

        try {
            $plugins = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/plugins'), ['per_page' => 100]);
            if (!empty($plugins)) {
                $general['plugins'] = array_map(fn($p) => [
                    'name' => $p['name'] ?? null, 'status' => $p['status'] ?? null, 'version' => $p['version'] ?? null,
                ], $plugins);
            }
        } catch (\Throwable) {
        }

        return $general;
    }

    private function fetchWpImages(string $baseUrl): array
    {
        $out  = [];
        $page = 1;
        while ($page <= 10) {
            $items = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/media'), [
                'per_page' => 100, 'page' => $page, 'media_type' => 'image',
            ]);
            if (empty($items)) {
                break;
            }
            foreach ($items as $m) {
                $url = (string) ($m['source_url'] ?? '');
                $ext = $url ? strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION)) : '';
                $out[] = [
                    'id' => $m['id'] ?? null, 'url' => $url ?: null, 'ext' => $ext ?: null,
                    'width' => $m['media_details']['width'] ?? null, 'height' => $m['media_details']['height'] ?? null,
                    'alt_text' => is_string($m['alt_text'] ?? null) ? $m['alt_text'] : null,
                    'filesize' => is_numeric($m['media_details']['filesize'] ?? null) ? (int) $m['media_details']['filesize'] : null,
                ];
            }
            if (count($items) < 100) {
                break;
            }
            $page++;
        }
        return $out;
    }

    private function fetchWpPages(string $baseUrl): array
    {
        $out           = [];
        $page          = 1;
        $htmlFetchCount = 0;
        $maxHtmlFetch  = 100;

        while ($page <= 10) {
            $items = $this->httpGetJson($this->wpApiUrl($baseUrl, 'wp/v2/pages'), [
                'per_page' => 100, 'page' => $page, 'context' => 'view',
            ]);
            if (empty($items)) {
                break;
            }

            foreach ($items as $p) {
                $id    = $p['id'] ?? null;
                $title = $p['title']['rendered'] ?? null;
                $link  = $p['link'] ?? null;

                // Dados do Yoast (sem fetch HTML)
                $metaDescription = $p['yoast_head_json']['description'] ?? null;
                $titleTag        = isset($p['yoast_head_json']['title']) && trim((string)$p['yoast_head_json']['title']) !== ''
                                   ? (string)$p['yoast_head_json']['title'] : null;
                $canonical       = $p['yoast_head_json']['canonical'] ?? null;
                $metaKeywords    = null;
                $h1 = null;
                $htmlFetched = false;

                // SEOPress fallback
                if (!$metaDescription && is_int($id) && $this->hasWpNamespace('seopress/v1')) {
                    try {
                        $seo             = $this->httpGetJson($this->wpApiUrl($baseUrl, "seopress/v1/posts/{$id}"));
                        $metaDescription = $this->extractFirstString($seo, ['meta_desc', 'meta_description', 'description']);
                        $metaKeywords    = $this->extractFirstString($seo, ['meta_key', 'meta_keywords', 'keywords']);
                        if (!$titleTag) {
                            $titleTag = $this->extractFirstString($seo, ['title', 'seo_title', 'meta_title']);
                        }
                    } catch (\Throwable) {
                    }
                }

                if (is_string($link) && $htmlFetchCount < $maxHtmlFetch) {
                    $htmlFetchCount++;
                    $htmlFetched = true;
                    $html        = $this->fetchPageHtml($link);
                    if ($html) {
                        $htmlData        = $this->parseHtmlData($html);
                        $metaDescription = $metaDescription ?: $htmlData['meta_description'];
                        $metaKeywords    = $metaKeywords    ?: $htmlData['meta_keywords'];
                        $h1              = $htmlData['h1'];
                        $titleTag        = $titleTag        ?: $htmlData['title_tag'];
                        $canonical       = $canonical       ?: $htmlData['canonical'];

                        $pageTitle = is_string($title) ? strip_tags($title) : ($link ?? '');
                        $pageBase  = rtrim((string) $link, '/');
                        $pageOrigin = parse_url($pageBase, PHP_URL_SCHEME) . '://' . parse_url($pageBase, PHP_URL_HOST);
                        foreach ($htmlData['image_alts'] as $src => $alt) {
                            $absSrc = $this->toAbsoluteUrl($src, $pageOrigin, $pageBase);
                            $this->htmlImageAlts[$absSrc] = $alt;
                        }
                        foreach ($htmlData['image_srcs'] as $src) {
                            $absSrc = $this->toAbsoluteUrl($src, $pageOrigin, $pageBase);
                            $this->htmlImagePages[$absSrc][] = ['url' => $link, 'title' => $pageTitle];
                        }
                        foreach ($htmlData['links'] ?? [] as $href) {
                            $this->htmlLinks[$href] = true;
                        }
                    }
                }

                $out[] = [
                    'id' => $id, 'title' => is_string($title) ? strip_tags($title) : null,
                    'url' => is_string($link) ? $link : null, 'status' => $p['status'] ?? null,
                    'meta_description' => is_string($metaDescription) ? $metaDescription : null,
                    'meta_keywords' => is_string($metaKeywords) ? $metaKeywords : null,
                    'h1' => $h1, 'title_tag' => $titleTag, 'canonical' => $canonical,
                    'html_fetched' => $htmlFetched,
                ];
            }

            if (count($items) < 100) {
                break;
            }
            $page++;
        }

        return $out;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Counters
    // ─────────────────────────────────────────────────────────────────────────

    private function computeImageCounters(): void
    {
        $this->totalImages = count($this->images);
        $ok = $err = $notWebp = $withoutAlt = $withoutDims = $large = 0;
        foreach ($this->images as $img) {
            $ext      = strtolower((string) ($img['ext'] ?? ''));
            $alt      = trim((string) ($img['alt_text'] ?? ''));
            $filesize = $img['filesize'] ?? null;
            if ($ext !== 'webp') {
                $notWebp++;
            }
            if ($alt === '') {
                $withoutAlt++;
            }
            if (!$img['width'] || !$img['height']) {
                $withoutDims++;
            }
            if ($filesize > 500000) {
                $large++;
            }
            if ($ext === 'webp' && $alt !== '') {
                $ok++;
            } else {
                $err++;
            }
        }
        $this->imagesOk = $ok;
        $this->imagesError = $err;
        $this->imagesNotWebp = $notWebp;
        $this->imagesWithoutAlt = $withoutAlt;
        $this->imagesWithoutDimensions = $withoutDims;
        $this->imagesLarge = $large;
    }

    private function computePageCounters(): void
    {
        $noDesc = $noKw = $noH1 = $noTitle = $descLong = $descShort = $fetched = 0;
        $descCounts = [];
        foreach ($this->pages as $p) {
            $desc      = trim((string) ($p['meta_description'] ?? ''));
            $kw        = trim((string) ($p['meta_keywords'] ?? ''));
            $isFetched = (bool) ($p['html_fetched'] ?? false);
            if ($isFetched) {
                $fetched++;
            }
            if ($desc === '') {
                $noDesc++;
            } else {
                $len = mb_strlen($desc);
                if ($len > 160) {
                    $descLong++;
                } elseif ($len < 50) {
                    $descShort++;
                }
                $descCounts[$desc] = ($descCounts[$desc] ?? 0) + 1;
            }
            if ($kw === '') {
                $noKw++;
            }
            if ($isFetched && trim((string) ($p['h1'] ?? '')) === '') {
                $noH1++;
            }
            if (trim((string) ($p['title_tag'] ?? '')) === '') {
                $noTitle++;
            }
        }
        $dupDescs = 0;
        foreach ($descCounts as $count) {
            if ($count > 1) {
                $dupDescs += $count;
            }
        }
        $this->pagesWithoutMetaDescription = $noDesc;
        $this->pagesWithoutMetaKeywords    = $noKw;
        $this->pagesWithoutH1              = $noH1;
        $this->pagesWithoutTitleTag        = $noTitle;
        $this->pagesDescriptionTooLong     = $descLong;
        $this->pagesDescriptionTooShort    = $descShort;
        $this->pagesDuplicateDescription   = $dupDescs;
        $this->pagesHtmlFetched            = $fetched;
    }

    private function enrichImageAltsFromHtml(): void
    {
        foreach ($this->images as &$img) {
            if (trim((string) ($img['alt_text'] ?? '')) !== '') {
                continue;
            }
            $url = (string) ($img['url'] ?? '');
            if ($url === '') {
                continue;
            }
            if (isset($this->htmlImageAlts[$url])) {
                $img['alt_text'] = $this->htmlImageAlts[$url];
                continue;
            }
            $normalized = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $url) ?? $url;
            foreach ($this->htmlImageAlts as $src => $alt) {
                if ((preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $src) ?? $src) === $normalized) {
                    $img['alt_text'] = $alt;
                    break;
                }
            }
        }
        unset($img);
    }

    private function buildImagePageMap(): void
    {
        foreach ($this->images as $img) {
            $url = (string) ($img['url'] ?? '');
            if ($url === '') {
                continue;
            }
            $pages = $this->htmlImagePages[$url] ?? [];
            if (empty($pages)) {
                $normalized = preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $url) ?? $url;
                foreach ($this->htmlImagePages as $src => $srcPages) {
                    if ((preg_replace('/-\d+x\d+(\.[a-z]+)$/i', '$1', $src) ?? $src) === $normalized) {
                        $pages = array_merge($pages, $srcPages);
                    }
                }
            }
            $seen   = [];
            $unique = [];
            foreach ($pages as $p) {
                if (!in_array($p['url'], $seen, true)) {
                    $seen[]   = $p['url'];
                    $unique[] = $p;
                }
            }
            if (!empty($unique)) {
                $this->imagePageMap[$url] = $unique;
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Generic HTTP / parsing helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function toAbsoluteUrl(string $src, string $origin, string $base): string
    {
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            return $src;
        }
        if (str_starts_with($src, '//')) {
            return 'https:' . $src;
        }
        if (str_starts_with($src, '/')) {
            return $origin . $src;
        }
        return $base . '/' . $src;
    }

    private function normalizeBaseUrl(?string $url): ?string
    {
        if (!$url || trim($url) === '') {
            return null;
        }
        $trimmed = trim($url);
        if (!Str::startsWith($trimmed, ['http://', 'https://'])) {
            $trimmed = 'https://' . $trimmed;
        }
        return rtrim($trimmed, '/');
    }

    private function normalizeScore(mixed $value): ?int
    {
        if (!is_numeric($value)) {
            return null;
        }
        $v = (float) $value;
        if ($v <= 1) {
            $v *= 100;
        }
        return max(0, min(100, (int) round($v)));
    }

    private function wpApiUrl(string $baseUrl, string $path): string
    {
        return $baseUrl . '/wp-json/' . ltrim($path, '/');
    }

    private function httpGetJson(string $url, array $query = []): array
    {
        $r = Http::timeout(20)->acceptJson()->get($url, $query);
        $r->throw();
        $json = $r->json();
        return is_array($json) ? $json : [];
    }

    private function fetchPageHtml(string $url): ?string
    {
        try {
            $r = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; WebAudit/1.0)'])->get($url);
            return $r->successful() ? $r->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseHtmlData(string $html): array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8" ?>' . $html);
        libxml_clear_errors();

        $description = $keywords = $wpVersionHint = null;
        $imageAlts   = $allImageSrcs = $links = [];

        foreach ($dom->getElementsByTagName('meta') as $meta) {
            $name    = strtolower($meta->getAttribute('name'));
            $content = $meta->getAttribute('content');
            if ($name === 'description' && !$description && trim($content) !== '') {
                $description = $content;
            }
            if ($name === 'keywords' && !$keywords && trim($content) !== '') {
                $keywords = $content;
            }
            if ($name === 'generator' && !$wpVersionHint && preg_match('/WordPress\s+([\d.]+)/i', $content, $m)) {
                $wpVersionHint = $m[1];
            }
        }

        foreach ($dom->getElementsByTagName('img') as $img) {
            // Captura src real considerando lazy loading (data-src, data-lazy-src, etc.)
            $src = '';
            foreach (['data-lazy-src', 'data-src', 'data-original', 'src'] as $attr) {
                $val = trim($img->getAttribute($attr));
                if ($val !== '' && !str_starts_with($val, 'data:')) {
                    $src = $val;
                    break;
                }
            }
            $alt = $img->getAttribute('alt');
            if ($src !== '') {
                $allImageSrcs[] = $src;
                if ($alt !== '') {
                    $imageAlts[$src] = $alt;
                }
            }
            // srcset: captura todas as URLs
            $srcset = $img->getAttribute('srcset') ?: $img->getAttribute('data-srcset');
            if ($srcset !== '') {
                foreach (explode(',', $srcset) as $part) {
                    $srcsetUrl = trim(explode(' ', trim($part))[0]);
                    if ($srcsetUrl !== '' && !str_starts_with($srcsetUrl, 'data:')) {
                        $allImageSrcs[] = $srcsetUrl;
                    }
                }
            }
        }

        $titleTag = null;
        $t        = $dom->getElementsByTagName('title');
        if ($t->length > 0 && ($text = trim($t->item(0)->textContent)) !== '') {
            $titleTag = $text;
        }

        $h1   = null;
        $h1Ns = $dom->getElementsByTagName('h1');
        if ($h1Ns->length > 0 && ($text = trim($h1Ns->item(0)->textContent)) !== '') {
            $h1 = $text;
        }

        $canonical = null;
        foreach ($dom->getElementsByTagName('link') as $link) {
            if (strtolower($link->getAttribute('rel')) === 'canonical' && ($href = $link->getAttribute('href')) !== '') {
                $canonical = $href;
                break;
            }
        }

        foreach ($dom->getElementsByTagName('a') as $anchor) {
            $href = trim($anchor->getAttribute('href'));
            if ($href !== '' && !str_starts_with($href, '#') && !str_starts_with($href, 'javascript:')
                && !str_starts_with($href, 'mailto:') && !str_starts_with($href, 'tel:')) {
                $links[] = $href;
            }
        }

        return [
            'meta_description' => $description, 'meta_keywords' => $keywords,
            'image_alts' => $imageAlts, 'image_srcs' => $allImageSrcs,
            'title_tag' => $titleTag, 'h1' => $h1, 'canonical' => $canonical,
            'wp_version_hint' => $wpVersionHint, 'links' => $links,
        ];
    }

    private function buildPageSpeedUrl(string $baseUrl, string $strategy): string
    {
        $key        = config('services.pagespeed.key');
        $categories = ['performance', 'seo', 'accessibility', 'best-practices'];
        $query      = ['url' => $baseUrl, 'strategy' => $strategy];
        if ($key) {
            $query['key'] = $key;
        }
        $qs = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        foreach ($categories as $cat) {
            $qs .= '&category=' . rawurlencode($cat);
        }
        return 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?' . $qs;
    }

    private function parsePageSpeedResponse(array $json): array
    {
        $lhr      = $json['lighthouseResult'] ?? [];
        $cats     = $lhr['categories'] ?? [];
        $audits   = $lhr['audits'] ?? [];

        $score = fn(string $key): ?float => is_numeric($cats[$key]['score'] ?? null) ? (float) $cats[$key]['score'] : null;
        $num   = fn(string $k): ?float => is_numeric($audits[$k]['numericValue'] ?? null) ? (float) $audits[$k]['numericValue'] : null;
        $disp  = fn(string $k): ?string => is_string($audits[$k]['displayValue'] ?? null) && trim($audits[$k]['displayValue']) !== '' ? $audits[$k]['displayValue'] : null;

        $opportunities = [];
        foreach ($audits as $id => $audit) {
            if (($audit['details']['type'] ?? '') !== 'opportunity') {
                continue;
            }
            $s = isset($audit['score']) && is_numeric($audit['score']) ? (float) $audit['score'] : null;
            if ($s === null || $s >= 1.0) {
                continue;
            }
            $ms             = $audit['details']['overallSavingsMs'] ?? null;
            $opportunities[] = [
                'id' => $id, 'title' => $audit['title'] ?? $id,
                'display_value' => $audit['displayValue'] ?? null,
                'score' => $s, 'savings_ms' => is_numeric($ms) ? (int) round((float) $ms) : null,
            ];
        }
        usort($opportunities, fn($a, $b) => ($b['savings_ms'] ?? 0) <=> ($a['savings_ms'] ?? 0));

        return [
            'scores'        => ['performance' => $score('performance'), 'seo' => $score('seo'),
                                'accessibility' => $score('accessibility'), 'best_practices' => $score('best-practices')],
            'metrics'       => ['fcp_ms' => $num('first-contentful-paint'), 'lcp_ms' => $num('largest-contentful-paint'),
                                'tbt_ms' => $num('total-blocking-time'), 'cls' => $num('cumulative-layout-shift'),
                                'ttfb_ms' => $num('server-response-time'), 'speed_index_ms' => $num('speed-index'),
                                'inp_ms' => $num('interaction-to-next-paint')],
            'display'       => ['fcp' => $disp('first-contentful-paint'), 'lcp' => $disp('largest-contentful-paint'),
                                'tbt' => $disp('total-blocking-time'), 'cls' => $disp('cumulative-layout-shift'),
                                'ttfb' => $disp('server-response-time'), 'speed_index' => $disp('speed-index'),
                                'inp' => $disp('interaction-to-next-paint')],
            'opportunities' => $opportunities,
        ];
    }

    private function hasWpNamespace(string $prefix): bool
    {
        foreach ($this->wpNamespaces as $ns) {
            if (str_starts_with($ns, $prefix)) {
                return true;
            }
        }
        return false;
    }

    private function extractFirstString(array $data, array $keys): ?string
    {
        $stack = [$data];
        while (!empty($stack)) {
            $current = array_pop($stack);
            foreach ($keys as $k) {
                if (array_key_exists($k, $current) && is_string($current[$k]) && trim($current[$k]) !== '') {
                    return $current[$k];
                }
            }
            foreach ($current as $v) {
                if (is_array($v)) {
                    $stack[] = $v;
                }
            }
        }
        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.clients.web-analysis', [
            'moduleDefs' => $this->moduleDefs(),
        ]);
    }
}
