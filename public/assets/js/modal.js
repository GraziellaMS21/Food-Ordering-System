(function () {
  'use strict';

  var queue = [];
  var current = null;
  var lastFocus = null;

  function ensureContainer() {
    var c = document.getElementById('app-modal-root');
    if (!c) {
      c = document.createElement('div');
      c.id = 'app-modal-root';
      document.body.appendChild(c);
    }
    return c;
  }

  function lockScroll(lock) {
    document.documentElement.style.overflow = lock ? 'hidden' : '';
    document.body.style.overflow = lock ? 'hidden' : '';
  }

  function trapFocus(modal) {
    var focusable = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable.length === 0) return;
    var first = focusable[0];
    var last = focusable[focusable.length - 1];
    modal.addEventListener('keydown', function (e) {
      if (e.key !== 'Tab') return;
      if (e.shiftKey) {
        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
      } else {
        if (document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });
  }

  function build(opts) {
    opts = opts || {};
    var variant = opts.variant || 'confirm';
    var iconColor = 'orange';
    var iconBg = 'bg-orange-100 text-orange-600';
    var btnClass = 'bg-orange-600 hover:bg-orange-700';
    var icon = 'fa-circle-question';
    var role = 'dialog';

    if (variant === 'danger') {
      iconBg = 'bg-rose-100 text-rose-600'; icon = 'fa-triangle-exclamation'; btnClass = 'bg-rose-600 hover:bg-rose-700'; role = 'alertdialog';
    } else if (variant === 'success') {
      iconBg = 'bg-emerald-100 text-emerald-600'; icon = 'fa-circle-check'; btnClass = 'bg-emerald-600 hover:bg-emerald-700';
    } else if (variant === 'error') {
      iconBg = 'bg-rose-100 text-rose-600'; icon = 'fa-circle-xmark'; btnClass = 'bg-stone-900 hover:bg-stone-800'; role = 'alertdialog';
    } else if (variant === 'warning') {
      iconBg = 'bg-amber-100 text-amber-600'; icon = 'fa-exclamation'; btnClass = 'bg-amber-600 hover:bg-amber-700';
    } else if (variant === 'info') {
      iconBg = 'bg-sky-100 text-sky-600'; icon = 'fa-circle-info'; btnClass = 'bg-sky-600 hover:bg-sky-700';
    }

    var root = ensureContainer();
    var backdrop = document.createElement('div');
    backdrop.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-200';
    backdrop.setAttribute('role', role);
    backdrop.setAttribute('aria-modal', 'true');
    backdrop.setAttribute('aria-labelledby', 'modal-title');
    backdrop.setAttribute('aria-describedby', 'modal-desc');

    var card = document.createElement('div');
    card.className = 'bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 sm:p-8 transform scale-95 opacity-0 transition-all duration-200';
    card.innerHTML = '' +
      '<div class="flex items-start gap-4 mb-4">' +
        '<div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 ' + iconBg + '"><i class="fa-solid ' + icon + ' text-xl"></i></div>' +
        '<div class="flex-1">' +
          '<h2 id="modal-title" class="text-lg font-extrabold text-stone-900">' + (opts.title || '') + '</h2>' +
          '<p id="modal-desc" class="text-sm text-stone-500 mt-1">' + (opts.message || '') + '</p>' +
        '</div>' +
        '<button type="button" class="modal-close-btn text-stone-400 hover:text-stone-700" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>' +
      '</div>' +
      (opts.content || '') +
      '<div class="flex flex-col sm:flex-row gap-2 mt-6">' +
        (opts.cancelText !== null ? '<button type="button" class="modal-cancel-btn flex-1 px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 font-bold text-sm hover:bg-stone-50 min-h-[44px]">' + (opts.cancelText || 'Cancel') + '</button>' : '') +
        (opts.confirmText !== null ? '<button type="button" class="modal-confirm-btn flex-1 px-4 py-2.5 rounded-xl text-white font-bold text-sm min-h-[44px] ' + btnClass + '">' + (opts.confirmText || 'OK') + '</button>' : '') +
      '</div>';

    backdrop.appendChild(card);
    root.appendChild(backdrop);

    return { backdrop: backdrop, card: card };
  }

  function show(opts) {
    opts = opts || {};
    if (current) {
      queue.push(opts);
      return;
    }
    lastFocus = document.activeElement;
    var m = build(opts);
    current = { opts: opts, m: m, closed: false };

    var backdrop = m.backdrop, card = m.card;
    lockScroll(true);

    // destructive default focus on cancel
    var cancelBtn = card.querySelector('.modal-cancel-btn');
    var confirmBtn = card.querySelector('.modal-confirm-btn');
    var closeBtn = card.querySelector('.modal-close-btn');

    function close(result) {
      if (current.closed) return;
      current.closed = true;
      backdrop.classList.add('opacity-0');
      card.classList.add('scale-95', 'opacity-0');
      setTimeout(function () {
        backdrop.remove();
        lockScroll(false);
        current = null;
        if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        if (typeof opts.onClose === 'function') opts.onClose(result);
        if (queue.length) show(queue.shift());
      }, 200);
    }

    // backdrop click
    backdrop.addEventListener('click', function (e) {
      if (e.target !== backdrop) return;
      if (opts.destructive || opts.noBackdropClose) return;
      close(false);
    });

    if (closeBtn) closeBtn.addEventListener('click', function () { close(false); });
    if (cancelBtn) cancelBtn.addEventListener('click', function () { close(false); });

    if (confirmBtn) {
      confirmBtn.addEventListener('click', function () {
        if (confirmBtn.disabled) return;
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>' + (opts.loadingText || 'Please wait...');
        if (typeof opts.onConfirm === 'function') {
          var r = opts.onConfirm();
          if (r && typeof r.then === 'function') {
            r.then(function () { close(true); }).catch(function () { confirmBtn.disabled = false; confirmBtn.textContent = opts.confirmText || 'OK'; });
          } else {
            close(true);
          }
        } else {
          close(true);
        }
      });
    }

    // focus
    setTimeout(function () {
      backdrop.classList.remove('opacity-0');
      card.classList.remove('scale-95', 'opacity-0');
      if (opts.destructive && cancelBtn) cancelBtn.focus();
      else if (confirmBtn) confirmBtn.focus();
      trapFocus(card);
    }, 10);

    // escape
    function onKey(e) {
      if (e.key === 'Escape' && !(opts.destructive === true && opts.noEscape)) {
        if (!opts.noEscape) close(false);
      }
    }
    document.addEventListener('keydown', onKey);
    current.onKey = onKey;

    // auto close
    if (opts.autoClose) {
      var ms = opts.autoClose;
      var bar = document.createElement('div');
      bar.className = 'h-1 bg-stone-100 rounded-full mt-3 overflow-hidden';
      bar.innerHTML = '<div class="h-full bg-emerald-500 transition-all" style="width:100%"></div>';
      card.appendChild(bar);
      var fill = bar.querySelector('div');
      var start = Date.now();
      var timer = setInterval(function () {
        var pct = 100 - ((Date.now() - start) / ms * 100);
        if (pct <= 0) { clearInterval(timer); close(true); }
        else fill.style.width = pct + '%';
      }, 50);
    }

    // cleanup key handler on close
    var origClose = close;
    // wrap not needed

    return current;
  }

  // public API
  window.showConfirm = function (o) { return show(Object.assign({ variant: 'confirm', confirmText: 'Confirm', cancelText: 'Cancel' }, o)); };
  window.showDanger = function (o) { return show(Object.assign({ variant: 'danger', confirmText: 'Delete', cancelText: 'Cancel', destructive: true }, o)); };
  window.showSuccess = function (o) { return show(Object.assign({ variant: 'success', confirmText: 'OK', cancelText: null, autoClose: o && o.autoClose ? o.autoClose : 0 }, o)); };
  window.showError = function (o) { return show(Object.assign({ variant: 'error', confirmText: 'Close', cancelText: null }, o)); };
  window.showInfo = function (o) { return show(Object.assign({ variant: 'warning', confirmText: 'OK', cancelText: null }, o)); };

  // data-attribute helper
  function handleConfirmable(el, e) {
    var title = el.getAttribute('data-confirm-title') || 'Are you sure?';
    var message = el.getAttribute('data-confirm-message') || '';
    var variant = el.getAttribute('data-confirm-variant') || 'confirm';
    var confirmText = el.getAttribute('data-confirm-confirm-text') || (variant === 'danger' ? 'Confirm' : 'OK');
    var cancelText = el.getAttribute('data-confirm-cancel-text') || 'Cancel';
    var destructive = variant === 'danger';

    if (e) e.preventDefault();

    show({
      variant: variant,
      title: title,
      message: message,
      confirmText: confirmText,
      cancelText: cancelText,
      destructive: destructive,
      onConfirm: function () {
        // submit form or click original
        if (el.tagName === 'FORM') {
          el.submit();
        } else if (el.tagName === 'A') {
          window.location.href = el.getAttribute('href');
        } else {
          // remove data attr to avoid loop
          el.removeAttribute('data-confirm-title');
          el.click();
        }
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    // forms
    document.querySelectorAll('form[data-confirm-title]').forEach(function (f) {
      f.addEventListener('submit', function (e) {
        if (f.dataset.confirmed === '1') return;
        e.preventDefault();
        handleConfirmable(f, e);
      });
    });
    // buttons/links
    document.querySelectorAll('[data-confirm-title]:not(form)').forEach(function (el) {
      el.addEventListener('click', function (e) {
        if (el.dataset.confirmed === '1') return;
        handleConfirmable(el, e);
      });
    });
  });

  // flash -> modal on load
  window.showFlashModals = function (flashes) {
    if (!flashes) return;
    if (flashes.success && flashes.success.length) {
      flashes.success.forEach(function (m) { showSuccess({ title: 'Success', message: m, autoClose: 3500 }); });
    }
    if (flashes.error && flashes.error.length) {
      flashes.error.forEach(function (m) { showError({ title: 'Error', message: m }); });
    }
    if (flashes.warning && flashes.warning.length) {
      flashes.warning.forEach(function (m) { showInfo({ title: 'Notice', message: m }); });
    }
  };
})();
