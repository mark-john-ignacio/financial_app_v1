document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-target');
            fetchContent(target);
        });
    });
});

function fetchContent(target) {
    fetch(`content.php?page=${target}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('app').innerHTML = html;
            window.history.pushState({ path: target }, '', target);
        })
        .catch(error => console.error('Error loading the content:', error));
}