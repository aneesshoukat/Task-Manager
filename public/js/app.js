document.addEventListener('DOMContentLoaded', function () {
    const html = document.documentElement;
    const toggleBtn = document.getElementById('darkModeToggle');
    const theme = localStorage.getItem('theme') || 'light';

    html.setAttribute('data-bs-theme', theme);

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const current = html.getAttribute('data-bs-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-bs-theme', next);
            localStorage.setItem('theme', next);
        });
    }

    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });

    setTimeout(function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        });
    }, 5000);
});
