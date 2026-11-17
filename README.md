# Forge & Code — Site vitrine

Site statique (HTML / CSS / JS natif) du studio **Forge & Code**, réalisé à partir de la maquette `maquette_forge_code.html`.

## Lancer le site

Ouvrir `index.html` dans un navigateur, ou servir le dossier :

```bash
python3 -m http.server 8000
# puis http://localhost:8000
```

## Structure

```
index.html      Accueil (hero + « En direct de la Forge »)
studio.html     Le Studio (équipe + recrutement)
jeu.html        Le Jeu — Chronos & Steel (trailer + piliers)
devlogs.html    Journal de développement (articles dépliables)
contact.html    Formulaire de contact + presse + localisation
assets/
  css/style.css   Feuille de style unique, mobile-first
  js/main.js      Menu mobile, animations, trailer, validation du formulaire
  fonts/          Cinzel + Rajdhani auto-hébergées (woff2, sous-ensemble latin)
  img/            Illustrations SVG (remplaçables par de vrais visuels)
```

## Choix techniques (performance)

| Maquette | Site final | Gain |
| :--- | :--- | :--- |
| Tailwind CDN (~300 Ko de JS, compilation dans le navigateur) | CSS écrit à la main (~30 Ko non minifié) | Plus de JS bloquant |
| FontAwesome complet (~100 Ko CSS + polices) | Sprite SVG en ligne, uniquement les icônes utilisées | ≈ 0 requête |
| Google Fonts (3 domaines externes) | Polices auto-hébergées + `preload` + `font-display: swap` | Pas de connexion tierce |
| Images placehold.co | SVG locaux avec `width`/`height` + `loading="lazy"` | Pas de décalage de mise en page (CLS) |
| `blur(150px)` sur de grands calques | Dégradés radiaux | Rendu GPU plus léger |
| Une seule page, navigation JS | 5 vraies pages | URL partageables, bouton « Retour », SEO |

La vidéo du trailer utilise une « façade » : YouTube n'est chargé qu'au clic.
Pour l'activer, renseigner l'ID de la vidéo dans `jeu.html` : `data-youtube-id="XXXXXXXXXXX"`.

## Responsive & ergonomie

- Points de rupture : 640 px, 768 px (menu burger ↔ menu complet), 1024 px.
- Zones tactiles ≥ 44 px, champs de formulaire à 16 px (pas de zoom auto sur iOS).
- Lien « Aller au contenu », focus clavier visible, `aria-current` sur la page active.
- Menu mobile : `aria-expanded`, fermeture avec Échap ou clic extérieur.
- Contrastes AA : les rouges de texte sont éclaircis (`--crimson-text`, `--blood-text`), les rouges sombres d'origine restent pour les bordures et les lueurs.
- Formulaire : validation accessible (messages sous les champs, `aria-invalid`, statut annoncé). Le sujet peut être pré-rempli : `contact.html?sujet=recrutement`.
- `prefers-reduced-motion` respecté (animations coupées).

> Le formulaire n'a pas encore de serveur : l'envoi est simulé côté navigateur.
