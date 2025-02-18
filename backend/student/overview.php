<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Site Development in Progress</title>
  <style>
    body, html {
      height: 100%;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: Arial, sans-serif;
      background-color: #f0f0f0;
    }
    .container {
      text-align: center;
    }
    .loader {
      border: 16px solid rgb(51, 47, 47); /* Corrected */
      border-radius: 50%;
      border-top: 16px solid rgb(52, 219, 94); /* Corrected */
      width: 120px;
      height: 120px;
      animation: spin 2s linear infinite;
      margin: 0 auto 20px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="loader"></div>
    <h2>Website in Development</h2>
    <p>Please check back later.</p>
    <p>We are sorry for the inconvenience.</p>
    <a href="../../index.php"><button class="btn btn-primary">Back Home</button></a>
  </div>
</body>
</html>
