// ===== Mobile Menu =====
(function() {
  const menuBtn = document.getElementById('mobileMenuBtn');
  const header = document.getElementById('header');
  const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

  if (menuBtn) {
    menuBtn.addEventListener('click', function() {
      header.classList.toggle('mobile-menu-open');
      const icon = this.querySelector('svg use');
      if (icon) {
        const isOpen = header.classList.contains('mobile-menu-open');
        icon.setAttribute('href', isOpen ? '#icon-x' : '#icon-menu');
      }
    });
  }

  mobileNavLinks.forEach(function(link) {
    link.addEventListener('click', function() {
      header.classList.remove('mobile-menu-open');
      const icon = menuBtn.querySelector('svg use');
      if (icon) icon.setAttribute('href', '#icon-menu');
    });
  });
})();

// ===== Smooth Scroll =====
(function() {
  document.addEventListener('click', function(e) {
    var target = e.target.closest('a[href^="#"]');
    if (!target) return;
    var href = target.getAttribute('href');
    if (href === '#') return;
    var el = document.querySelector(href);
    if (el) {
      e.preventDefault();
      el.scrollIntoView({ behavior: 'smooth' });
    }
  });
})();

// ===== Toast System =====
(function() {
  var container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  window.showToast = function(title, description, variant) {
    var toast = document.createElement('div');
    toast.className = 'toast' + (variant === 'destructive' ? ' destructive' : '');
    
    var titleEl = document.createElement('div');
    titleEl.className = 'toast-title';
    titleEl.textContent = title;
    toast.appendChild(titleEl);

    if (description) {
      var descEl = document.createElement('div');
      descEl.className = 'toast-desc';
      descEl.textContent = description;
      toast.appendChild(descEl);
    }

    container.appendChild(toast);

    setTimeout(function() {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s';
      setTimeout(function() { toast.remove(); }, 300);
    }, 4000);
  };
})();

// ===== Early Access Form =====
(function() {
  var form = document.getElementById('earlyAccessForm');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    var agree = form.querySelector('[name="agreeToTerms"]');
    if (agree && !agree.checked) {
      window.showToast('يرجى الموافقة على الشروط', 'يجب الموافقة على شروط الاستخدام للمتابعة', 'destructive');
      return;
    }

    var formData = {};
    var fields = form.querySelectorAll('[name]');
    fields.forEach(function(field) {
      if (field.type === 'checkbox') {
        formData[field.name] = field.checked;
      } else {
        formData[field.name] = field.value;
      }
    });
    formData.timestamp = new Date().toISOString();

    var submissions = JSON.parse(localStorage.getItem('earlyAccessSubmissions') || '[]');
    submissions.push(formData);
    localStorage.setItem('earlyAccessSubmissions', JSON.stringify(submissions));

    window.showToast('تم التسجيل بنجاح', 'شكراً لانضمامك إلى المجموعة التجريبية. سنتواصل معك قريباً');
    form.reset();
  });
})();

// ===== Intersection Observer: Scroll Animations =====
(function() {
  if (!window.IntersectionObserver) return;

  var animElements = document.querySelectorAll('[data-anim]');
  if (animElements.length === 0) return;

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        var el = entry.target;
        var anim = el.getAttribute('data-anim') || 'fade-up';
        var delay = parseInt(el.getAttribute('data-delay')) || 0;
        
        setTimeout(function() {
          el.classList.add('anim-' + anim);
        }, delay);
        
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.1 });

  animElements.forEach(function(el) {
    el.classList.add('anim-hidden');
    observer.observe(el);
  });
})();

// ===== Progress Bar Animations =====
(function() {
  if (!window.IntersectionObserver) return;

  var progressBars = document.querySelectorAll('[data-progress]');
  if (progressBars.length === 0) return;

  var progressObserver = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        var bar = entry.target;
        bar.classList.add('animated');
        progressObserver.unobserve(bar);
      }
    });
  }, { threshold: 0.1 });

  progressBars.forEach(function(bar) {
    progressObserver.observe(bar);
  });
})();

// ===== Floating Particles =====
(function() {
  var container = document.getElementById('particles');
  if (!container) return;
  for (var i = 0; i < 15; i++) {
    var p = document.createElement('div');
    p.className = 'particle';
    var size = Math.random() * 6 + 2;
    var x = Math.random() * 100;
    var y = Math.random() * 100;
    var dur = Math.random() * 20 + 10;
    var del = Math.random() * 5;
    p.style.cssText = 'width:' + size + 'px;height:' + size + 'px;left:' + x + '%;top:' + y + '%;animation:float2 ' + dur + 's ' + del + 's linear infinite;opacity:' + (Math.random() * 0.4 + 0.1);
    container.appendChild(p);
  }
})();

// ===== Footer Year =====
(function() {
  var yearEl = document.getElementById('footerYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();

// ===== Hero Mockup Bars Animation Trigger =====
(function() {
  var mockup = document.querySelector('.hero-mockup-wrapper');
  if (!mockup) return;

  var mockupObserver = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        var bars = entry.target.querySelectorAll('.mockup-progress-bar');
        bars.forEach(function(bar) { bar.classList.add('animated'); });
        mockupObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.3 });

  mockupObserver.observe(mockup);
})();

// ===== Auto-close mobile menu on scroll =====
(function() {
  var header = document.getElementById('header');
  var lastScroll = 0;
  window.addEventListener('scroll', function() {
    if (header.classList.contains('mobile-menu-open')) {
      var currentScroll = window.pageYOffset;
      if (Math.abs(currentScroll - lastScroll) > 50) {
        header.classList.remove('mobile-menu-open');
        var icon = document.querySelector('#mobileMenuBtn svg use');
        if (icon) icon.setAttribute('href', '#icon-menu');
      }
      lastScroll = currentScroll;
    }
  });
})();
