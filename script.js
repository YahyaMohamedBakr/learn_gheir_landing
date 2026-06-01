// ===== Preloader =====
(function() {
  var preloader = document.querySelector('.preloader');
  if (preloader) {
    window.addEventListener('load', function() {
      setTimeout(function() { preloader.classList.add('hidden'); }, 1500);
    });
    setTimeout(function() { preloader.classList.add('hidden'); }, 4000);
  }
})();

// ===== Header Scroll Effect =====
(function() {
  var header = document.querySelector('.header');
  if (!header) return;

  function handleScroll() {
    if (window.scrollY > 50) {
      header.classList.add('header-scrolled');
      header.classList.remove('header-transparent');
    } else {
      header.classList.remove('header-scrolled');
      header.classList.add('header-transparent');
    }
  }

  window.addEventListener('scroll', handleScroll);
  handleScroll();
})();

// ===== Mobile Menu =====
(function() {
  var hamburger = document.querySelector('.mobile-menu-btn');
  var header = document.getElementById('header');
  var mobileNavLinks = document.querySelectorAll('.mobile-nav-link');

  if (hamburger && header) {
    hamburger.addEventListener('click', function() {
      header.classList.toggle('mobile-menu-open');
      hamburger.classList.toggle('active');
      document.body.style.overflow = header.classList.contains('mobile-menu-open') ? 'hidden' : '';
    });

    mobileNavLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        header.classList.remove('mobile-menu-open');
        hamburger.classList.remove('active');
        document.body.style.overflow = '';
      });
    });

    document.addEventListener('click', function(e) {
      if (header.classList.contains('mobile-menu-open') &&
          !header.querySelector('.nav-items').contains(e.target) &&
          !hamburger.contains(e.target)) {
        header.classList.remove('mobile-menu-open');
        hamburger.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  }
})();

// ===== Active Navigation Link =====
(function() {
  var sections = document.querySelectorAll('section[id]');
  var navLinks = document.querySelectorAll('.nav-link');

  function updateActive() {
    var scrollY = window.scrollY + 150;
    sections.forEach(function(section) {
      var top = section.offsetTop;
      var height = section.offsetHeight;
      var id = section.getAttribute('id');
      if (scrollY >= top && scrollY < top + height) {
        navLinks.forEach(function(link) {
          link.classList.remove('active');
          if (link.getAttribute('href') === '#' + id) {
            link.classList.add('active');
          }
        });
      }
    });
  }

  window.addEventListener('scroll', updateActive);
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
      var header = document.querySelector('.header');
      var offset = header ? header.offsetHeight : 0;
      window.scrollTo({ top: el.offsetTop - offset, behavior: 'smooth' });
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

// ===== Scroll Animations (Intersection Observer) =====
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
          // Animate any progress bars inside this element
          var bars = el.querySelectorAll('[data-progress]');
          bars.forEach(function(bar) {
            var targetWidth = bar.getAttribute('data-target-width');
            if (targetWidth) {
              bar.style.transition = 'width 1s ease-out';
              void bar.offsetWidth;
              bar.style.width = targetWidth;
            }
          });
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

// ===== Template Scroll Animations (animate-on-scroll classes) =====
(function() {
  if (!window.IntersectionObserver) return;

  var animEls = document.querySelectorAll('.animate-on-scroll, .animate-from-left, .animate-from-right, .animate-scale');
  if (animEls.length === 0) return;

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('animated');
        observer.unobserve(entry.target);
      }
    });
  }, { rootMargin: '0px 0px -60px 0px', threshold: 0.15 });

  animEls.forEach(function(el) { observer.observe(el); });
})();

