<?php
use App\Security;

$old = $old ?? ['nom' => '', 'email' => '', 'sujet' => 'general', 'message' => ''];
$errors = $errors ?? [];
$formSuccess = $formSuccess ?? false;
$statusMessage = $statusMessage ?? '';
$csrfToken = $csrfToken ?? '';
$statusClass = $formSuccess ? ' is-success' : (!empty($errors) || $statusMessage !== '' ? ' is-error' : '');
$statusIcon = $formSuccess ? '#i-circle-check' : '#i-circle-alert';
?>
    <section class="section">
      <div class="container container--md contact-layout">

        <!-- Formulaire -->
        <div class="panel box-glow-red">
          <h1>Contacter Flexarion</h1>
          <p class="form__hint">Les champs marqués d'un <span class="text-crimson">*</span> sont obligatoires.</p>

          <form id="contact-form" class="form" action="/contact" method="post" novalidate>
            <input type="hidden" name="_csrf" value="<?= Security::e($csrfToken) ?>">
            <div class="hp-field" aria-hidden="true">
              <label for="website">Ne pas remplir ce champ</label>
              <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

            <div class="form__row">
              <div class="field<?= isset($errors['nom']) ? ' has-error' : '' ?>">
                <label for="nom">Nom / Pseudo <span class="req" aria-hidden="true">*</span></label>
                <input id="nom" name="nom" type="text" autocomplete="name" required minlength="2" maxlength="80" placeholder="Votre nom" value="<?= Security::e($old['nom']) ?>" aria-describedby="err-nom"<?= isset($errors['nom']) ? ' aria-invalid="true"' : '' ?>>
                <p id="err-nom" class="field__error"><svg class="icon" aria-hidden="true"><use href="#i-circle-alert"/></svg><span><?= Security::e($errors['nom'] ?? 'Indiquez votre nom ou pseudo.') ?></span></p>
              </div>
              <div class="field<?= isset($errors['email']) ? ' has-error' : '' ?>">
                <label for="email">E-mail <span class="req" aria-hidden="true">*</span></label>
                <input id="email" name="email" type="email" autocomplete="email" inputmode="email" required maxlength="120" placeholder="email@exemple.com" value="<?= Security::e($old['email']) ?>" aria-describedby="err-email"<?= isset($errors['email']) ? ' aria-invalid="true"' : '' ?>>
                <p id="err-email" class="field__error"><svg class="icon" aria-hidden="true"><use href="#i-circle-alert"/></svg><span><?= Security::e($errors['email'] ?? 'Indiquez une adresse e-mail valide (ex : nom@exemple.com).') ?></span></p>
              </div>
            </div>

            <div class="field">
              <label for="sujet">Sujet</label>
              <div class="select-wrap">
                <select id="sujet" name="sujet">
                  <option value="general"<?= $old['sujet'] === 'general' ? ' selected' : '' ?>>Question générale</option>
                  <option value="support"<?= $old['sujet'] === 'support' ? ' selected' : '' ?>>Support / Bug</option>
                  <option value="recrutement"<?= $old['sujet'] === 'recrutement' ? ' selected' : '' ?>>Recrutement</option>
                </select>
                <svg class="icon" aria-hidden="true"><use href="#i-chevron-down"/></svg>
              </div>
            </div>

            <div class="field<?= isset($errors['message']) ? ' has-error' : '' ?>">
              <label for="message">Message <span class="req" aria-hidden="true">*</span></label>
              <textarea id="message" name="message" rows="5" required minlength="10" maxlength="2000" placeholder="Transmettez votre message par onde quantique…" aria-describedby="err-message"<?= isset($errors['message']) ? ' aria-invalid="true"' : '' ?>><?= Security::e($old['message']) ?></textarea>
              <p id="err-message" class="field__error"><svg class="icon" aria-hidden="true"><use href="#i-circle-alert"/></svg><span><?= Security::e($errors['message'] ?? 'Votre message doit contenir au moins 10 caractères.') ?></span></p>
            </div>

            <div id="form-status" class="form-status<?= $statusClass ?>" role="status" aria-live="polite">
              <svg class="icon" aria-hidden="true"><use href="<?= $statusIcon ?>"/></svg>
              <span class="form-status__text"><?= Security::e($statusMessage) ?></span>
            </div>

            <button class="btn btn--crimson btn--lg btn--block" type="submit">
              <svg class="icon" aria-hidden="true"><use href="#i-send"/></svg> Envoyer le message
            </button>
          </form>
        </div>

        <!-- Infos -->
        <div class="aside">
          <section class="panel panel--plain info-card" aria-labelledby="loc-title">
            <svg class="icon info-card__icon text-muted" aria-hidden="true"><use href="#i-map-pin"/></svg>
            <h2 id="loc-title" class="info-card__title--sm">Localisation</h2>
            <address>
              Studio Flexarion<br>
              13100 Aix-en-Provence<br>
              France<br>
              <a class="mail-link" href="mailto:mysticalknight2@gmail.com">mysticalknight2@gmail.com</a>
            </address>
          </section>
        </div>

      </div>
    </section>
