/**
 * DMF Dental Training Center — Enrollment JS
 * enrollment.js
 *
 * Layout-level behaviour: scrollspy for the landing page navbar.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Only run scrollspy on the home / landing page
    if (window.location.pathname !== '/' && window.location.pathname !== '') return;

    // Auto-tag hero section if it has no id
    const hero = document.querySelector('.hero-gradient');
    if (hero && !hero.id) hero.id = 'hero';

    const sections = document.querySelectorAll('section[id], header[id]');
    const navLinks = document.querySelectorAll('#desktop-nav .nav-link');

    if (sections.length === 0 || navLinks.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');

                // Clear active states
                navLinks.forEach(a => {
                    a.classList.remove('text-brand-600', 'font-bold', 'border-b-2', 'border-brand-600', 'pb-0.5');
                    a.classList.add('text-gray-500');
                });

                // Activate the matching nav link
                const activeLink = document.querySelector(`#desktop-nav a[href$="#${id}"]`);
                if (activeLink) {
                    activeLink.classList.remove('text-gray-500');
                    activeLink.classList.add('text-brand-600', 'font-bold', 'border-b-2', 'border-brand-600', 'pb-0.5');
                }
            }
        });
    }, {
        threshold: 0,
        rootMargin: '-25% 0px -65% 0px', // triggers when section enters the top 25-35% of screen
    });

    sections.forEach(sec => observer.observe(sec));
});
