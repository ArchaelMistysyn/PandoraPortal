fetch('../header.html')
    .then(response => response.text())
    .then(data => {
        document.getElementById('header').innerHTML = data;
        const currentPage = window.location.pathname.split('/').pop();
        const buttons = document.querySelectorAll('.header-middle a');
        buttons.forEach(button => {
            if (button.getAttribute('href') === currentPage) {
                button.classList.add('header-current');
            }
        });
    })
    .catch(error => console.error("Error loading header:", error));
