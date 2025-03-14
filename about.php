<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Pills Station Pharmacy</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      color: #333;
      background-color: #f9f9f9;
      line-height: 1.6;
      overflow-x: hidden;
    }

    main {
      margin-top: 90px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
      position: relative;
      overflow: hidden;
    }

    main::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('https://www.cheapflights.com/news/wp-content/uploads/sites/136/2015/07/theme_medication-pills-pill-bottle-shutterstock-portfolio_549397639-1536x1024.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      filter: brightness(0.5);
      z-index: -1;
    }

    .about-section {
      background: rgba(255, 255, 255, 0.9);
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 15px 25px rgba(0, 0, 0, 0.2);
      max-width: 1200px; /* Increased max-width for landscape layout */
      width: 100%;
      animation: fadeIn 1.2s ease;
    }

    .about-section h2 {
      font-size: 36px;
      color: #005f73;
      margin-bottom: 20px;
      text-align: center;
    }

    .about-section p {
      font-size: 18px;
      color: #555;
      margin-bottom: 20px;
      line-height: 1.8;
    }

    /* Container for landscape layout */
    .content-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 20px;
    }

    .mission, .vision, .values, .branches {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      flex: 1 1 calc(50% - 20px); /* Each section takes up half the width minus gap */
      min-width: 300px; /* Ensures sections don't shrink too much on smaller screens */
    }

    .mission:hover, .vision:hover, .values:hover, .branches:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .mission h3, .vision h3, .values h3, .branches h3 {
      color: #0a9396;
      margin-bottom: 10px;
      font-size: 24px;
    }

    .mission ul, .vision ul, .values ul, .branches ul {
      list-style: disc;
      padding-left: 20px;
    }

    .mission ul li, .vision ul li, .values ul li, .branches ul li {
      margin-bottom: 10px;
      color: #444;
    }

    footer {
      background-color: #333;
      color: #bbb;
      text-align: center;
      padding: 15px;
      font-size: 14px;
      margin-top: 50px;
    }

    footer p {
      margin: 5px 0;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .content-container {
        flex-direction: column; /* Stack sections vertically on smaller screens */
      }

      .mission, .vision, .values, .branches {
        flex: 1 1 100%; /* Full width on smaller screens */
      }

      .about-section {
        max-width: 100%; /* Full width on smaller screens */
        padding: 20px; /* Reduce padding for smaller screens */
      }
    }
  </style>
</head>
<body>

  <?php include 'header.php'; ?>
  <main>
    <section class="about-section">
      <h2>About Pills Station Pharmacy</h2>
      <p>Founded in 2022, Pills Station Pharmacy has grown to become a trusted healthcare provider, dedicated to promoting health and wellness in our community. We are committed to delivering personalized, quality care that meets the unique needs of each individual.</p>
      
      <div class="content-container">
        <div class="mission">
          <h3><i class="fas fa-bullseye" style="margin-right: 10px; color: #0a9396;"></i> Our Mission</h3>
          <p>Our mission is to provide accessible, reliable, and compassionate healthcare solutions that empower individuals to lead healthier lives. We strive to be a valuable resource for health and wellness, with a commitment to quality, service, and innovation.</p>
        </div>
        
        <div class="vision">
          <h3><i class="fas fa-eye" style="margin-right: 10px; color: #0a9396;"></i> Our Vision</h3>
          <p>Our vision is to be the preferred pharmacy partner in our community, recognized for our integrity, quality, and dedication to enhancing the well-being of our customers. We aspire to expand our reach and impact by continuously improving our services and embracing advancements in healthcare.</p>
        </div>
        
        <div class="branches">
  <h3><i class="fas fa-map-marker-alt" style="margin-right: 10px; color: #0a9396;"></i> Our Branches</h3>
  <ul>
    <li>Wellawatta</li>
    <li>Batticaloa</li>
    <li>Ganemulla</li>
    <li>Mannar</li>
  </ul>
</div>
        
        <div class="values">
          <h3><i class="fas fa-heart" style="margin-right: 10px; color: #0a9396;"></i> Our Values</h3>
          <p>At Pills Station Pharmacy, our values are at the core of everything we do:</p>
          <ul>
            <li>Commitment to Quality Care</li>
            <li>Empathy and Compassion</li>
            <li>Integrity and Trustworthiness</li>
            <li>Community Focus</li>
            <li>Innovation and Improvement</li>
          </ul>
        </div>
      </div>
    </section>
  </main>
  
  <?php include 'footer.php'; ?>
</body>
</html>