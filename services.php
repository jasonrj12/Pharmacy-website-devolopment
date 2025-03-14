<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pharmacy Services - Pills Station Pharmacy</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      color: #fff;
      background-image: url('https://www.cheapflights.com/news/wp-content/uploads/sites/136/2015/07/theme_medication-pills-pill-bottle-shutterstock-portfolio_549397639-1536x1024.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
      overflow-x: hidden;
    }

    main {
      margin-top: 80px;
      text-align: center;
      margin-bottom: 80px;
    }

    .services-section {
      padding: 30px;
      background: rgba(255, 255, 255, 0.8);
      box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3);
      border-radius: 15px;
      margin: 50px auto;
      width: 90%; /* Increased width for landscape layout */
      max-width: 1400px; /* Added max-width for better control on large screens */
      backdrop-filter: blur(10px);
      transition: transform 0.3s;
    }

    .services-section:hover {
      transform: translateY(-10px);
    }

    .services-section h2 {
      font-size: 40px;
      color: #333;
      margin-bottom: 25px;
      font-weight: 700;
      text-transform: uppercase;
    }

    /* Container for landscape layout */
    .services-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 20px;
    }

    .service {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s;
      flex: 1 1 calc(33.33% - 20px); /* Each service takes up one-third of the width minus gap */
      min-width: 300px; /* Ensures services don't shrink too much on smaller screens */
    }

    .service:hover {
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
    }

    .service h3 {
      font-size: 28px;
      color: #28a745;
      margin-bottom: 10px;
    }

    .service p {
      font-size: 18px;
      color: #555;
      line-height: 1.6;
    }

    footer {
      background-color: rgba(0, 0, 0, 0.9);
      color: #ccc;
      text-align: center;
      padding: 15px 0;
      font-size: 14px;
      position: relative;
      bottom: 0;
      width: 100%;
    }

    footer p {
      margin: 0;
    }

    .footer-address,
    .footer-phone {
      font-size: 12px;
      margin-top: 5px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      .service {
        flex: 1 1 calc(50% - 20px); /* Two services per row on medium screens */
      }
    }

    @media (max-width: 768px) {
      main {
        margin-bottom: 60px;
      }

      .services-section {
        width: 95%;
        padding: 20px;
      }

      .services-section h2 {
        font-size: 32px;
      }

      .service {
        flex: 1 1 100%; /* Full width on smaller screens */
      }

      .service h3 {
        font-size: 24px;
      }

      .service p {
        font-size: 16px;
      }
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>
  <main>
    <section class="services-section">
      <h2>Pharmacy Services</h2>
      <div class="services-container">
        <div class="service">
          <h3>Medication Management</h3>
          <p>We provide comprehensive medication reviews and management to ensure your medications are effective and safe.</p>
        </div>
        <div class="service">
          <h3>Immunization Services</h3>
          <p>Stay protected with our immunization services. We offer a variety of vaccines for adults and children.</p>
        </div>
        <div class="service">
          <h3>Health Screenings</h3>
          <p>Regular health screenings help you stay informed about your health. We offer blood pressure checks, cholesterol screenings, and more.</p>
        </div>
        <div class="service">
          <h3>Consultation Services</h3>
          <p>Our pharmacists are here to offer personalized health advice. Book a consultation to discuss any health concerns.</p>
        </div>
        <div class="service">
          <h3>Compounding Services</h3>
          <p>We offer personalized medication compounding to meet unique patient needs, ensuring effective treatment options.</p>
        </div>
      </div>
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>