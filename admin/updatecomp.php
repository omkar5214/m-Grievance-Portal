<?php
session_start();
include('include/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/vendor/autoload.php'; // Include the PHPMailer library

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_POST['update'])) {
        $complaintnumber = $_GET['cid'];
        $status = $_POST['status'];
        $remark = $_POST['remark'];

        $userIdQuery = mysqli_query($bd, "SELECT userId FROM tblcomplaints WHERE complaintNumber = '$complaintnumber'");
        $userIdRow = mysqli_fetch_assoc($userIdQuery);
        $userId = $userIdRow['userId'];

        // Fetch the email address of the user from the 'users' table
        $emailQuery = mysqli_query($bd, "SELECT userEmail FROM users WHERE id = '$userId'");
        $emailRow = mysqli_fetch_assoc($emailQuery);
        $toEmail = $emailRow['userEmail'];

        $query = mysqli_query($bd, "INSERT INTO complaintremark(complaintNumber, status, remark) VALUES ('$complaintnumber','$status','$remark')");
        $sql = mysqli_query($bd, "UPDATE tblcomplaints SET status='$status' WHERE complaintNumber='$complaintnumber'");

        // Send an email using PHPMailer
        $mail = new PHPMailer();

        $mail->SMTPDebug = 0;                                       
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com;';                    
        $mail->SMTPAuth   = true;                             
        $mail->Username   = 'm.greivanceportal@gmail.com';   // Enter your gmail-id              
        $mail->Password   = 'zbez qhca qffx qfql';     // Enter your gmail app password that you generated 
        $mail->SMTPSecure = 'tls';                              
        $mail->Port       = 587;

        $mail->setFrom('m.greivanceportal@gmail.com', 'M-Grievance Portal'); // Replace with your email address and name
        $mail->addAddress($toEmail);
        $mail->Subject = 'Greivance Update';
        $mail->Body = 'Your grievance has been updated with the following status: ' . $status;

        if ($mail->send()) {
            echo "<script>alert('Complaint details updated successfully and email sent');</script>";
        } else {
            echo "<script>alert('Email could not be sent.');</script>";
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>User Profile</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="anuj.css" rel="stylesheet" type="text/css">
</head>
<body>

<div style="margin-left:50px;">
 <form name="updateticket" id="updatecomplaint" method="post"> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td  >&nbsp;</td>
      <td >&nbsp;</td>
    </tr>
    <tr height="50">
      <td><b>Complaint Number</b></td>
      <td><?php echo htmlentities($_GET['cid']); ?></td>
    </tr>
    <tr height="50">
      <td><b>Status</b></td>
      <td><select name="status" required="required">
      <option value="">Select Status</option>
      <option value="in process">In Process</option>
    <option value="closed">Closed</option>
        
      </select></td>
    </tr>

    <tr height="50">
      <td><b>Remark</b></td>
      <td><textarea name="remark" cols="50" rows="10" required="required"></textarea></td>
    </tr>

    <tr height="50">
      <td>&nbsp;</td>
      <td><input type="submit" name="update" value="Submit"></td>
    </tr>

    <tr><td colspan="2">&nbsp;</td></tr>

    <tr>
      <td></td>
      <td >   
        <input name="Submit2" type="submit" class="txtbox4" value="Close this window " onClick="return f2();" style="cursor: pointer;"  />
      </td>
    </tr>
</table>
 </form>
</div>

</body>
</html>
