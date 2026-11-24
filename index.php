<?php

declare(strict_types=1);

require __DIR__ . '/app/Router.php';
require __DIR__ . '/app/Security.php';
require __DIR__ . '/app/icons.php';

use App\Router;
use App\Security;

Security::enforceHttps();
Security::startSession();
Security::sendHeaders();

function current_origin(): string
{
    $scheme = Security::isHttps() ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
    return $scheme . '://' . $host;
}

/**
 * Rend une vue de page à l'intérieur du gabarit principal.
 */
function render_page(string $view, array $data = []): void
{
    $pageTitle = $data['title'] ?? 'Flexarion';
    $pageDescription = $data['description'] ?? '';
    $activeRoute = $data['route'] ?? '';
    $cspNonce = Security::getNonce();
    $csrfToken = Security::csrfToken();
    $origin = current_origin();
    $canonicalUrl = $origin . ($data['canonicalPath'] ?? '/');
    $robotsMeta = $data['robots'] ?? 'index, follow';

    extract($data, EXTR_SKIP);

    ob_start();
    require __DIR__ . '/views/pages/' . $view . '.php';
    $content = ob_get_clean();

    require __DIR__ . '/views/layout.php';
}

/**
 * Valide et assainit les données du formulaire de contact côté serveur.
 *
 * @return array{old: array<string, string>, errors: array<string, string>}
 */
function validate_contact(array $input): array
{
    $allowedSujets = ['general', 'support', 'recrutement', 'presse'];
    $sujet = (string) ($input['sujet'] ?? 'general');
    if (!in_array($sujet, $allowedSujets, true)) {
        $sujet = 'general';
    }

    $old = [
        'nom' => Security::sanitizeText((string) ($input['nom'] ?? ''), 80),
        'email' => Security::sanitizeText((string) ($input['email'] ?? ''), 120),
        'sujet' => $sujet,
        'message' => Security::sanitizeText((string) ($input['message'] ?? ''), 2000),
    ];

    $errors = [];
    if (mb_strlen($old['nom'], 'UTF-8') < 2) {
        $errors['nom'] = 'Indiquez votre nom ou pseudo (2 caractères minimum).';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Indiquez une adresse e-mail valide (ex : nom@exemple.com).';
    }
    if (mb_strlen($old['message'], 'UTF-8') < 10) {
        $errors['message'] = 'Votre message doit contenir au moins 10 caractères.';
    }

    return ['old' => $old, 'errors' => $errors];
}

$router = new Router();

$router->get('/', static function (): void {
    render_page('accueil', [
        'route' => 'accueil',
        'canonicalPath' => '/',
        'title' => 'Flexarion | Studio indépendant de jeux RPG Dark Fantasy',
        'description' => "Flexarion, studio indépendant de jeux vidéo RPG dark fantasy à Aix-en-Provence : chevalier, monstres, magie et brume.",
    ]);
});

$router->get('/studio', static function (): void {
    render_page('studio', [
        'route' => 'studio',
        'canonicalPath' => '/studio',
        'title' => 'Le Studio | Flexarion',
        'description' => "Découvrez l'équipe de Flexarion : vétérans et jeunes talents réunis à Aix-en-Provence pour créer des mondes dark fantasy sci-fi.",
    ]);
});

$router->get('/jeu', static function (): void {
    render_page('jeu', [
        'route' => 'jeu',
        'canonicalPath' => '/jeu',
        'title' => 'Chronos & Steel | Jeu RPG Dark Fantasy — Studio Flexarion',
        'description' => 'Chronos & Steel, jeu RPG dark fantasy du studio indépendant Flexarion : incarnez un chevalier face aux monstres dans la brume, entre acier et magie.',
    ]);
});

$router->get('/devlogs', static function (): void {
    render_page('devlogs', [
        'route' => 'devlogs',
        'canonicalPath' => '/devlogs',
        'title' => 'Devlogs | Journal de développement | Flexarion',
        'description' => 'Suivez le développement de Chronos & Steel par Flexarion : coulisses, système de combat, direction artistique et worldbuilding.',
    ]);
});

