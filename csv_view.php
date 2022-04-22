<?php

include('config/connection.php');

if(isset($_GET['id']))
{   $result;
    $changetable=0;
    //$changetable=0 tell the if statments to hold  the orginal form of the table


    $realid=$_GET['id'];
      //e.g bikes.csv
    $id=$_GET['id'];
    
    $h=fopen("uploads/".$id, "r");
   
    //fopen() extracts the data from the csv file form folder
    
    $headings[]= fgets($h);
  
    
    // the heading contains e.g
    //array(1) { [0]=> string(68) "#,company,name,size,stocks,exchange,height,area,Storage ,net value " }

    while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
    {		
      // Read the data from a single row
      $the_big_array[] = $data;	
      //$the_big_array[] contains all the data in a multidimensional array
      //except the headings
     
    }
   
    $c=explode(',',$headings[0]);
    /*
    the heading $c is a string broken down after every "," and stored in a array

     $c=array(10) { [0]=> string(1) "#" [1]=> string(7) "company" [2]=> string(4) "name" [3]=> string(4) "size" [4]=> 
        string(6) "stocks" [5]=> string(8) "exchange" [6]=> string(6) "height" [7]=> string(4) 
        "area" [8]=> string(8) "Storage " [9]=> string(11) "net value " }
    
    <NOTE>that this heading has speical character "#" that needs to be removed</NOTE>
    */
    $s=0;
    $a=[];
    foreach($c as $counter)
    {
        
        array_push($a,"col$s");
    $s++;
    }
  
    /*
    create array of heading that contain col1... conl2 and so on that dont have special characters to place as 
    MYSQL table Column names
   
    */

    $id=preg_replace('/[^\w\s]+/',"",$id);
    
    /*
    changing csv file name to suit as a datbase name e.g bikes.csv to "bikescsv"
    used preg_replace to remove all special characters from the name
    */
 
  
    //create table in database
    // need atleast one column to create database
$create_table = "CREATE TABLE IF NOT EXISTS $id (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
   )";
   if(mysqli_query($conn,$create_table))
   {
    //create columns in sql table by using heading name in csv "$a"   
    foreach($a as $counter)
    {
      
        
       $addcolumns = "ALTER TABLE $id ADD $counter varchar(255)";
         mysqli_query($conn,$addcolumns);
     
    }
       
   }
   else {
       mysqli_error($conn);
   }


  //check if there is data or not in database 
   $query= "SELECT COUNT(*)FROM `$id`";
   $query= mysqli_query($conn,$query);
   $fetchcount= mysqli_fetch_array($query, MYSQLI_NUM);
   
   
   //if there is no data in database add data from csv 
   //if not, do nothing
   if ($fetchcount[0]<1)
   {
        //delete "id" column created during the creation of table
    $addcolumns ="ALTER TABLE $id DROP COLUMN id";
    mysqli_query($conn,$addcolumns);
// add data in respected columns in database
    foreach($the_big_array as $counter) 
  
    {
        $hello2= (implode("','",$counter));
        $query = "INSERT INTO $id VALUES('$hello2')";
        mysqli_query($conn,$query);  
    }
   }
     
}
 

