// ===================================
// Language Switcher
// ===================================
const langButtons = document.querySelectorAll('.lang-btn');
let currentLang = localStorage.getItem('language') || 'en';

// Set initial language on page load
document.addEventListener('DOMContentLoaded', function() {
    setLanguage(currentLang);
    updateActiveLangButton();
});

// Language button click handlers
langButtons.forEach(button => {
    button.addEventListener('click', function() {
        const lang = this.getAttribute('data-lang');
        setLanguage(lang);
        localStorage.setItem('language', lang);
        updateActiveLangButton();
    });
});

function setLanguage(lang) {
    currentLang = lang;
    const elements = document.querySelectorAll('[data-tr][data-en]');
    
    elements.forEach(element => {
        const translation = element.getAttribute(`data-${lang}`);
        if (translation) {
            element.textContent = translation;
        }
    });
    
    // Update HTML lang attribute
    document.documentElement.lang = lang === 'tr' ? 'tr' : 'en';
}

function updateActiveLangButton() {
    langButtons.forEach(btn => {
        if (btn.getAttribute('data-lang') === currentLang) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// ===================================
// Mobile Navigation Toggle
// ===================================
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const navMenu = document.querySelector('.nav-menu');

if (mobileMenuToggle && navMenu) {
    mobileMenuToggle.addEventListener('click', function() {
        this.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const isClickInsideNav = navMenu.contains(event.target);
        const isClickOnToggle = mobileMenuToggle.contains(event.target);
        
        if (!isClickInsideNav && !isClickOnToggle && navMenu.classList.contains('active')) {
            mobileMenuToggle.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });

    // Close menu when clicking on a link
    const navLinks = navMenu.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (!link.classList.contains('lang-btn')) {
                mobileMenuToggle.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });
    });
}

// ===================================
// Navbar Scroll Effect
// ===================================
let lastScroll = 0;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll > 100) {
        navbar.style.boxShadow = '0 4px 16px rgba(0, 0, 0, 0.12)';
    } else {
        navbar.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.08)';
    }
    
    lastScroll = currentScroll;
});

// ===================================
// Product Category Tabs
// ===================================
const tabButtons = document.querySelectorAll('.tab-btn:not(.lang-btn)');
const categoryContents = document.querySelectorAll('.category-content');

if (tabButtons.length > 0 && categoryContents.length > 0) {
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetCategory = this.getAttribute('data-category');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            categoryContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            const targetContent = document.getElementById(targetCategory);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

// ===================================
// Contact Form Handling - FormSubmit
// ===================================
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = currentLang === 'tr' ? 'Gönderiliyor...' : 'Sending...';
        
        // FormSubmit will handle the actual submission via the form action
        // The page will redirect or reload after submission
    });
}

// ===================================
// Smooth Scroll for Anchor Links
// ===================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Don't prevent default for empty hash or javascript: links
        if (href === '#' || href.startsWith('javascript:')) {
            return;
        }
        
        e.preventDefault();
        
        const target = document.querySelector(href);
        if (target) {
            const navHeight = navbar ? navbar.offsetHeight : 70;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navHeight - 20;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ===================================
// Intersection Observer for Fade-in Animations
// ===================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const fadeElements = document.querySelectorAll('.service-card, .product-card, .team-member, .field-card');

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(30px)';
                entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    fadeElements.forEach(element => {
        observer.observe(element);
    });
}

// ===================================
// Statistics Counter Animation
// ===================================
const statNumbers = document.querySelectorAll('.stat-number');

if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const finalValue = target.textContent;
                const numericValue = parseInt(finalValue.replace(/\D/g, ''));
                const suffix = finalValue.replace(/[0-9]/g, '');
                
                if (!isNaN(numericValue)) {
                    animateCounter(target, 0, numericValue, suffix, 2000);
                }
                
                statsObserver.unobserve(target);
            }
        });
    }, { threshold: 0.5 });
    
    statNumbers.forEach(stat => {
        statsObserver.observe(stat);
    });
}

function animateCounter(element, start, end, suffix, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            element.textContent = end + suffix;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + suffix;
        }
    }, 16);
}

// ===================================
// Form Validation
// ===================================
const formInputs = document.querySelectorAll('.contact-form input, .contact-form select, .contact-form textarea');

formInputs.forEach(input => {
    input.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
            this.style.borderColor = '#dc3545';
        } else {
            this.style.borderColor = '';
        }
    });
    
    input.addEventListener('input', function() {
        if (this.style.borderColor === 'rgb(220, 53, 69)') {
            this.style.borderColor = '';
        }
    });
});

// ===================================
// Active Page Indicator in Navigation
// ===================================
const currentPage = window.location.pathname.split('/').pop() || 'index.html';
const navLinks = document.querySelectorAll('.nav-menu a:not(.lang-btn)');

navLinks.forEach(link => {
    const linkPage = link.getAttribute('href');
    if (linkPage === currentPage) {
        link.classList.add('active');
    }
});