/* ══════════════════════════════════════════════════════════════
   READ MORE / READ LESS — Auto-initializing component
   Finds all .read-more elements, measures height, and clamps
   those exceeding ~3 lines. Adds a toggle button.
   ══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var DEFAULT_LINES = 3;
  var EXTRA_PX = 8;
  var TOLERANCE = 12;

  function getLineHeight(el) {
    var computed = window.getComputedStyle(el);
    return parseFloat(computed.lineHeight) || 24;
  }

  function initReadMore() {
    var elements = document.querySelectorAll('.read-more');

    for (var i = 0; i < elements.length; i++) {
      var el = elements[i];

      if (el.dataset.rmInit === 'true') continue;

      /* Measure natural (unclamped) height */
      var savedMax = el.style.maxHeight;
      var savedOver = el.style.overflow;
      el.style.maxHeight = 'none';
      el.style.overflow = 'visible';

      var naturalHeight = el.scrollHeight;

      el.style.maxHeight = savedMax;
      el.style.overflow = savedOver;

      var lineHeight = getLineHeight(el);
      var collapsedHeight = lineHeight * DEFAULT_LINES + EXTRA_PX;

      if (naturalHeight <= collapsedHeight + TOLERANCE) continue;

      el.dataset.rmInit = 'true';

      /* Wrap inner content */
      var content = document.createElement('div');
      content.className = 'read-more-content';
      while (el.firstChild) content.appendChild(el.firstChild);
      el.appendChild(content);

      /* Create toggle button */
      var toggle = document.createElement('button');
      toggle.className = 'read-more-toggle';
      toggle.type = 'button';
      toggle.setAttribute('aria-expanded', 'false');
      toggle.innerHTML = 'Lire plus <span class="rm-chevron">&#9662;</span>';
      el.appendChild(toggle);

      /* CSS custom properties */
      el.style.setProperty('--rm-collapsed-height', collapsedHeight + 'px');
      el.style.setProperty('--rm-expanded-height', (naturalHeight + EXTRA_PX) + 'px');

      el.classList.add('is-clamped');

      /* Click handler */
      (function (elRef, btnRef) {
        btnRef.addEventListener('click', function () {
          var expanded = elRef.classList.contains('is-expanded');
          if (expanded) {
            elRef.classList.remove('is-expanded');
            elRef.classList.add('is-clamped');
            btnRef.setAttribute('aria-expanded', 'false');
            btnRef.innerHTML = 'Lire plus <span class="rm-chevron">&#9662;</span>';
          } else {
            elRef.classList.remove('is-clamped');
            elRef.classList.add('is-expanded');
            btnRef.setAttribute('aria-expanded', 'true');
            btnRef.innerHTML = 'Lire moins <span class="rm-chevron">&#9662;</span>';
          }
        });
      })(el, toggle);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReadMore);
  } else {
    initReadMore();
  }

  window.ReadMore = { init: initReadMore };
})();
