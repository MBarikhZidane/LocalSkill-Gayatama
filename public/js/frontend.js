document.addEventListener('DOMContentLoaded', () => {
    window.lucide?.createIcons();
    const applyTheme = (dark) => {
        document.documentElement.classList.toggle('dark', dark);
        document.documentElement.dataset.theme = dark ? 'dark' : 'light';
    };
    try { applyTheme(localStorage.getItem('theme') === 'dark'); } catch {}
    document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
        const dark = !document.documentElement.classList.contains('dark');
        applyTheme(dark);
        try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch {}
    });
    document.querySelectorAll('[data-skill-level]').forEach(input => {
        input.addEventListener('input', () => {
            document.getElementById(input.dataset.skillLevel).value = input.value;
        });
    });
});
