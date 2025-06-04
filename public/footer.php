<footer>
    © 2025 VideGrenier+. Tous droits réservés.
  </footer>

  <script>
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('nav-links');

    hamburger.onclick = () => {
      navLinks.classList.toggle('active');
    };

    document.querySelectorAll('#nav-links a').forEach(link => {
      link.onclick = () => {
        navLinks.classList.remove('active');
      };
    });
  </script>
</body>
</html>
