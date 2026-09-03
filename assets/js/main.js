(() => {
  // Password Visibility Toggle
  document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    const input = document.getElementById(toggle.getAttribute('data-password-toggle'));
    const targetId = toggle.getAttribute('data-password-toggle');
    const input = document.getElementById(targetId);
    if (!input) return;

    toggle.addEventListener('click', (event) => {
      const visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      toggle.setAttribute('aria-pressed', String(!visible));
      toggle.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
      event.stopImmediatePropagation();
    }, true);
      event.preventDefault();
      event.stopPropagation();
      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';
      toggle.setAttribute('aria-pressed', String(isPassword));
      toggle.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
      
      const icon = toggle.querySelector('svg');
      if (icon) {
        if (isPassword) {
          icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
        } else {
          icon.innerHTML = '<path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path><circle cx="12" cy="12" r="2.75"></circle>';
        }
      }
    });
  });

  document.querySelectorAll('form').forEach((form) => form.addEventListener('submit', () => {
    const button = form.querySelector('button[type=submit]');
    if (button) {
      button.disabled = true;
      button.dataset.label = button.textContent;
      button.textContent = 'Processing...';
    }
  }));
  // Submit button "Processing..." feedback & double-click protection
  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', (event) => {
      // Don't disable if form submission was prevented by client validation
      if (form.checkValidity && !form.checkValidity()) {
        return;
      }
      const button = form.querySelector('button[type="submit"]') || form.querySelector('button:not([type="button"])');
      if (button && !button.disabled) {
        // Use a short timeout so that the form submit event completes cleanly
        setTimeout(() => {
          button.disabled = true;
          button.dataset.originalText = button.textContent;
          button.textContent = 'Processing...';
        }, 10);
      }
    });
  });
})();