// ===== Progress Bar Animations (standalone bars without data-anim parent) =====
(function() {
  if (!window.IntersectionObserver) return;

  var bars = document.querySelectorAll('[data-progress]');
  if (bars.length === 0) return;

  // Immediately collapse all bars to prevent flash
  bars.forEach(function(bar) {
    var targetWidth = bar.style.width;
    if (!targetWidth) {
      targetWidth = window.getComputedStyle(bar).width;
    }
    bar.setAttribute('data-target-width', targetWidth);
    bar.style.width = '0';
  });

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        var bar = entry.target;
        if (bar.closest('[data-anim]')) return;
        var targetWidth = bar.getAttribute('data-target-width');
        if (targetWidth) {
          bar.style.transition = 'width 1s ease-out';
          void bar.offsetWidth;
          bar.style.width = targetWidth;
        }
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  bars.forEach(function(bar) {
    if (bar.closest('[data-anim]')) return;
    observer.observe(bar);
  });
})();

// ===== Number Counter Animation (data-counter) =====
(function() {
  if (!window.IntersectionObserver) return;

  var counters = document.querySelectorAll('[data-counter]');
  if (counters.length === 0) return;

  function animate(el) {
    var target = parseInt(el.getAttribute('data-counter'));
    var suffix = el.getAttribute('data-suffix') || '';
    var prefix = el.getAttribute('data-prefix') || '';
    var duration = 2000;
    var stepTime = 30;
    var totalSteps = Math.floor(duration / stepTime);
    var increment = target / totalSteps;
    var current = 0;

    var timer = setInterval(function() {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = prefix + Math.floor(current) + suffix;
    }, stepTime);
  }

  var observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        animate(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach(function(el) { observer.observe(el); });
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

// ===== Scroll to Top Button =====
(function() {
  var btn = document.querySelector('.scroll-to-top');
  if (!btn) return;

  function toggle() {
    if (window.scrollY > 500) {
      btn.classList.add('visible');
    } else {
      btn.classList.remove('visible');
    }
  }

  window.addEventListener('scroll', toggle);

  btn.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();

// ===== Ripple Effect on Buttons =====
(function() {
  document.querySelectorAll('.btn-primary, .btn-outline, .btn-white, .nav-cta').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      var ripple = document.createElement('span');
      var rect = this.getBoundingClientRect();
      var size = Math.max(rect.width, rect.height);
      var x = e.clientX - rect.left - size / 2;
      var y = e.clientY - rect.top - size / 2;

      ripple.style.cssText =
        'position: absolute; width: ' + size + 'px; height: ' + size + 'px;' +
        'left: ' + x + 'px; top: ' + y + 'px;' +
        'background: rgba(255, 255, 255, 0.3);' +
        'border-radius: 50%;' +
        'transform: scale(0);' +
        'animation: ripple-effect 0.6s ease-out;' +
        'pointer-events: none;';

      this.appendChild(ripple);
      setTimeout(function() { ripple.remove(); }, 600);
    });
  });
})();

// ===== Simple Parallax for hero-shape elements =====
(function() {
  var parallaxEls = document.querySelectorAll('.hero-shape');
  if (parallaxEls.length === 0) return;

  function handleParallax() {
    var scrollY = window.scrollY;
    parallaxEls.forEach(function(el, index) {
      var speed = (index + 1) * 0.05;
      el.style.transform = 'translateY(' + (scrollY * speed) + 'px)';
    });
  }

  window.addEventListener('scroll', handleParallax, { passive: true });
})();

// ===== Footer Year =====
(function() {
  var yearEl = document.getElementById('footerYear');
  if (yearEl) yearEl.textContent = new Date().getFullYear();
})();

// ===== Auto-close mobile menu on scroll =====
(function() {
  var header = document.getElementById('header');
  if (!header) return;
  var lastScroll = 0;
  window.addEventListener('scroll', function() {
    if (header.classList.contains('mobile-menu-open')) {
      var currentScroll = window.pageYOffset;
      if (Math.abs(currentScroll - lastScroll) > 50) {
        header.classList.remove('mobile-menu-open');
        var hamburger = document.querySelector('.mobile-menu-btn');
        if (hamburger) hamburger.classList.remove('active');
        document.body.style.overflow = '';
      }
      lastScroll = currentScroll;
    }
  });
})();
