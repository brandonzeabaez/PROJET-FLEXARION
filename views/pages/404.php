<?php
use App\Security;

$requestedPath = $requestedPath ?? '/';
?>
    <section class="hero" aria-labelledby="notfound-title">
      <div class="embers" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>
      <div class="hero__content">
        <p class="hero__kicker">Erreur 404 — Secteur introuvable</p>
        <h1 id="notfound-title" class="hero__title">
          Cette <span class="text-blood glow-dark-red">catacombe</span> est scellée.
        </h1>
        <p class="hero__text">
          L'adresse <code class="notfound-path"><?= Security::e($requestedPath) ?></code> n'existe pas ou s'est perdue dans la brume de Mystical Knight.
        </p>
        <div class="hero__actions">
          <a class="btn btn--crimson btn--lg" href="/">
            <svg class="icon" aria-hidden="true"><use href="#i-arrow-right"/></svg> Retour à l'accueil
          </a>
          <a class="btn btn--ghost btn--lg" href="/jeu">
            <svg class="icon" aria-hidden="true"><use href="#i-gamepad-2"/></svg> Découvrir Mystical Knight
          </a>
        </div>
        <nav class="notfound-links" aria-label="Pages disponibles">
          <a href="/studio">Le Studio</a>
          <span aria-hidden="true">·</span>
          <a href="/devlogs">Devlogs</a>
          <span aria-hidden="true">·</span>
          <a href="/contact">Contact</a>
        </nav>
      </div>
    </section>
