<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Abeth Hardware</title>
  <link rel="stylesheet" href="index.css" />
</head>
<body>

  <nav>
    <div class="logo"><strong>Abeth Hardware</strong></div>
    <div class="menu">
      <a href="#">Home</a>
      <a href="#categories">Categories</a>
      <a href="#about">About</a>
      <a href="#contact">Contact</a>

      <?php if (isset($_SESSION['username'])): ?>
        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="#" onclick="openModal('login-modal')">Log In</a> |
        <a href="#" onclick="openModal('register-modal')">Sign Up</a>
      <?php endif; ?>
    </div>
  </nav>

  <header>
    <h1>Exclusive Range of Hardware Materials</h1>
    <p>Gravel, Sand, Hollow Blocks, Cement, and More</p>

    <?php if (isset($_SESSION['username'])): ?>
      <button onclick="window.location.href='products.php'">Shop Now!</button>
    <?php else: ?>
      <button onclick="openModal('login-modal')">Shop Now!</button>
    <?php endif; ?>
  </header>

  <!-- Categories Section -->
  <section id="categories">
    <h2 class="section-title">Categories</h2>
    <div class="categories">
      <div class="cat-card">
        <img src="istockphoto-92775187-2048x2048.jpg" alt="Gravel">
        <h3>Gravel</h3>
      </div>
      <div class="cat-card">
        <img src="pexels-david-iloba-28486424-17268238.jpg" alt="Sand">
        <h3>Sand</h3>
      </div>
      <div class="cat-card">
        <img src="download.jpg" alt="Hollow Blocks">
        <h3>Hollow Blocks</h3>
      </div>
      <div class="cat-card">
        <img src="shopping.webp" alt="Cement">
        <h3>Cement</h3>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <div id="about">
    <div class="about-img">
      <img src="about.jpg" alt="About Abeth Hardware">
    </div>
    <div class="about-text">
      <h2>About Us</h2>
      <p>Abeth Hardware is your trusted supplier of high-quality construction materials. From sand and gravel to cement and steel, we provide durable and affordable products to help you build your dreams. With years of experience in the hardware industry, we are committed to delivering excellent customer service and reliable materials for every project, big or small.</p>
    </div>
  </div>

  <!-- CONTACT -->
  <div id="contact">
    <div class="contact-info">
      <h2>Contact Us</h2>
      <p><b>Address:</b> B3/L11 Tiongquaio St. Manuyo Dos, Las Pinas City.</p>
      <p><b>Phone:</b> +63 966-866-9728 / +63 977-386-8066</p>
      <p><b>Email:</b> abethhardware@gmail.com</p>
      <p><b>Business Hours:</b> Mon–Sat: 8:00 AM – 5:00 PM</p>
    </div>
    <div class="map">
      <img src="map.jpg" alt="Abeth Hardware Location">
    </div>
  </div>

  <footer>
    <p>&copy; 2025 Abeth Hardware. All rights reserved.</p>
  </footer>

  <!-- LOGIN MODAL -->
  <div id="login-modal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('login-modal')">&times;</span>
      <h2>Sign In</h2>
      <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username or Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
        <p>Don’t have an account? <a href="#" onclick="switchModal('login-modal','register-modal')">Sign Up</a></p>
      </form>
    </div>
  </div>

  <!-- REGISTER MODAL -->
  <div id="register-modal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal('register-modal')">&times;</span>
      <h2>Create Account</h2>
      <form method="POST" action="register.php">
        <input type="text" name="fname" placeholder="First Name" required>
        <input type="text" name="lname" placeholder="Last Name" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="text" name="contact" placeholder="Contact Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="#" onclick="switchModal('register-modal','login-modal')">Login</a></p>
      </form>
    </div>
  </div>

  <script>
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
    function switchModal(hideId, showId) { closeModal(hideId); openModal(showId); }
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) event.target.style.display = 'none';
    };
  </script>

</body>
</html>
