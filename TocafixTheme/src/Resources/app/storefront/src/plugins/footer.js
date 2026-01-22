import Plugin from 'src/plugin-system/plugin.class';

export default class TocafixFooterPlugin extends Plugin {
    init() {
        // const el = document.querySelector(".header-main");
        // const observer = new IntersectionObserver( 
        //     ([e]) => e.target.classList.toggle("is-pinned", e.intersectionRatio < 1), { threshold: [1] }
        // );

        // observer.observe(el);

        const scrollTrigger = document.querySelector('.scroll-down');
        const section = document.querySelector('.cms-section.pos-1');

        if (scrollTrigger) {
            scrollTrigger.addEventListener('click', () => {
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth' });
                }
            });
        }
    }
}