<?php
include_once("session.php");
include("../db/db.php");
$id=$_GET['clientid'];

$sql="SELECT techietask.ClientName,techietask.Region,papdailysales.AptLayout,papdailysales.Floor,papdailysales.ClientID,techietask.ClientContact,techietask.ClientAvailability,techietask.BuildingName,techietask.Region,techietask.Date,techieteams.Team_ID,techieteams.Techie_1,techieteams.Techie_2,
papdailysales.BuildingCode,papdailysales.Floor from papdailysales LEFT JOIN 
techietask on techietask.ClientID=papdailysales.ClientID LEFT JOIN techieteams ON techieteams.Team_ID=techietask.TeamID WHERE techietask.ClientID is not null AND techietask.ClientID=$id AND techieteams.Team_ID='".$_SESSION['TeamID']."'";
$result=mysqli_query($connection,$sql);
$row=mysqli_fetch_assoc($result);
$clientid=$row['ClientID'];
$teamid=$row['Team_ID'];
$date=$row['Date'];
$reg=$row['Region'];
$t1=$row['Techie_1'];
$t2=$row['Techie_2'];
$floor=$row['Floor'];
$layout=$row['AptLayout'];


if(isset($_POST['submit']) && !empty($_FILES["image"]["name"])) {
$Team_ID=$_POST['teamid'];
$MacAddress = $_POST['macaddress'];
$SerialNumber = $_POST['serialnumber'];
$DateInstalled = $_POST['dateinstalled'];
$ClientID = $_POST['ClientID'];
$Region = $_POST['region'];
$techie1 = $row['Techie_1'];
$techie2 = $row['Techie_2'];
$Floor = $_POST['floor'];
$Note = $_POST['note'];
$layout = $_POST['layout'];

ini_set('upload_max_filesize', '60M');
ini_set('post_max_size', '70M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 300);

$fileName = basename($_FILES["image"]["name"]); 
          $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
           
          // Allow certain file formats 
          $allowTypes = array('jpg','png','jpeg','gif'); 
          if(in_array($fileType, $allowTypes)){ 
              $image = $_FILES['image']['tmp_name']; 
              $imgContent = addslashes(file_get_contents($image)); 

//checking connection
if($connection->connect_error){
    die('connection failed : '.$connection->connect_error);
}
else
{
  $stmt= $connection->prepare("select * from papinstalled where MacAddress= ?");
  $stmt->bind_param("s",$MacAddress);
  $stmt->execute();
  $stmt_result= $stmt->get_result();
  if($stmt_result->num_rows>0){
    echo "<script>alert('The Macaddress Already Exists');</script>";
    echo '<script>window.location.href="my-task.php";</script>';
  }
  else{
       if(strlen(trim($MacAddress)) <17 || strlen(trim($MacAddress))>17 ){
      echo "<script>alert('Incorrect Mac Address Format');</script>";
      echo '<script>window.location.href="my-task.php";</script>';
     }
     else{
     // Insert records into database 
     $sql="update papdailysales set ClientID=$id,Floor='$Floor',AptLayout='$layout' where ClientID=$id";
     $result=mysqli_query($connection,$sql);
     $insert = $connection->query("INSERT into papinstalled (Team_ID,ClientID,MacAddress,SerialNumber,DateInstalled,Region,Image,Note,Floor,AptLayout) VALUES ('$Team_ID','$ClientID','$MacAddress','$SerialNumber','$DateInstalled','$Region','$imgContent','$Note','$Floor','$layout')"); 
    
     if($insert && $result){ 
     echo '<script>alert("Submitted!")</script>';
      echo '<script>window.location.href="my-task.php";</script>';
  }else{ 
    echo "<script>alert('UnSuccessfull.');</script>"; 
echo '<script>window.location.href="my-task.php";</script>';
  }  
    }
  }

}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
  <meta name="description" content=""/>
  <meta name="author" content=""/>
  <title>New PAP</title>
  <!-- loader--
  <link href="../assets/css/pace.min.css" rel="stylesheet"/>--
  <script src="../assets/js/pace.min.js"></script>
  <!--favicon-->
  <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
  <!-- simplebar CSS-->
  <link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet"/>
  <!-- Bootstrap core CSS-->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- animate CSS-->
  <link href="../assets/css/animate.css" rel="stylesheet" type="text/css"/>
  <!-- Icons CSS-->
  <link href="../assets/css/icons.css" rel="stylesheet" type="text/css"/>
  <!-- Sidebar CSS-->
  <link href="../assets/css/sidebar-menu.css" rel="stylesheet"/>
  <!-- Custom Style-->
  <link href="../assets/css/app-style.css" rel="stylesheet"/>
  
  
</head>

<body class="bg-theme bg-theme11">

<!-- start loader -->
   <div id="pageloader-overlay" class="visible incoming"><div class="loader-wrapper-outer"><div class="loader-wrapper-inner" ><div class="loader"></div></div></div></div>
   <!-- end loader -->

<!-- Start wrapper-->
 <div id="wrapper">

 <!--Start sidebar-wrapper-->
 <div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
     <div class="brand-logo">
      <a href="TechieDashboard.php">
       <h5 class="logo-text"><?php echo $_SESSION['TeamID'];?></h5>
     </a>
   </div>
   <ul class="sidebar-menu do-nicescrol">
      <li class="sidebar-header">MAIN NAVIGATION</li>
      <li>
        <a href="TechieDashboard.php">
          <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>

     <!-- <li>
        <a href="icons.php">
          <i class="zmdi zmdi-invert-colors"></i> <span>UI Icons</span>
        </a>
      </li>-->
       <li>
        <a href="my-task.php">
          <i class="zmdi zmdi-format-list-bulleted"></i> <span>My Task</span>
          <small class="badge float-right badge-light"><?php
                                            $query="SELECT  COUNT(techieteams.Team_ID)as MyTask from papdailysales LEFT JOIN techietask on techietask.ClientID=papdailysales.ClientID LEFT JOIN techieteams ON techieteams.Team_ID=techietask.TeamID  LEFT JOIN papinstalled ON papinstalled.ClientID=papdailysales.ClientID WHERE 
                                             techietask.ClientID is not null AND papinstalled.ClientID is null AND techieteams.Team_ID='".$_SESSION['TeamID']."'";
                                             $data=mysqli_query($connection,$query);
                                             while($row=mysqli_fetch_assoc($data)){
                                             echo $row['MyTask']."<br><br>";
                                              }
                                              ?></small>
        </a>
      </li>


      <li>
        <a href="profile.php">
          <i class="zmdi zmdi-face"></i> <span>Profile</span>
        </a>
      </li>

     <!-- <li>
        <a href="login.php" target="_blank">
          <i class="zmdi zmdi-lock"></i> <span>Login</span>
        </a>
      </li>-->

       <li>
        <a href="logout.php" target="_blank">
          <i class="zmdi zmdi-lock"></i> <span>Logout</span>
        </a>
      </li>

     <!-- <li class="sidebar-header">LABELS</li>
      <li><a href="javaScript:void();"><i class="zmdi zmdi-coffee text-danger"></i> <span>Important</span></a></li>
      <li><a href="javaScript:void();"><i class="zmdi zmdi-chart-donut text-success"></i> <span>Warning</span></a></li>
      <li><a href="javaScript:void();"><i class="zmdi zmdi-share text-info"></i> <span>Information</span></a></li>-->

    </ul>
   
   </div>
   <!--End sidebar-wrapper-->
  

<!--Start topbar header-->
<header class="topbar-nav">
 <nav class="navbar navbar-expand fixed-top">
  <ul class="navbar-nav mr-auto align-items-center">
    <li class="nav-item">
      <a class="nav-link toggle-menu" href="javascript:void();">
       <i class="icon-menu menu-icon"></i>
     </a>
    </li>
    <li class="nav-item">
      <form class="search-bar">
        <input type="text" class="form-control" placeholder="Enter keywords">
         <a href="javascript:void();"><i class="icon-magnifier"></i></a>
      </form>
    </li>
  </ul>
    
    <li class="nav-item">
      <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown" href="#">
        <span class="user-profile"><img src="https://via.placeholder.com/110x110" class="img-circle" alt="user avatar"></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-right">
       <li class="dropdown-item user-details">
        <a href="javaScript:void();">
           <div class="media">
             <div class="avatar"><img class="align-self-start mr-3" src="https://via.placeholder.com/110x110" alt="user avatar"></div>
            <div class="media-body">
          
            <p class="user-subtitle"><?php echo $_SESSION['TeamID'];?></p>
            </div>
           </div>
          </a>
        </li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-item" >Team ID : <?php echo $_SESSION['TeamID'];?></li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-item" >Techie 1 : <?php echo $_SESSION['Techie1'];?></li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-item" >Techie 2 : <?php echo $_SESSION['Techie2'];?></li>
        <li class="dropdown-divider"></li>
        <li class="dropdown-item" ><i class="icon-power mr-2" ></i></li>
      </ul>
    </li>
  </ul>
</nav>
</header>
<!--End topbar header-->
<div class="clearfix"></div>
	
  <div class="content-wrapper">
    <div class="container-fluid">

    <div class="row mt-3">
      <div class="col-lg-6">
         <div class="card">
           <div class="card-body">
           <div class="card-title">PAP Details</div>
           <hr>
            <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="form-group">
            <label for="input-1">Client ID</label>
            <input type="text" class="form-control" id="input-1" name="ClientID" value="<?php echo $clientid?>" readonly>
           </div>
           <div class="form-group">
            <label for="input-1">Team ID</label>
            <input type="text" class="form-control" id="input-1" name="teamid" value="<?php echo $teamid?>" readonly>
           </div>
           <div class="form-group">
            <label for="input-4">Floor<i style="color:red;">*</i></label>
            <input type="text" class="form-control" id="input-4" value="<?php echo $floor?>" name="floor" required>
           </div>
           <div class="form-group">
            <label for="input-1">Apt Layout<i style="color:red;">*</i></label>
            <select type="text" class="form-control" id="input-1" name="layout"value="<?php echo $layout?>" placeholder="Enter Your Name">
              <option value="<?php echo $layout?>"><?php echo $layout?></option>
              <option value="Single">Single</option>
              <option value="Double">Double</option>
              <option value="Bedsitter">Bedsitter</option>
              <option value="1 BR">1 BR</option>
              <option value="2 BR">2 BR</option>
              <option value="3 BR">3 BR</option>
              <option value="4 BR and above">4 BR and above</option>
            </select>
           </div>
           <div class="form-group">
            <label for="input-3">MAC Address<i style="color:red;">*</i></label>
            <input type="text" class="form-control" id="input-3" name="macaddress" style="text-transform: uppercase" placeholder="Format:AB-CD-EF-GH-IJ-KL" maxlength="17" required>
           </div>
           <div class="form-group">
            <label for="input-3">Serial Number<i style="color:red;">*</i></label>
            <input type="text" class="form-control" id="input-3" placeholder="Serial Number" name="serialnumber" maxlength="13" required>
           </div>
           <div class="form-group">
            <label for="input-3">Pap Image<i style="color:red;">*</i></label>
            <input type="file" class="form-control" id="input-3" placeholder="Pac Image" name="image" required>
           </div>
           <div class="form-group">
            <label for="input-4">Date Installed<i style="color:red;">*</i></label>
            <input type="date" class="form-control" id="input-5" name="dateinstalled" required>
           </div>
           <div class="form-group">
            <label for="input-4">Region</label>
            <input type="text" class="form-control" id="input-4" value="<?php echo $reg?>" name="region" readonly>
           </div>
           <div class="form-group">
            <label for="input-4">Note<i style="color:red;">*</i></label>
            <input type="text" class="form-control" id="input-4"  name="note" placeholder="Comments/Suggestion/Observations" required>
           </div>
           <div class="form-group">
            <button type="submit" name="submit"  class="btn btn-light px-5"><i class="icon-check"></i> Submit</button>
          </div>
          </form>
         </div>
         </div>
      </div>


	<!--start overlay-->
		  <div class="overlay toggle-menu"></div>
		<!--end overlay-->

    </div>
    <!-- End container-fluid-->
    
   </div><!--End content-wrapper-->
   <!--Start Back To Top Button-->
    <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
    <!--End Back To Top Button-->
	
	<!--Start footer--
	<footer class="footer">
      <div class="container">
        <div class="text-center">
          Copyright ?? Konnect 2022
        </div>
      </div>
    </footer>
	<!--End footer-->
	
	<!--start color switcher-->
   <div class="right-sidebar">
    <div class="switcher-icon">
      <i class="zmdi zmdi-settings zmdi-hc-spin"></i>
    </div>
    <div class="right-sidebar-content">

      <p class="mb-0">Gaussion Texture</p>
      <hr>
      
      <ul class="switcher">
        <li id="theme1"></li>
        <li id="theme2"></li>
        <li id="theme3"></li>
        <li id="theme4"></li>
        <li id="theme5"></li>
        <li id="theme6"></li>
      </ul>

      <p class="mb-0">Gradient Background</p>
      <hr>
      
      <ul class="switcher">
        <li id="theme7"></li>
        <li id="theme8"></li>
        <li id="theme9"></li>
        <li id="theme10"></li>
        <li id="theme11"></li>
        <li id="theme12"></li>
		<li id="theme13"></li>
        <li id="theme14"></li>
        <li id="theme15"></li>
      </ul>
      
     </div>
   </div>
  <!--end color switcher-->
   
  </div><!--End wrapper-->


  <!-- Bootstrap core JavaScript-->
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
	
 <!-- simplebar js -->
  <script src="../assets/plugins/simplebar/js/simplebar.js"></script>
  <!-- sidebar-menu js -->
  <script src="../assets/js/sidebar-menu.js"></script>
  
  <!-- Custom scripts -->
  <script src="../assets/js/app-script.js"></script>
 <script>
 var todayDate= new Date();
 var month= todayDate.getMonth() + 1;
 var year= todayDate.getFullYear();
 var todate=todayDate.getDate();
if(todate<10){
  todate= "0"+ todate;
}
if(month<10){
  month= "0"+ month;
}
 maxdate= year +"-" + month + "-" + todate;
 document.getElementById("input-5").setAttribute("max",maxdate);
 </script>
</body>
<script>
 var todayDate= new Date();
 var month= todayDate.getMonth() + 1;
 var year= todayDate.getFullYear();
 var todate=todayDate.getDate();
if(todate<10){
  todate= "0"+ todate;
}
if(month<10){
  month= "0"+ month;
}
 maxdate= year +"-" + month + "-" + todate;
 document.getElementById("input-1").setAttribute("max",maxdate);
 </script> 
</body>
</html>
