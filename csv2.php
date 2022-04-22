<?php

include('config/connection.php');
$csv_file="";
$uploadOk="";
$error=['filetype'=>'','upload'=>'','moveupload'=>null];


$path    = 'uploads';
$files = array_diff(scandir($path), array('.', '..'));

/*The "Recent" section uses the files in the folder uploads and list them out
we use scandir() to give the path of the folder and to remove the dots from the array 
we use array_diff()
$path    = 'uploads';
$files = array_diff(scandir($path), array('.', '..'));*/


// $csv_data = "SHOW TABLES from csv_database";
// $result=mysqli_query($conn,$csv_data);
// $result = mysqli_fetch_all($result,MYSQLI_ASSOC); 
// var_dump($result);


if(isset($_POST['submit']))
{
  $csv_file=$_FILES['csvupload']['name'];
  $target_dir = "uploads/";
  $target_file = $target_dir . basename($csv_file);
  $uploadOk = 1;
  $filetype = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
  
  if( $filetype != "csv") {
    $error=["Sorry, only CSV files are allowed."];
   
    // echo "Sorry, only CSV files are allowed.";
    $uploadOk = 0;
  }

  if($uploadOk==0)
  {
    $error = array_pad($error, 2, "Sorry, your file was not uploaded.");
  
    // echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file

  }
else {
  if (move_uploaded_file($_FILES["csvupload"]["tmp_name"], $target_file)) {
    

   
  } else {
    $uploadOk = 0;
    $error = array_pad($error , 3, "Sorry, there was an error uploading your file.");
   
  
  
  }
}

}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles1.css">
    <script src="https://kit.fontawesome.com/b6920f1e00.js" crossorigin="anonymous"></script>
    <title>CSV Viewer</title>
   

  </head>
<body>

  <section>
    <div class="main">
  <div class="center-content"> 
  <h1>Change The Way You Organize</h1>
  <h5>Upload Your CVS File</h3>

    <form action="csv2.php" method="post" enctype="multipart/form-data">
    <label for="csvupload">
      <div class="btn"><i class="fas fa-file-upload fa-2x"></i><span>Choose file</span></div>
    </label>
     <input type="file"  id="csvupload"  name="csvupload" style="display:none" accept="csv/*" />
     <button type="submit" name="submit" style="display:none"></button>
     
     

    </form>

<?php if($uploadOk==1) :?>
  <div class="btn-holder" >
  <div class="status">
    <div><i class="fas fa-exclamation-circle fa-2x"></i></div>
    <div class = " container_status">  
    <div class="status_results"><?= "File Name: ". $csv_file ?></div>
    <div class="status_results" >YOUR CSV FILE IS UPLOADED SUCCESSFULLY </div>
    </div>
  </div>

  <a class="btn-anchor" href="csv_view.php?id=<?php echo $csv_file?>"><button class="button-30" type="button" name="submit2">Continue</button></a >
</div>
</div>
   <?php endif ?>

   <?php if($uploadOk==0) :?>
  <div class="status <?="statusred"?>">
    <div><i class="fas fa-exclamation-circle fa-2x"></i></div>
    <div class = " container_status">  
    <div class="status_results"><?= "File Name: ". $csv_file ?></div>
    <div class="status_results" >

<?php foreach($error as $errors) :?>
  <ul >
  <li style="text-align:left" ><?=$errors ?></li>
  </ul>

<?php endforeach ?>
    </div>
    </div>
  </div>
</div>
<?php endif ?>
 


 </div>
 <div class="recent">
  <h4>Recent</h4>
 
    <?php foreach($files as $count) :?>
      <a href="csv_view.php?id=<?=$count?>"> <div class="recent_files"> <?= $count ?> </div></a >
     <?php endforeach ?>

 </div>
 </div>
 </section>


<script>
  document.querySelector(".btn").addEventListener("click", ()=>{
    document.querySelector("#csvupload").addEventListener("change",()=>{
      document.querySelector("form").submit.click()
    })
     
      
    
   
  })

  
</script>


</body>
</html>