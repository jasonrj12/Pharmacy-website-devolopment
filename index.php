<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pills Station Pharmacy</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="font-inter bg-gray-50 text-gray-800">
  <?php include 'header.php'; ?>

  <main>
    <section class="relative flex items-center justify-center min-h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('https://www.cheapflights.com/news/wp-content/uploads/sites/136/2015/07/theme_medication-pills-pill-bottle-shutterstock-portfolio_549397639-1536x1024.jpg');">
      <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
      <div class="relative z-10 text-center p-8 bg-indigo-600 bg-opacity-80 rounded-xl max-w-2xl mx-4 animate-fadeInUp">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Welcome to <br>Pills Station Pharmacy</h1>
        <p class="text-lg md:text-xl text-gray-100 mb-6">Your Trusted Station for Health and Healing.</p>
        <a href="products.php" class="inline-flex items-center px-6 py-3 text-lg font-medium text-indigo-900 bg-white rounded-lg shadow-md hover:bg-indigo-100 hover:scale-105 transition-all duration-300">Explore Products</a>
      </div>
    </section>

    <!-- Social Media Stats Section -->
    <section class="icons py-16 bg-white">
      <div class="max-w-6xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
          <div class="bg-gray-50 border border-indigo-500 rounded-lg shadow-lg p-6 text-center hover:-translate-y-2 transition-transform duration-300">
            <i class="fas fa-users text-pink-500 text-4xl mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-800">1040+</h3>
            <p class="text-gray-600">Satisfied Patients</p>
          </div>
          <div class="bg-gray-50 border border-indigo-500 rounded-lg shadow-lg p-6 text-center hover:-translate-y-2 transition-transform duration-300">
            <i class="fas fa-procedures text-pink-500 text-4xl mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-800">500+</h3>
            <p class="text-gray-600">Bed Facility</p>
          </div>
          <div class="bg-gray-50 border border-indigo-500 rounded-lg shadow-lg p-6 text-center hover:-translate-y-2 transition-transform duration-300">
            <i class="fas fa-hospital text-pink-500 text-4xl mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-800">80+</h3>
            <p class="text-gray-600">Available Hospitals</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Our Location</h2>
        <div class="w-full h-96 rounded-lg overflow-hidden shadow-lg">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.902929805802!2d79.86373131477287!3d6.902207695003551!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae259631bc3b7e1%3A0x2a3d928e8d3e8c2!2sGalle%20Rd%2C%20Colombo%2006%2C%20Sri%20Lanka!5e0!3m2!1sen!2sus!4v1697681234567!5m2!1sen!2sus"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <style>
    /* Custom Animation */
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp {
      animation: fadeInUp 1.5s ease-out forwards;
    }
  </style>
</body>
</html>