<!DOCTYPE html>
<html>
<head>
<title>Sides</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-light-green.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>
<!--Navigation bar-->
<div id="nav-placeholder"></div>
<script>
$(function(){
    $("#nav-placeholder").load("/kirk/css/nav_kirk.html");
});
</script>

<!--end of Navigation bar-->

		<!--Header-->
		<div id="header-placeholder">
		
		</div>
		
		<script>
		$(function(){
				$("#header-placeholder").load("/kirk/css/header_kirk.html");
		});
		</script>
		<!--end of header-->

<!--Thirds-->

<div class="w3-container w3-padding" style="margin-top: 25px;">
 <div class="w3-row">
  
  <div class="w3-col m2 w3-center">
   <br>
  </div>
  
 
         
    <!-- File List Container -->
 <div class="w3-col m8 w3-container">
    <h2 class="w3-center">Sides</h2>
    <ul class="w3-theme-d1" id="fileList">
          <?php
        $dir = "./"; // Adjust the path as necessary
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != ".." && $file != "sides.php") {
                $relativePath = $dir . $file;
                echo "<li><a target='_blank' href='$relativePath'>$file</a></li><br>";
            }
        }
        ?>
          
      </ul>
  </div>
  
  <div class="w3-col m2 w3-center">
   <br>
  </div>
  
 </div>
</div>

	<!--Footer-->
		<div id="footer-placeholder">
		</div>
		
		<script>
		$(function(){
				$("#footer-placeholder").load("/kirk/css/footer_kirk.html");
		});
		</script>
		<!--end of Footer-->

<script>
  // Navigation Bar
 function myFunction() {
  var x = document.getElementById("smallBar");
  if (x.className.indexOf("w3-show") == -1) {
    x.className += " w3-show";
  } else { 
    x.className = x.className.replace(" w3-show", "");
  }
}
// End of Navigation Bar
</script>

</body>
</html>