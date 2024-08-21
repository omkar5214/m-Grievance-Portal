<?php
session_start();
include('include/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $mysql_hostname = "localhost";
    $mysql_user = "root";
    $mysql_password = "";
    $mysql_database = "cms";
    $bd = mysqli_connect($mysql_hostname, $mysql_user, $mysql_password, $mysql_database) or die("Could not connect database");

    // Function to fetch complaint counts by category
    function getComplaintCounts($conn) {
        $sql = "SELECT c.categoryName, COUNT(*) AS complaint_count
                FROM tblcomplaints AS t
                JOIN category AS c ON t.category = c.id
                GROUP BY c.categoryName";
        $result = $conn->query($sql);

        $complaintCounts = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $complaintCounts[] = $row;
            }
        }

        return $complaintCounts;
    }

    // Function to fetch complaints by category
    function getComplaintsByCategory($conn) {
        $sql = "SELECT c.categoryName, t.*
                FROM tblcomplaints AS t
                JOIN category AS c ON t.category = c.id
                ORDER BY c.categoryName";
        $result = $conn->query($sql);

        $complaintsByCategory = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $category = $row['categoryName'];
                if (!isset($complaintsByCategory[$category])) {
                    $complaintsByCategory[$category] = array();
                }
                $complaintsByCategory[$category][] = $row;
            }
        }

        return $complaintsByCategory;
    }

    function getComplaintsByLocation($conn) {
        $sql = "SELECT state, COUNT(*) AS complaint_count
                FROM tblcomplaints
                GROUP BY state";
        $result = $conn->query($sql);
    
        $complaintsByLocation = array();
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $complaintsByLocation[] = $row;
            }
        }
    
        return $complaintsByLocation;
    }
    
    // Fetch complaints by location
    $complaintsByLocation = getComplaintsByLocation($bd);
    
    // Fetch complaint counts and complaints by category
    $complaintCounts = getComplaintCounts($bd);
    $complaintsByCategory = getComplaintsByCategory($bd);

    // Close the database connection
    mysqli_close($bd);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Complaint Dashboard</title>
    <!-- Include Bootstrap CSS from a CDN -->
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
    <style>
        <style>
        body {
            font-family: Arial, sans-serif;
            
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            padding: 20px 0;
        }

        h2 {
            background-color: #333;
            color: #fff;
            padding: 8px;
            margin-top: 20px;
            text-align:center;
        }


        .center-boxes {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .category-box {
            border: 1px solid #ccc;
            width: 150px;
            height: 150px;
            margin: 10px;
            text-align: center;
            padding: 20px;
            transition: transform 0.2s;
            float: left;
            background-color: #fff;
        }

        .category-box:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            background-color: #fff;
            margin: 10px auto;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: brown;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btncont {
            margin-top: 20px;
            text-align: center;
        }

        .btns {
            align-items: center;
        }

        .btn {
            padding: 10px;
        }

   
        h1 {
            text-align: center;
            padding: 20px 0;
        }

        .location-box {
            border: 1px solid #ccc;
            width: 150px;
            height: 150px;
            margin: 10px;
            text-align: center;
            padding: 20px;
            background-color: #fff;
            transition: transform 0.2s;
            float: left; /* Make the boxes float left */
        }

        .location-box:hover {
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            background-color: #333;
            color: #fff;
            padding: 8px;
            text-align: center;
            margin-top: 20px;
        }
    </style>
    
</head>
<body style="background-color: beige;">
    <?php include 'include/header.php'; ?>
    <h1>Complaint Dashboard</h1>
    <div class="container mt-5">
        <div class="center-boxes">
            <?php foreach ($complaintCounts as $category): ?>
                <div class="category-box">
                    <h3><?php echo $category['categoryName']; ?></h3>
                    <p>Complaints: <?php echo $category['complaint_count']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="clearfix"></div> <!-- Add clearfix to clear the floats -->
    </div>

    <h1>Complaint Categories</h1>

    <?php foreach ($complaintsByCategory as $category => $complaints): ?>
        <h2><?php echo $category; ?></h2>
        <table border="1">
            <tr>
                <th>Complaint ID</th>
                <th>Complaint Text</th>
                <th>Location</th>
                <th>Status</th>
                <!-- Add more columns here if needed -->
            </tr>
            <?php foreach ($complaints as $complaint): ?>
                <tr>
                    <td><?php echo $complaint['complaintNumber']; ?></td>
                    <td><?php echo $complaint['complaintDetails']; ?></td>
                    <td><?php echo $complaint['state']; ?></td>
                    <td><?php echo $complaint['status']; ?></td>
                    <!-- Add more columns here if needed -->
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>

    <h1>Locations</h1>
    
        <div class="center-boxes">
        <?php foreach ($complaintsByLocation as $location): ?>
        <div class="category-box">
            <h3><?php echo $location['state']; ?></h3>
            <p>Complaints: <?php echo $location['complaint_count']; ?></p>
        </div>
        <?php endforeach; ?>
        </div>
    
    <!-- Include Bootstrap JS and jQuery (optional) from a CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Add buttons at the bottom -->
    <div class="container mt-3 btncont">
        <div class="text-center btns">
            <a href="notprocess-complaint.php"><button class="btn btn-danger mr-2">Not Processed Grievances</button></a>
            <a href="inprocess-complaint.php"><button class="btn btn-primary mr-2">In Process Grievances</button></a>
            <a href="closed-complaint.php"><button class="btn btn-success">Closed Grievances</button></a>
        </div>
    </div>
    <?php include('include/footer.php');?>
</body>
</html>

