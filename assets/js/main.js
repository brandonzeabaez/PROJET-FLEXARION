/* =========================================================
   FORGE & CODE — Interactions & Animations (100 % JS)
   Vanilla JS, chargé en "defer", aucune dépendance.
   ========================================================= */
(function () {
  "use strict";

  var reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var canAnimate = !reducedMotion && typeof Element.prototype.animate === "function";
  var EASE_OUT = "cubic-bezier(0.22, 1, 0.36, 1)";

  /* ---------- Animations des braises (Hero / 404) ---------- */
  if (canAnimate) {
    var emberConfigs = [
      { duration: 9000, delay: 0 },
      { duration: 11000, delay: 3000 },
      { duration: 9000, delay: 6000 },
      { duration: 12000, delay: 1500 },
      { duration: 9000, delay: 4500 },
      { duration: 10000, delay: 7500 },
      { duration: 9000, delay: 2200 },
      { duration: 13000, delay: 5200 }
    ];
    document.querySelectorAll(".embers span").forEach(function (ember, i) {
      var cfg = emberConfigs[i % emberConfigs.length];
      ember.animate(
        [
          { transform: "translate3d(0, 0, 0) scale(1)", opacity: 0, offset: 0 },
          { opacity: 0.9, offset: 0.1 },
          { transform: "translate3d(30px, -85vh, 0) scale(0.3)", opacity: 0, offset: 1 }
        ],
        { duration: cfg.duration, delay: cfg.delay, iterations: Infinity, easing: "linear" }
      );
    });
  }

  /* ---------- Menu mobile ---------- */
  var toggle = document.querySelector(".menu-toggle");
  var menu = document.getElementById("mobile-menu");

  function setMenu(open) {
    if (!toggle || !menu) return;
    var wasOpen = menu.classList.contains("is-open");
    toggle.setAttribute("aria-expanded", String(open));
    toggle.setAttribute("aria-label", open ? "Fermer le menu" : "Ouvrir le menu");

    if (open && !wasOpen) {
      menu.classList.add("is-open");
      if (canAnimate) {
        menu.animate(
          [
            { opacity: 0, transform: "translateY(-0.5rem)" },
            { opacity: 1, transform: "translateY(0)" }
          ],
          { duration: 240, easing: EASE_OUT }
        );
      }
    } else if (!open && wasOpen) {
      if (canAnimate) {
        var anim = menu.animate(
          [
            { opacity: 1, transform: "translateY(0)" },
            { opacity: 0, transform: "translateY(-0.5rem)" }
          ],
          { duration: 180, easing: "ease-in" }
        );
        anim.onfinish = function () { menu.classList.remove("is-open"); };
      } else {
        menu.classList.remove("is-open");
      }
    }
  }

  if (toggle && menu) {
    toggle.addEventListener("click", function () {
      setMenu(toggle.getAttribute("aria-expanded") !== "true");
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && toggle.getAttribute("aria-expanded") === "true") {
        setMenu(false);
        toggle.focus();
      }
    });
    document.addEventListener("click", function (e) {
      if (toggle.getAttribute("aria-expanded") === "true" &&
          !menu.contains(e.target) && !toggle.contains(e.target)) {
        setMenu(false);
      }
    });
    window.matchMedia("(min-width: 768px)").addEventListener("change", function (mq) {
      if (mq.matches) setMenu(false);
    });
  }

  /* ---------- Apparition au défilement ---------- */
  var reveals = document.querySelectorAll(".reveal");
  if (canAnimate && "IntersectionObserver" in window && reveals.length) {
    reveals.forEach(function (el) {
      el.style.opacity = "0";
      el.style.transform = "translateY(24px)";
    });
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var target = entry.target;
        io.unobserve(target);
        target.animate(
          [
            { opacity: 0, transform: "translateY(24px)" },
            { opacity: 1, transform: "translateY(0)" }
          ],
          { duration: 650, easing: EASE_OUT, fill: "forwards" }
        ).onfinish = function () {
          target.style.opacity = "";
          target.style.transform = "";
          target.classList.add("is-visible");
        };
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.1 });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add("is-visible"); });
  }

  /* ---------- Animations de survol & détails (JS) ---------- */
  if (canAnimate && window.matchMedia("(hover: hover)").matches) {
    // Reflet sur les boutons
    document.querySelectorAll(".btn").forEach(function (btn) {
      btn.addEventListener("mouseenter", function () {
        var sheen = document.createElement("span");
        sheen.setAttribute("aria-hidden", "true");
        sheen.style.cssText =
          "position:absolute;inset:0;pointer-events:none;z-index:-1;" +
          "background:linear-gradient(90deg,transparent,rgba(255,255,255,0.22),transparent);";
        btn.appendChild(sheen);
        sheen.animate(
          [{ transform: "translateX(-100%)" }, { transform: "translateX(100%)" }],
          { duration: 480, easing: "ease-out" }
        ).onfinish = function () { sheen.remove(); };
      });
    });

    // Zoom léger sur les images des cartes sociales et devlogs
    document.querySelectorAll(".social-card, .devlog").forEach(function (card) {
      var img = card.querySelector("img");
      if (!img) return;
      card.addEventListener("mouseenter", function () {
        img.animate([{ transform: "scale(1)" }, { transform: "scale(1.04)" }],
          { duration: 450, easing: EASE_OUT, fill: "forwards" });
      });
      card.addEventListener("mouseleave", function () {
        img.animate([{ transform: "scale(1.04)" }, { transform: "scale(1)" }],
          { duration: 350, easing: EASE_OUT, fill: "forwards" });
      });
    });
  }

  // Flèche des articles devlog (<details>)
  document.querySelectorAll(".devlog details").forEach(function (details) {
    var icon = details.querySelector("summary .icon");
    if (!icon) return;
    details.addEventListener("toggle", function () {
      if (!canAnimate) {
        icon.style.transform = details.open ? "rotate(90deg)" : "rotate(0deg)";
        return;
      }
      icon.animate(
        [
          { transform: details.open ? "rotate(0deg)" : "rotate(90deg)" },
          { transform: details.open ? "rotate(90deg)" : "rotate(0deg)" }
        ],
        { duration: 250, easing: EASE_OUT, fill: "forwards" }
      );
    });
  });

  /* ---------- Bouton retour en haut (défilement doux en JS) ---------- */
  function smoothScrollTop(onDone) {
    if (reducedMotion || window.scrollY === 0) {
      window.scrollTo(0, 0);
      if (onDone) onDone();
      return;
    }
    var startY = window.scrollY;
    var startTime = performance.now();
    var duration = Math.min(600, Math.max(250, startY * 0.35));

    function step(now) {
      var t = Math.min(1, (now - startTime) / duration);
      var eased = 1 - Math.pow(1 - t, 3);
      window.scrollTo(0, Math.round(startY * (1 - eased)));
      if (t < 1) {
        requestAnimationFrame(step);
      } else if (onDone) {
        onDone();
      }
    }
    requestAnimationFrame(step);
  }

  var toTop = document.querySelector(".to-top");
  if (toTop) {
    var ticking = false;
    var visible = false;
    window.addEventListener("scroll", function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        var shouldShow = window.scrollY > 600;
        if (shouldShow !== visible) {
          visible = shouldShow;
          if (shouldShow) {
            toTop.classList.add("is-visible");
            if (canAnimate) {
              toTop.animate(
                [{ opacity: 0, transform: "translateY(1rem)" }, { opacity: 1, transform: "translateY(0)" }],
                { duration: 250, easing: EASE_OUT }
              );
            }
          } else if (canAnimate) {
            toTop.animate(
              [{ opacity: 1, transform: "translateY(0)" }, { opacity: 0, transform: "translateY(1rem)" }],
              { duration: 200, easing: "ease-in" }
            ).onfinish = function () { if (!visible) toTop.classList.remove("is-visible"); };
          } else {
            toTop.classList.remove("is-visible");
          }
        }
        ticking = false;
      });
    }, { passive: true });

    toTop.addEventListener("click", function () {
      smoothScrollTop(function () {
        var skip = document.querySelector(".skip-link");
        if (skip) skip.focus({ preventScroll: true });
      });
    });
  }

  /* ---------- Bande-annonce (façade : la vidéo n'est chargée qu'au clic) ---------- */
  var trailer = document.querySelector("[data-trailer]");
  if (trailer) {
    var btn = trailer.querySelector(".trailer__btn");
    btn.addEventListener("click", function () {
      var id = trailer.getAttribute("data-youtube-id");
      if (!id) {
        var label = trailer.querySelector(".trailer__label");
        label.textContent = "Bande-annonce bientôt disponible";
        label.setAttribute("role", "status");
        return;
      }
      var iframe = document.createElement("iframe");
      iframe.src = "https://www.youtube-nocookie.com/embed/" + encodeURIComponent(id) + "?autoplay=1&rel=0";
      iframe.title = "Bande-annonce de Chronos & Steel";
      iframe.allow = "accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture";
      iframe.allowFullscreen = true;
      trailer.replaceChildren(iframe);
      iframe.focus();
    });
  }

  /* ---------- Formulaire de contact (validation + envoi AJAX vers PHP) ---------- */
  var form = document.getElementById("contact-form");
  if (form) {
    var status = document.getElementById("form-status");
    var statusText = status.querySelector(".form-status__text");

    function validateField(input) {
      var field = input.closest(".field");
      var valid = input.checkValidity();
      field.classList.toggle("has-error", !valid);
      input.setAttribute("aria-invalid", String(!valid));
      return valid;
    }

    form.querySelectorAll("input, textarea").forEach(function (input) {
      input.addEventListener("blur", function () {
        if (input.value.trim() !== "") validateField(input);
      });
      input.addEventListener("input", function () {
        if (input.closest(".field").classList.contains("has-error")) validateField(input);
      });
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      status.className = "form-status";

      var firstInvalid = null;
      form.querySelectorAll("input, textarea").forEach(function (input) {
        if (!validateField(input) && !firstInvalid) firstInvalid = input;
      });

      if (firstInvalid) {
        status.classList.add("is-error");
        status.querySelector("use").setAttribute("href", "#i-circle-alert");
        statusText.textContent = "Certains champs sont incomplets. Corrigez-les puis réessayez.";
        firstInvalid.focus();
        return;
      }

      var submit = form.querySelector('[type="submit"]');
      submit.disabled = true;

      fetch(form.action, {
        method: "POST",
        headers: {
          "Accept": "application/json",
          "X-Requested-With": "XMLHttpRequest"
        },
        body: new FormData(form)
      })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function (result) {
          if (result.ok && result.data.ok) {
            status.classList.add("is-success");
            status.querySelector("use").setAttribute("href", "#i-circle-check");
            statusText.textContent = result.data.message;
            form.reset();
            form.querySelectorAll("[aria-invalid]").forEach(function (el) { el.removeAttribute("aria-invalid"); });
          } else {
            status.classList.add("is-error");
            status.querySelector("use").setAttribute("href", "#i-circle-alert");
            statusText.textContent = result.data.message || "Une erreur est survenue.";
          }
        })
        .catch(function () {
          status.classList.add("is-error");
          status.querySelector("use").setAttribute("href", "#i-circle-alert");
          statusText.textContent = "Impossible de joindre le serveur. Réessayez dans un instant.";
        })
        .finally(function () {
          submit.disabled = false;
        });
    });
  }
})();
