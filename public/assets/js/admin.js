/* admin.js – Rothe-Transporte Admin UI – vanilla, < 4 kB */
(function () {
  'use strict';

  /* ── Tab Toggle ──────────────────────────────────────────── */
  function initTabs() {
    var tabBtns = document.querySelectorAll('[data-tab]');
    if (!tabBtns.length) return;

    tabBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var targetId = btn.getAttribute('data-tab');
        var panels = document.querySelectorAll('[data-tab-panel]');
        var siblings = btn.parentElement.querySelectorAll('[data-tab]');

        // Deactivate all
        siblings.forEach(function (b) {
          b.setAttribute('aria-selected', 'false');
        });
        panels.forEach(function (p) {
          p.hidden = true;
        });

        // Activate selected
        btn.setAttribute('aria-selected', 'true');
        var panel = document.getElementById(targetId);
        if (panel) panel.hidden = false;
      });
    });

    // Show first tab panel if none active
    var firstPanel = document.querySelector('[data-tab-panel]');
    if (firstPanel) {
      var allHidden = Array.from(document.querySelectorAll('[data-tab-panel]')).every(function (p) {
        return p.hidden;
      });
      if (allHidden) firstPanel.hidden = false;
    }
  }

  /* ── Live Counter for meta inputs ────────────────────────── */
  function initCounters() {
    var counters = document.querySelectorAll('[data-counter-target]');
    counters.forEach(function (hint) {
      var targetName = hint.getAttribute('data-counter-target');
      var min = parseInt(hint.getAttribute('data-min') || '0', 10);
      var max = parseInt(hint.getAttribute('data-max') || '999', 10);

      // Find input by name OR by id matching target name
      var input = document.querySelector('[name="' + targetName + '"]') ||
                  document.getElementById(targetName);
      if (!input) return;

      function update() {
        var len = input.value.length;
        hint.textContent = len + ' Zeichen';
        hint.className = 'counter-hint';
        if (len >= min && len <= max) {
          hint.classList.add('counter-hint--ok');
        } else if (len > max) {
          hint.classList.add('counter-hint--over');
        } else {
          hint.classList.add('counter-hint--warn');
        }
      }

      input.addEventListener('input', update);
      update();
    });
  }

  /* ── Confirm Buttons ─────────────────────────────────────── */
  function initConfirm() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-confirm]');
      if (!btn) return;
      var msg = btn.getAttribute('data-confirm') || 'Wirklich fortfahren?';
      if (!window.confirm(msg)) {
        e.preventDefault();
      }
    });
  }

  /* ── File Preview ────────────────────────────────────────── */
  function initFilePreview() {
    var inputs = document.querySelectorAll('[data-file-preview]');
    inputs.forEach(function (input) {
      var previewId = input.getAttribute('data-file-preview');
      var preview = document.getElementById(previewId);
      if (!preview) return;

      input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) { preview.hidden = true; return; }
        if (!file.type.startsWith('image/')) { preview.hidden = true; return; }

        var reader = new FileReader();
        reader.onload = function (ev) {
          preview.src = ev.target.result;
          preview.hidden = false;
        };
        reader.readAsDataURL(file);
      });
    });
  }

  /* ── Slug Generator ──────────────────────────────────────── */
  function toSlug(str) {
    return str
      .toLowerCase()
      .replace(/ä/g, 'ae').replace(/ö/g, 'oe').replace(/ü/g, 'ue')
      .replace(/ß/g, 'ss')
      .replace(/[^a-z0-9\s-]/g, '')
      .trim()
      .replace(/[\s]+/g, '-')
      .replace(/-{2,}/g, '-');
  }

  function initSlugGenerator() {
    var sources = document.querySelectorAll('[data-slug-source]');
    sources.forEach(function (source) {
      // Find slug target in same form
      var form = source.closest('form');
      if (!form) return;
      var target = form.querySelector('[data-slug-target]');
      if (!target) return;
      if (target.readOnly) return;

      var userEdited = target.value.length > 0;

      target.addEventListener('input', function () {
        userEdited = true;
      });

      source.addEventListener('input', function () {
        if (!userEdited) {
          target.value = toSlug(source.value);
        }
      });
    });
  }

  /* ── Flash Message Dismiss ───────────────────────────────── */
  function initFlashDismiss() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.flash__dismiss');
      if (!btn) return;
      var flash = btn.closest('.flash');
      if (flash) flash.remove();
    });
  }

  /* ── Mobile Sidebar Toggle ───────────────────────────────── */
  function initSidebar() {
    var toggle = document.getElementById('sidebarToggle');
    var sidebar = document.getElementById('adminSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    if (!toggle || !sidebar) return;

    function openSidebar() {
      sidebar.classList.add('is-open');
      if (overlay) overlay.classList.add('is-visible');
      toggle.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
      sidebar.classList.remove('is-open');
      if (overlay) overlay.classList.remove('is-visible');
      toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', function () {
      if (sidebar.classList.contains('is-open')) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });

    if (overlay) {
      overlay.addEventListener('click', closeSidebar);
    }

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
        closeSidebar();
        toggle.focus();
      }
    });
  }

  /* ── Init ────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    initTabs();
    initCounters();
    initConfirm();
    initFilePreview();
    initSlugGenerator();
    initFlashDismiss();
    initSidebar();
    initMediaPicker();
  });

  // ── Media picker (modal grid for image_id fields) ─────────────────────
  function initMediaPicker() {
    document.querySelectorAll('[data-picker-open]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var modal = document.getElementById(btn.dataset.pickerOpen);
        if (modal) modal.hidden = false;
      });
    });
    document.querySelectorAll('[data-picker-close]').forEach(function (el) {
      el.addEventListener('click', function () {
        var modal = document.getElementById(el.dataset.pickerClose);
        if (modal) modal.hidden = true;
      });
    });
    document.querySelectorAll('[data-picker-search]').forEach(function (input) {
      input.addEventListener('input', function () {
        var grid = input.closest('.media-modal').querySelector('[data-picker-grid]');
        if (!grid) return;
        var q = (input.value || '').toLowerCase().trim();
        grid.querySelectorAll('.media-tile').forEach(function (t) {
          if (!q) { t.classList.remove('is-hidden'); return; }
          var hay = (t.dataset.search || '').toLowerCase();
          t.classList.toggle('is-hidden', hay.indexOf(q) === -1);
        });
      });
    });
    document.querySelectorAll('[data-picker-pick]').forEach(function (tile) {
      tile.addEventListener('click', function () {
        var pickerId = tile.dataset.pickerPick;
        var picker = document.querySelector('[data-picker="' + pickerId + '"]');
        if (!picker) return;
        var input = picker.querySelector('[data-picker-input]');
        if (input) input.value = tile.dataset.id || '';
        var current = picker.querySelector('[data-picker-current]');
        if (current) {
          var meta = (tile.dataset.w && tile.dataset.h)
            ? '#' + tile.dataset.id + ' · ' + tile.dataset.w + '×' + tile.dataset.h
            : '#' + tile.dataset.id;
          current.innerHTML = ''
            + '<img src="' + (tile.dataset.thumb || '') + '" alt="' + (tile.dataset.alt || '') + '" width="120" height="80" loading="lazy">'
            + '<div class="media-picker__meta"><strong>' + (tile.dataset.name || '') + '</strong>'
            + '<span class="muted">' + meta + '</span></div>';
        }
        var modal = document.getElementById(pickerId);
        if (modal) modal.hidden = true;
      });
    });
    document.querySelectorAll('[data-picker-clear]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var picker = document.querySelector('[data-picker="' + btn.dataset.pickerClear + '"]');
        if (!picker) return;
        var input = picker.querySelector('[data-picker-input]');
        if (input) input.value = '0';
        var current = picker.querySelector('[data-picker-current]');
        if (current) current.innerHTML = '<span class="muted">Kein Bild ausgewaehlt</span>';
      });
    });
    document.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      document.querySelectorAll('.media-modal').forEach(function (m) {
        if (!m.hidden) m.hidden = true;
      });
    });
  }

}());
