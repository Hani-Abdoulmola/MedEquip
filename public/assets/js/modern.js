/**
 * MediTrust - Modern Medical Equipment B2B Platform JavaScript
 * Senior Frontend Developer Implementation
 */

class MediTrustApp {
  constructor() {
    this.init();
  }

  init() {
    this.setupNavbar();
    this.setupSmoothScrolling();
    this.setupScrollAnimations();
    this.setupMobileMenu();
    this.setupFAQ();
    this.setupContactForm();
    this.setupLightbox();
  }

  // Navbar scroll effects
  setupNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    let lastScrollY = window.scrollY;
    let ticking = false;

    const updateNavbar = () => {
      const scrollY = window.scrollY;
      
      if (scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
      } else {
        navbar.classList.remove('navbar-scrolled');
      }

      // Update active nav link based on scroll position
      this.updateActiveNavLink();
      
      lastScrollY = scrollY;
      ticking = false;
    };

    const requestTick = () => {
      if (!ticking) {
        requestAnimationFrame(updateNavbar);
        ticking = true;
      }
    };

    window.addEventListener('scroll', requestTick, { passive: true });
  }

  // Update active navigation link based on scroll position
  updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    let currentSection = '';
    
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 100;
      const sectionHeight = section.offsetHeight;
      
      if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
        currentSection = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `#${currentSection}`) {
        link.classList.add('active');
      }
    });
  }

  // Smooth scrolling for anchor links
  setupSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', (e) => {
        e.preventDefault();
        const target = document.querySelector(anchor.getAttribute('href'));
        
        if (target) {
          const offsetTop = target.offsetTop - 80; // Account for fixed navbar
          
          window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
          });
        }
      });
    });
  }

  // Scroll animations using Intersection Observer
  setupScrollAnimations() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-in');
        }
      });
    }, observerOptions);

    // Observe all elements with animation classes
    document.querySelectorAll('.fade-up, .fade-in, .slide-in-left, .slide-in-right').forEach(el => {
      observer.observe(el);
    });
  }

  // Mobile menu functionality
  setupMobileMenu() {
    const toggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.querySelector('.navbar-nav');
    
    if (!toggle || !nav) return;

    toggle.addEventListener('click', () => {
      toggle.classList.toggle('active');
      nav.classList.toggle('active');
    });

    // Close mobile menu when clicking on a link
    nav.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        toggle.classList.remove('active');
        nav.classList.remove('active');
      });
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!toggle.contains(e.target) && !nav.contains(e.target)) {
        toggle.classList.remove('active');
        nav.classList.remove('active');
      }
    });
  }

  // FAQ accordion functionality
  setupFAQ() {
    document.querySelectorAll('.faq-item').forEach(item => {
      const question = item.querySelector('.faq-question');
      const answer = item.querySelector('.faq-answer');
      
      if (!question || !answer) return;

      question.addEventListener('click', () => {
        const isOpen = item.classList.contains('active');
        
        // Close all other FAQ items
        document.querySelectorAll('.faq-item').forEach(otherItem => {
          if (otherItem !== item) {
            otherItem.classList.remove('active');
          }
        });
        
        // Toggle current item
        item.classList.toggle('active', !isOpen);
      });
    });
  }

  // Contact form handling
  setupContactForm() {
    const form = document.querySelector('.contact-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const formData = new FormData(form);
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      
      // Show loading state
      submitBtn.textContent = 'جاري الإرسال...';
      submitBtn.disabled = true;
      
      try {
        // Here you would typically send the form data to your Laravel backend
        // For now, we'll simulate a successful submission
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Show success message
        this.showNotification('تم إرسال رسالتك بنجاح!', 'success');
        form.reset();
        
      } catch (error) {
        // Show error message
        this.showNotification('حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.', 'error');
      } finally {
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }
    });
  }

  // Simple lightbox for gallery
  setupLightbox() {
    document.querySelectorAll('.gallery-item img').forEach(img => {
      img.addEventListener('click', () => {
        this.openLightbox(img.src, img.alt);
      });
    });
  }

  openLightbox(src, alt) {
    // Create lightbox overlay
    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.innerHTML = `
      <div class="lightbox-content">
        <img src="${src}" alt="${alt}">
        <button class="lightbox-close">&times;</button>
      </div>
    `;
    
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    
    // Close lightbox
    const closeLightbox = () => {
      document.body.removeChild(overlay);
      document.body.style.overflow = '';
    };
    
    overlay.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeLightbox();
    });
    
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeLightbox();
    });
  }

  // Utility function to show notifications
  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => document.body.removeChild(notification), 300);
    }, 5000);
  }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  new MediTrustApp();
});

// Add CSS for animations and components that need JavaScript
const additionalCSS = `
  .fade-up { opacity: 0; transform: translateY(30px); transition: all 0.6s ease; }
  .fade-in { opacity: 0; transition: opacity 0.6s ease; }
  .slide-in-left { opacity: 0; transform: translateX(-30px); transition: all 0.6s ease; }
  .slide-in-right { opacity: 0; transform: translateX(30px); transition: all 0.6s ease; }
  
  .animate-in { opacity: 1 !important; transform: translate(0) !important; }
  
  .faq-item { border-bottom: 1px solid var(--gray-200); }
  .faq-question { cursor: pointer; padding: 1rem 0; font-weight: 600; }
  .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
  .faq-item.active .faq-answer { max-height: 200px; padding-bottom: 1rem; }
  
  .lightbox-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 9999; display: flex; align-items: center; justify-content: center; }
  .lightbox-content { position: relative; max-width: 90vw; max-height: 90vh; }
  .lightbox-content img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .lightbox-close { position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 2rem; cursor: pointer; }
  
  .notification { position: fixed; top: 20px; right: 20px; padding: 1rem 1.5rem; border-radius: 0.5rem; color: white; z-index: 9999; transform: translateX(100%); transition: transform 0.3s ease; }
  .notification.show { transform: translateX(0); }
  .notification-success { background-color: var(--secondary-color); }
  .notification-error { background-color: var(--accent-color); }
  .notification-info { background-color: var(--primary-color); }
`;

// Inject additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);
