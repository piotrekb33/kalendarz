// Animacja rozbłysku flagi tylko po zmianie języka
document.addEventListener('DOMContentLoaded', function() {
    const lang = new URLSearchParams(window.location.search).get('lang');
    const prevLang = localStorage.getItem('lastLang');
    if (lang && prevLang && lang !== prevLang) {
        setTimeout(() => {
            const active = document.querySelector('.lang-switch a.active');
            if (active) {
                active.classList.add('flash');
                setTimeout(() => active.classList.remove('flash'), 600);
            }
        }, 50);
    }
    if (lang) localStorage.setItem('lastLang', lang);
});