const form = document.getElementById('registerForm');

    form.addEventListener('submit', function(e) {
      let valid = true;

      const username = form.username.value.trim();
      const email = form.email.value.trim();
      const password = form.password.value.trim();

      // Validation nom
      if (username === '') {
        showError('username', true);
        valid = false;
      } else {
        showError('username', false);
      }

      // Validation email
      if (!validateEmail(email)) {
        showError('email', true);
        valid = false;
      } else {
        showError('email', false);
      }

      // Validation mot de passe
      if (password.length < 6) {
        showError('password', true);
        valid = false;
      } else {
        showError('password', false);
      }

      if (!valid) e.preventDefault();
    });

    function validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email.toLowerCase());
    }

    function showError(field, show) {
      const error = document.getElementById(`error-${field}`);
      error.style.display = show ? 'block' : 'none';
    }

    let slides = document.querySelectorAll('.slide');
let index = 0;

function showSlide(i) {
  slides.forEach((slide, idx) => {
    slide.classList.remove('active');
    if (idx === i) slide.classList.add('active');
  });
}

setInterval(() => {
  index = (index + 1) % slides.length;
  showSlide(index);
}, 3000);


document.getElementById('hamburger').onclick = function() {
        const navLinks = document.getElementById('nav-links');
        navLinks.classList.toggle('active');
    };
