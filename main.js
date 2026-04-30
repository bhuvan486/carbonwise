/**
 * Carbonwise - Main JavaScript
 * Handles form validation, real-time preview, and interactivity
 */

document.addEventListener('DOMContentLoaded', () => {
  
  // Emission factors (matching PHP constants)
  const factors = { 
    electricity: 0.82,
    travel: 0.21,
    lpg: 2.98
  };
  
  const inputs = document.querySelectorAll('input[type="number"]');
  
  function updatePreview() {
    let total = 0;
    inputs.forEach(i => {
      const value = parseFloat(i.value) || 0;
      total += value * (factors[i.name] || 0);
    });
    const previewElement = document.getElementById('live-preview');
    if (previewElement) {
      previewElement.textContent = total > 0 
        ? `🌍 Estimated: ~${total.toFixed(1)} kg CO₂ per month` 
        : '';
    }
  }
  
  inputs.forEach(input => {
    input.addEventListener('input', () => {
      if (parseFloat(input.value) < 0) {
        input.value = 0;
      }
      clearError(input);
      updatePreview();
    });
  });
  
  const form = document.querySelector('.calc-form');
  if (form) {
    form.addEventListener('submit', e => {
      let valid = true;
      
      inputs.forEach(input => {
        const v = input.value.trim();
        const num = parseFloat(v);
        
        if (v === '' || isNaN(num)) {
          showError(input, 'Please enter a valid number.');
          valid = false;
        } else if (num < 0) {
          showError(input, 'Value cannot be negative.');
          valid = false;
        } else if (num > 100000) {
          showError(input, 'Value seems too high. Please check your input.');
          valid = false;
        }
      });
      
      if (!valid) {
        e.preventDefault();
        return;
      }
      
      const btn = form.querySelector('.submit-btn');
      btn.innerHTML = '🔄 Calculating...';
      btn.disabled = true;
    });
  }
  
  function showError(input, msg) {
    clearError(input);
    input.style.borderColor = '#b83232';
    const err = document.createElement('span');
    err.className = 'input-error';
    err.textContent = msg;
    input.closest('.input-row').after(err);
  }
  
  function clearError(input) {
    input.style.borderColor = '';
    const next = input.closest('.input-row')?.nextElementSibling;
    if (next?.classList.contains('input-error')) {
      next.remove();
    }
  }
  
  function autoFormatNumber(input) {
    input.addEventListener('blur', function() {
      let value = parseFloat(this.value);
      if (!isNaN(value) && value !== 0) {
        this.value = value.toFixed(1);
      }
    });
  }
  
  inputs.forEach(input => autoFormatNumber(input));
  
  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '') return;
      
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
  
  updatePreview();
  
  console.log('🌱 Carbonwise loaded — Calculate your carbon footprint!');
});