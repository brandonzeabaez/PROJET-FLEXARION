<?php

declare(strict_types=1);

use App\Security;

/**
 * Gabarit principal du site Flexarion.
 * Variables attendues : $pageTitle, $pageDescription, $activeRoute, $cspNonce, $content.
 */
$navItems = [
    ['key' => 'accueil', 'href' => '/',        'label' => 'Accueil',   'class' => ''],
    ['key' => 'studio',  'href' => '/studio',  'label' => 'Le Studio', 'class' => ''],
    ['key' => 'jeu',     'href' => '/jeu',     'label' => 'Le Jeu',    'class' => 'nav-link--blood'],
    ['key' => 'devlogs', 'href' => '/devlogs', 'label' => 'Devlogs',   'class' => ''],
    ['key' => 'contact', 'href' => '/contact', 'label' => 'Contact',   'class' => ''],
];

ob_start();
?>
  <a class="skip-link" href="#contenu">Aller au contenu</a>

  <header class="site-header">
    <div class="container site-header__inner">
      <a class="logo-brand" href="/" aria-label="Flexarion — accueil">
        <img class="logo-img" src="/assets/img/logo-flexarion.jpg" alt="Flexarion" width="220" height="54" decoding="async">
      </a>

      <nav class="nav-desktop" aria-label="Navigation principale">
        <ul>
          <?php foreach ($navItems as $item): ?>
            <?php
              $classes = trim('nav-link ' . $item['class']);
              $current = ($activeRoute === $item['key']) ? ' aria-current="page"' : '';
            ?>
            <li><a class="<?= Security::e($classes) ?>" href="<?= $item['href'] ?>"<?= $current ?>><?= Security::e($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Ouvrir le menu">
        <svg class="icon icon-open" aria-hidden="true"><use href="#i-menu"/></svg>
        <svg class="icon icon-close" aria-hidden="true"><use href="#i-x"/></svg>
      </button>
    </div>

    <nav id="mobile-menu" class="nav-mobile" aria-label="Navigation mobile">
      <ul>
        <?php foreach ($navItems as $item): ?>
          <?php
            $classAttr = $item['class'] !== '' ? ' class="' . Security::e($item['class']) . '"' : '';
            $current = ($activeRoute === $item['key']) ? ' aria-current="page"' : '';
          ?>
          <li><a<?= $classAttr ?> href="<?= $item['href'] ?>"<?= $current ?>><?= Security::e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </header>

  <main id="contenu" tabindex="-1">
<?= $content ?>
  </main>

  <footer class="site-footer">
    <div class="container site-footer__inner">
      <div class="site-footer__brand">
        <a class="logo-brand logo-brand--sm" href="/" aria-label="Flexarion — accueil">
          <img class="logo-img" src="/assets/img/logo-flexarion.jpg" alt="Flexarion" width="160" height="40" loading="lazy" decoding="async">
        </a>
        <small>© <?= date('Y') ?> Flexarion — Tous droits réservés.</small>
      </div>
      <nav class="footer-nav" aria-label="Plan du site">
        <ul>
          <?php foreach ($navItems as $item): ?>
            <?php $current = ($activeRoute === $item['key']) ? ' aria-current="page"' : ''; ?>
            <li><a href="<?= $item['href'] ?>"<?= $current ?>><?= Security::e($item['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <ul class="socials">
        <li><a href="https://discord.com" target="_blank" rel="noopener noreferrer" aria-label="Discord (nouvel onglet)"><svg class="icon" aria-hidden="true"><use href="#i-discord"/></svg></a></li>
        <li><a href="https://www.tiktok.com" target="_blank" rel="noopener noreferrer" aria-label="TikTok (nouvel onglet)"><svg class="icon" aria-hidden="true"><use href="#i-tiktok"/></svg></a></li>
        <li><a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram (nouvel onglet)"><svg class="icon" aria-hidden="true"><use href="#i-instagram"/></svg></a></li>
        <li><a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube (nouvel onglet)"><svg class="icon" aria-hidden="true"><use href="#i-youtube"/></svg></a></li>
      </ul>
    </div>
  </footer>

  <button class="to-top" type="button" aria-label="Revenir en haut de la page">
    <svg class="icon" aria-hidden="true"><use href="#i-arrow-up"/></svg>
  </button>
<?php
$bodyHtml = ob_get_clean();

preg_match_all('/href="#i-([a-z0-9-]+)"/', $bodyHtml, $matches);
$usedIcons = $matches[1] ?? [];
if ($activeRoute === 'contact') {
    $usedIcons = array_merge($usedIcons, ['circle-check', 'circle-alert']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= Security::e($pageTitle) ?></title>
  <meta name="description" content="<?= Security::e($pageDescription) ?>">
  <meta name="theme-color" content="#030000">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="fr_FR">
  <meta property="og:site_name" content="Flexarion">
  <meta property="og:title" content="<?= Security::e($pageTitle) ?>">
  <meta property="og:description" content="<?= Security::e($pageDescription) ?>">
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preload" href="/assets/fonts/rajdhani-500-latin.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="/assets/fonts/cinzel-latin.woff2" as="font" type="font/woff2" crossorigin>
  <link rel="stylesheet" href="/assets/css/style.css">
  <script nonce="<?= Security::e($cspNonce) ?>">document.documentElement.classList.add("js")</script>
  <script src="/assets/js/main.js" defer></script>
</head>
<body>
  <?= render_svg_sprite($usedIcons) ?>
<?= $bodyHtml ?>
</body>
</html>