$router->get('/contact', static function (): void {
    $allowedSujets = ['general', 'support', 'recrutement', 'presse'];
    $sujet = (string) ($_GET['sujet'] ?? 'general');
    if (!in_array($sujet, $allowedSujets, true)) {
        $sujet = 'general';
    }

    render_page('contact', [
        'route' => 'contact',
        'canonicalPath' => '/contact',
        'title' => 'Contact | Flexarion',
        'description' => 'Contactez le studio Flexarion : questions, support, recrutement, presse et créateurs de contenu.',
        'old' => ['nom' => '', 'email' => '', 'sujet' => $sujet, 'message' => ''],
    ]);
});

$router->get('/robots.txt', static function (): void {
    header('Content-Type: text/plain; charset=utf-8');
    $origin = current_origin();
    echo "User-agent: *\n";
    echo "Allow: /\n";
    echo "Disallow: /app/\n";
    echo "Disallow: /views/\n";
    echo "Sitemap: {$origin}/sitemap.xml\n";
});

$router->get('/sitemap.xml', static function (): void {
    header('Content-Type: application/xml; charset=utf-8');
    $origin = current_origin();
    $urls = [
        ['loc' => '/',        'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => '/jeu',     'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => '/devlogs', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => '/studio',  'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
    ];
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($urls as $u) {
        $loc = Security::e($origin . $u['loc']);
        echo "  <url>\n";
        echo "    <loc>{$loc}</loc>\n";
        echo "    <changefreq>{$u['changefreq']}</changefreq>\n";
        echo "    <priority>{$u['priority']}</priority>\n";
        echo "  </url>\n";
    }
    echo "</urlset>\n";
});

$router->post('/contact', static function (): void {
    $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
        || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

    $respondError = static function (int $code, string $message, array $old = [], array $errors = []) use ($isAjax): void {
        http_response_code($code);
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok' => false,
                'message' => $message,
                'errors' => $errors,
            ], JSON_UNESCAPED_UNICODE);
            return;
        }
        render_page('contact', [
            'route' => 'contact',
            'title' => 'Contact | Flexarion',
            'description' => 'Contactez le studio Flexarion : questions, support, recrutement, presse et créateurs de contenu.',
            'old' => $old,
            'errors' => $errors,
            'statusMessage' => $message,
        ]);
    };

    // 1. Vérification CSRF
    if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
        $respondError(403, 'Session expirée ou jeton de sécurité invalide. Rechargez la page et réessayez.');
        return;
    }

    // 2. Honeypot anti-spam
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        $respondError(400, 'Requête rejetée.');
        return;
    }

    // 3. Limitation de débit (anti-flood)
    if (!Security::checkRateLimit('contact_last_submit', 10)) {
        $respondError(429, 'Veuillez patienter quelques secondes avant de renvoyer un message.');
        return;
    }

    // 4. Validation des champs
    $result = validate_contact($_POST);
    if (!empty($result['errors'])) {
        $respondError(422, 'Certains champs sont incomplets. Corrigez-les puis réessayez.', $result['old'], $result['errors']);
        return;
    }

    Security::markRateLimit('contact_last_submit');

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'ok' => true,
            'message' => 'Message transmis au studio Flexarion ! Nous vous répondrons sous 48 h.',
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    render_page('contact', [
        'route' => 'contact',
        'title' => 'Contact | Flexarion',
        'description' => 'Contactez le studio Flexarion : questions, support, recrutement, presse et créateurs de contenu.',
        'formSuccess' => true,
        'statusMessage' => 'Message transmis au studio Flexarion ! Nous vous répondrons sous 48 h.',
    ]);
});

// Redirections propres depuis les anciennes URLs .html
$legacyMap = [
    '/index.html' => '/',
    '/studio.html' => '/studio',
    '/jeu.html' => '/jeu',
    '/devlogs.html' => '/devlogs',
    '/contact.html' => '/contact',
];
foreach ($legacyMap as $oldPath => $newPath) {
    $router->get($oldPath, static function () use ($newPath): void {
        $query = $_SERVER['QUERY_STRING'] ?? '';
        $target = $newPath . ($query !== '' ? '?' . $query : '');
        header('Location: ' . $target, true, 301);
    });
}

$router->setNotFound(static function (): void {
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    render_page('404', [
        'route' => '404',
        'robots' => 'noindex, nofollow',
        'canonicalPath' => '/404',
        'title' => 'Erreur 404 — Page introuvable | Flexarion',
        'description' => 'La page demandée est introuvable sur le site de Flexarion.',
        'requestedPath' => is_string($rawPath) ? $rawPath : '/',
    ]);
});

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