// filter table
if(isset($_POST['submit'])){
     
    // chanage assoctive array into indexed array
    $convert_array= array_values($_POST);

    
     //too place the selected options in the filter table for users 
     $s=1;
     $convert_array2=$convert_array;
    $container2=[];
    foreach($convert_array as $counter) {
    
       if($counter!="")
       {
        array_push($container2,$counter);
        $s++;
       }
        else{
            array_push($container2,"col$s");
            $s++;
        }
       
       }
     

  
    $z=0;
    $sqlcolumns=$a;
    foreach($convert_array as $x)
    {
        
                      
       //if some of the filter selected are not picked then delete the empty strings 
       //from the $_POST array 
        if($convert_array[$z]=="")
        {
            //delete the unesscessary indexes 
            unset($sqlcolumns[$z]);
            unset($convert_array[$z]);
        }
        $z++;
        //convert the arrays into sql friendly string
        $sqlcolumnss=implode(',',$sqlcolumns);   
        $convert_arrays=implode("','",$convert_array);   
       
    }
    //place the sql friendly string as a query to the sql database
    $query="SELECT * FROM $id WHERE ($sqlcolumnss) = ('$convert_arrays')";
    $query= mysqli_query($conn,$query);
    $result = mysqli_fetch_all($query,MYSQLI_NUM);
   
   
    if($result)
    {
        
        $changetable=1;
        // if result is found change the contents of the table to result in the query
        // and the filter options
    }
  else{
    $changetable=2;
    // else show not found
  }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/b6920f1e00.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="csv_view_styles.css">
</head>
<body>
<section>
<div class="main">

    <nav>
    <a href="csv2.php">   
    <button class=" button btn btn-primary"><i class="fas fa-angle-left"></i>Back</button>
    </a>    
</nav>

<?php if($changetable==0) :?>
    <form action="csv_view.php?id=<?=$realid?>" method="post">
        <div class="selector">
            <div class="selector-options">
        <?php $x=0 ?>
        <?php $columnNum=0 ?>
    <?php foreach($c as $counter) : ?>
        <?php 
         $y=1;
         $container=[]; 
         $columnNum++;
         ?>
         
        <select  class="form-select" name="col<?=$columnNum?>" >
        <option value="" hidden selected ><?="column-".$columnNum?></option>
            <?php foreach($the_big_array as $counter)  : ?>
               
                   
                <?php $container=array_pad($container,$y,$counter[$x]); ?>
                
               
                <?php $y++;?>
                <?php endforeach; ?> 
               
                <?php  $container=array_unique( $container) ; ?>
                <?php foreach( $container as $counter)  : ?>
                <option ><?=$counter?></option>
                <?php endforeach; ?> 
                <?php $x++ ?>
                </select>
                <?php endforeach; ?> 
                </div>
               
             
        </div>
        <div class="options">
             <?php if($changetable==1||$changetable==2) :?>   
             <button class="btn btn-primary" ><i class="fas fa-undo fa-2x"></i></button>
              <?php endif ?>
              <button class="btn btn-success" type="submit" name="submit">Filter</button>
             
              </div> 
    </form>
 
    <?php endif ?>


    <?php if($changetable==1) :?>
    <form action="csv_view.php?id=<?=$realid?>" method="post">
        <div class="selector">
            <div class="selector-options">
        <?php $x=0 ?>
        <?php $columnNum=0 ?>
          <?php foreach($c as $counter) : ?>
               <?php 
               $y=1;
               $container=[]; 
                $columnNum++;
               ?>
         
         <select  class="form-select" name="col<?=$columnNum?>" >
         <?php if($convert_array2[$x]!="")  :?>
        <option value="<?=$container2[$x]?>" hidden selected ><?=$container2[$x]?></option>
        <?php endif ?>
        <?php if($convert_array2[$x]=="")  :?>
        <option value="" hidden selected ><?="column-".$columnNum?></option>
        <?php endif ?>
             
             
             
             <?php foreach($result as $counter)  : ?>               
              <?php $container=array_pad($container,$y,$counter[$x]); ?>              
                 <?php $y++;?>
                 <?php endforeach; ?> 
               
                <?php  $container=array_unique( $container) ; ?>

                <?php foreach( $container as $counter)  : ?>
                <option ><?=$counter?></option>
                <?php endforeach; ?> 

                <?php $x++ ?>

                </select>
                <?php endforeach; ?> 
             </div>      
        </div>



        <div class="options">
             <?php if($changetable==1||$changetable==2) :?>   
             <button class="btn btn-primary" ><i class="fas fa-undo fa-2x"></i></button>
              <?php endif ?>
              <button class="btn btn-success" type="submit" name="submit">Filter</button>
             
              </div> 
    </form>
 
    <?php endif ?>


    <?php if($changetable==0) :?>

    <table class="table table-striped table-hover table1 " >
    <thead >
    <tr>
    <?php foreach($c as $counter) : ?>
        <th scope="col"><?= $counter ?></th>
        
        <?php endforeach; ?> 
      </tr>
    </thead>
    <tbody>
  
      <?php foreach($the_big_array  as $counter) : ?>
        <?php $x=0 ?>
        <tr  class=datatable id=datatable>
       
        <?php foreach($counter as $counters) : ?>
        <td data-label="<?=$c[$x++]?>" ><span><?=$counters?></span></td>
        <?php endforeach; ?> 
      
        </tr>
        
        <?php endforeach; ?> 
        </tbody>
    </table>
    <?php endif ?>
   
  
    <?php if($changetable==1) :?>
             
    <table class="table table-striped table-hover " >
       <thead>
         <tr>
             <?php foreach($c as $counter) : ?>
             <th scope="col"><?= $counter ?></th>
             <?php endforeach; ?> 
         </tr>
       </thead >
       <tbody>
          <?php foreach($result as $counter) : ?>
               <?php $x=0 ?>
               <tr  class=datatable id=datatable>
          
                  <?php foreach($counter as $counters) : ?>
                 <td data-label="<?=$c[$x++]?>" ><span><?= $counters ?></td></span></td>
                 <?php endforeach; ?> 
               </tr>
         <?php endforeach; ?> 
        </tbody>
    </table>

    <?php endif ?>

     <?php if( $changetable==2) :?>
           
       <h1>No Match Found</h1>
     
      <?php endif ?>


 </div>
 </section>





 <script>
     //change font size of table to make it more readable;
     var d = document.getElementsByTagName("table")[0];
     var c = document.getElementsByClassName("main")[0];
     var z=100;
    
        while(d.offsetWidth > c.offsetWidth)
    {
        d.style.fontSize=z+"%"
      console.log(z);
      if(z<60){
          break;
      }
      z=z-4;

    } 
    


    
    console.log("no overflow");
     
    var select = document.querySelectorAll(".form-select")
    select.forEach(element => 
    {

    element.addEventListener('change',()=>{
      element.classList.add("selected")
})
})

 </script>
</body>
</html>