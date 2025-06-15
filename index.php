<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>LGU Quick Appoint</title>
<!-- FontAwesome CDN for icons -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
/>
<style>
  /* Reset some default styles */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body,
  html {
    height: 100%;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #f0f0f0;
  }

  /* Background image styling */
  body {
    background: url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1470&q=80')
      no-repeat center center fixed;
    background-size: cover;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  /* Overlay for better readability */
  body::before {
    content: "";
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(22, 33, 51, 0.7);
    z-index: -1;
  }

  /* Header */
  header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 3rem;
    background: rgba(0, 0, 0, 0.4);
  }

  header .logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: #5de1e6;
    letter-spacing: 2px;
    user-select: none;
  }

  nav ul {
    list-style: none;
    display: flex;
    gap: 2.5rem;
  }

  nav ul li a {
    color: #c0e9ff;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: color 0.3s ease;
    cursor: pointer;
    padding-bottom: 3px;
  }

  nav ul li a:hover,
  nav ul li a:focus {
    color: #5de1e6;
    border-bottom: 2px solid #5de1e6;
  }

  /* Main content */
  main {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 2rem 1rem;
  }

  main h1 {
    font-size: 3rem;
    max-width: 700px;
    line-height: 1.3;
    text-shadow: 0 0 10px rgba(93, 225, 230, 0.8);
    color: #aaf3fc;
  }

  /* Footer */
  footer {
    background: rgba(0, 0, 0, 0.65);
    padding: 2rem 3rem;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    font-size: 0.9rem;
    color: #c3dbe8;
  }

  footer .about,
  footer .contacts {
    max-width: 45%;
    margin-bottom: 1rem;
  }

  footer .about h3,
  footer .contacts h3 {
    margin-bottom: 0.75rem;
    color: #5de1e6;
  }

  footer .contacts ul {
    list-style: none;
    padding-left: 0;
  }

  footer .contacts ul li {
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
    cursor: default;
  }

  footer .contacts ul li i {
    color: #5de1e6;
    font-size: 1.3rem;
    width: 25px;
    text-align: center;
  }

  footer .contacts ul li a {
    color: #c3dbe8;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  footer .contacts ul li a:hover,
  footer .contacts ul li a:focus {
    color: #5de1e6;
  }

  /* Responsive adjustments */
  @media (max-width: 720px) {
    header {
      flex-direction: column;
      gap: 1rem;
      padding: 1rem 2rem;
    }

    nav ul {
      gap: 1.5rem;
      flex-wrap: wrap;
      justify-content: center;
    }

    main h1 {
      font-size: 2.2rem;
      max-width: 90%;
    }

    footer {
      flex-direction: column;
      gap: 1.5rem;
    }

    footer .about,
    footer .contacts {
      max-width: 100%;
    }
  }
</style>
</head>
<body>
<header>
  <div class="logo" tabindex="0">LGU Quick Appoint</div>
  <nav>
    <ul>
      <li><a href="#" tabindex="0">Home</a></li>
      <li><a href="#" tabindex="0">About</a></li>
      <li><a href="#" tabindex="0">Contact</a></li>
      <li><a href="login.php" tabindex="0">Login</a></li>
    </ul>
  </nav>
</header>
<main>
  <h1>Welcome to LGU Quick Appoint</h1>
</main>
<footer>
  <section class="about">
    <h3>About the system</h3>
    <p>
      LGU Quick Appoint is a revolutionary platform designed to streamline appointment scheduling for Local Government Units,
      providing residents with easy access to services and reducing wait times through efficient resource management.
    </p>
  </section>
  <section class="contacts">
    <h3>Contacts</h3>
    <ul>
      <li>
        <i class="fab fa-facebook-f" aria-hidden="true"></i>
        <a href="https://facebook.com" target="_blank" rel="noopener" tabindex="0">Facebook</a>
      </li>
      <li>
        <i class="fab fa-instagram" aria-hidden="true"></i>
        <a href="https://instagram.com" target="_blank" rel="noopener" tabindex="0">Instagram</a>
      </li>
      <li>
        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
        <a href="https://linkedin.com" target="_blank" rel="noopener" tabindex="0">LinkedIn</a>
      </li>
      <li>
        <i class="fas fa-envelope" aria-hidden="true"></i>
        <a href="mailto:contact@lguquickappoint.com" tabindex="0">contact@lguquickappoint.com</a>
      </li>
    </ul>
  </section>
</footer>
</body>
</html>
