<?php
@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
   exit(); // Add exit to stop further execution
};

if(isset($_GET['delete'])){
	$delete_id = $_GET['delete'];
	$delete_applications = $conn->prepare("DELETE FROM `job_applications` WHERE id = ?");
	$delete_applications->bind_param("i", $delete_id); // "i" specifies the type of the parameter (integer)
	$delete_applications->execute();
	header('location:admin-job.php');
	exit(); // Add exit to stop further execution
 }
 

$job_applications = array(); // Initialize an array to store fetched results

// Fetch job applications including resume paths
$select_applications = $conn->prepare("SELECT id, job_title, name, email, resume  FROM `job_applications`");
$select_applications->execute();
$result = $select_applications->get_result(); // Get result set from prepared statement

while ($row = $result->fetch_assoc()) {
    $job_applications[] = $row; // Append each row to the array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Job Applications</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

  

   <style>
	.title{
		font-size: 35px;
		text-align: center;
	}
      .box-container {
          display: flex;
          flex-wrap: wrap;
          gap: 20px;
		  font-size: 20px;
		  
      }

      .box {
          width: calc(100% - 20px);
          border: 1px solid #ccc;
          padding: 20px;
      }

      .table-container {
          overflow-x: auto;
      }

      table {
          width: 100%;
          border-collapse: collapse;
          border-spacing: 0;
      }

      th, td {
          padding: 8px;
          border: 1px solid #ddd;
      }

      th {
          background-color: #f2f2f2;
          text-align: left;
      }

      .delete-btn {
          color: #fff;
          background-color: #ff0000;
          padding: 5px 10px;
          border-radius: 5px;
          text-decoration: none;
      }

      .delete-btn:hover {
          background-color: #cc0000;
      }
	  .view{
		background-color: greenyellow;
		text-decoration: underline;

	  }
	  .view:hover{
		background-color: #ddd;
		color: blue;
	  }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="user-accounts">

   <h1 class="title">Job Applications</h1>

   <div class="box-container">
      <?php if(empty($job_applications)): ?>
         <p>No job applications found.</p>
      <?php else: ?>
         <div class="box">
            <div class="table-container">
               <table>
                  <thead>
                     <tr>
                        <th>Application ID</th>
                        <th>Job Title</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Resume</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach($job_applications as $application): ?>
                        <tr>
                           <td><?= $application['id']; ?></td>
                           <td><?= $application['job_title']; ?></td>
                           <td><?= $application['name']; ?></td>
                           <td><?= $application['email']; ?></td>
						   <td class="view"><a href="../<?= $application['resume']; ?>" target="_blank">View Resume</a></td>
                           <td>
                              <a href="admin-job.php?delete=<?= $application['id']; ?>" onclick="return confirm('Delete this application?');" class="delete-btn">Delete</a>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
            </div>
         </div>
      <?php endif; ?>
   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>
