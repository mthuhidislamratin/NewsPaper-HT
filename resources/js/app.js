const savedTheme = localStorage.getItem('jago24barta-theme');
if (savedTheme === 'dark' || savedTheme === 'light') {
    document.body.dataset.theme = savedTheme;
}

document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
    toggle.setAttribute('aria-pressed', String(document.body.dataset.theme === 'dark'));
    toggle.addEventListener('click', () => {
        const theme = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
        document.body.dataset.theme = theme;
        localStorage.setItem('jago24barta-theme', theme);
        toggle.setAttribute('aria-pressed', String(theme === 'dark'));
    });
});

document.querySelectorAll('[data-menu-toggle]').forEach((toggle) => {
    const menu = document.getElementById(toggle.getAttribute('aria-controls'));
    if (!menu) return;

    toggle.addEventListener('click', () => {
        const isOpen = menu.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(isOpen));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.classList.contains('is-open')) {
            menu.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            toggle.focus();
        }
    });
});
