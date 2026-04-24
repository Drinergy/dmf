/**
 * DMF Dental Training Center — Enrollment JS
 * enrollment.js
 *
 * - Landing: scroll-in reveals, staggered cards, and navbar scrollspy.
 * - Reveal class "is-inview" is set once when elements enter the viewport.
 */
document.addEventListener('DOMContentLoaded', () => {
    const isLanding = window.location.pathname === '/' || window.location.pathname === '';

    if (isLanding) {
        const revealEls = document.querySelectorAll('.land-reveal, .land-stagger');
        if (revealEls.length > 0) {
            const preferReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (preferReduced) {
                revealEls.forEach((el) => {
                    el.classList.add('is-inview');
                });
            } else {
                const revealObserver = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                entry.target.classList.add('is-inview');
                                revealObserver.unobserve(entry.target);
                            }
                        });
                    },
                    {
                        root: null,
                        rootMargin: '0px 0px -5% 0px',
                        threshold: 0.1,
                    },
                );
                revealEls.forEach((el) => revealObserver.observe(el));
            }
        }
    }

    if (!isLanding) {
        return;
    }

    const hero = document.querySelector('.hero-gradient');
    if (hero && !hero.id) {
        hero.id = 'hero';
    }

    const sections = document.querySelectorAll('section[id], header[id]');
    const navLinks = document.querySelectorAll('#desktop-nav .nav-link');

    if (sections.length === 0 || navLinks.length === 0) {
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const id = entry.target.getAttribute('id');

                navLinks.forEach((a) => {
                    a.classList.remove('text-brand-600', 'font-bold', 'border-b-2', 'border-brand-600', 'pb-0.5');
                    a.classList.add('text-gray-500');
                });

                const activeLink = document.querySelector(`#desktop-nav a[href$="#${id}"]`);
                if (activeLink) {
                    activeLink.classList.remove('text-gray-500');
                    activeLink.classList.add('text-brand-600', 'font-bold', 'border-b-2', 'border-brand-600', 'pb-0.5');
                }
            }
        });
    }, {
        threshold: 0,
        rootMargin: '-25% 0px -65% 0px',
    });

    sections.forEach((sec) => observer.observe(sec));
});
