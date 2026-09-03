const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const reveal = {
    mounted(el, binding) {
        if (prefersReducedMotion()) {
            return;
        }

        el.classList.add('reveal-init');

        const delay = typeof binding.value === 'number' ? binding.value : 0;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        window.setTimeout(() => el.classList.add('reveal-visible'), delay);
                        observer.unobserve(el);
                    }
                });
            },
            { threshold: 0.15, rootMargin: '0px 0px -40px 0px' },
        );

        observer.observe(el);
        el.__revealObserver = observer;
    },
    unmounted(el) {
        el.__revealObserver?.disconnect();
    },
};
