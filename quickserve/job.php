<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location: login.php');
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Job Openings</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        background-image: url("./image/job_bg.jpg");
        background-repeat: no-repeat;
        background-size: cover;
      }
      .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }
      h1 {
        text-align: center;
        color: #333;
      }
      .job {
        margin-bottom: 20px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
      }
      .job h2 {
        margin-bottom: 10px;
        color: #333;
      }
      .job p {
        margin-bottom: 10px;
        color: #666;
      }
      .job .btn {
        display: inline-block;
        padding: 8px 16px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        transition: background-color 0.3s;
      }
      .job .btn:hover {
        background-color: #0056b3;
      }
      #applyForm {
        display: none;
        margin-top: 20px;
      }
      #applyForm h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
      }
      #applyForm label {
        display: block;
        margin-bottom: 10px;
        color: #333;
      }
      #applyForm input[type="text"],
      #applyForm input[type="email"],
      #applyForm input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
      }
      #applyForm input[type="submit"] {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
      }
      #applyForm input[type="submit"]:hover {
        background-color: #0056b3;
      }
      .btn1 {
        width: 10%;
        height: 40px;
        background-color: #f1e500;
        color: #ffffff;
        margin: 20px;
        font-size: 25px;
        text-align: center;
        font-weight: bold;
      }
      .btn1:hover {
        background-color: greenyellow;
        color: white;
      }
    </style>
  </head>
  <body>
    <header>
      <div class="btn1">
        <a href="home.php">Back</a>
      </div>
    </header>
    <div class="container">
      <h1>Current Job Openings</h1>
      <div class="job">
        <h2>Carpenter</h2>
        <p>Location: Vadodara, IN</p>
        <p>
          Description: We are looking for a skilled Carpenter to join our
          team...
        </p>
        <a href="#" class="btn apply-btn">Apply Now</a>
      </div>
      <div class="job">
        <h2>Car washer</h2>
        <p>Location: Nadiad, IN</p>
        <p>
          Description: We are looking for a skilled Car cleaner to join our
          team...
        </p>
        <a href="#" class="btn apply-btn">Apply Now</a>
      </div>
      <div class="job">
        <h2>Painter</h2>
        <p>Location: Anand, IN</p>
        <p>
          Description: We are looking for a skilled Painter to join our team...
        </p>
        <a href="#" class="btn apply-btn">Apply Now</a>
      </div>
    </div>

    <!-- Application Form -->
    <div id="applyForm" style="display: none">
      <div class="container">
        <h2 id="formTitle">Apply for Job</h2>
        <form action="submit.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="job_title" id="job_title" />
          <label for="name">Your Name:</label><br />
          <input type="text" id="name" name="name" required /><br />
          <label for="email">Your Email:</label><br />
          <input type="email" id="email" name="email" required /><br />
          <label for="resume">Upload Resume:</label><br />
          <input
            type="file"
            id="resume"
            name="resume"
            accept=".pdf,.doc,.docx"
            required
          /><br />
          <input type="submit" value="Submit Application" class="btn" />
        </form>
      </div>
    </div>

    <script>
      // JavaScript to show form when clicking on "Apply Now"
      document.querySelectorAll(".apply-btn").forEach((btn) => {
        btn.addEventListener("click", function (e) {
          e.preventDefault();
          const jobTitle = this.closest(".job").querySelector("h2").textContent;
          document.getElementById("job_title").value = jobTitle;
          document.getElementById(
            "formTitle"
          ).innerText = `Apply for ${jobTitle}`;
          document.getElementById("applyForm").style.display = "block";
        });
      });
    </script>
  </body>
</html>
