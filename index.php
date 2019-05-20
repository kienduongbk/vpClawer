<link rel="stylesheet" href="templates/css/template.css"  type="text/css" />
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<div class="container">
     <div class="row">
       <div class="col-md-6">
           <form method="post" enctype="multipart/form-data" action="#" id="file">
              <div class="form-group files">
                <label>Upload Your File </label>
                <input name="uploaded_file" type="file" class="form-control" multiple="">
              </div>
              <input type="submit" name="submit" value="Submit">
          </form>
       </div>
     </div>
</div>
<?php
if(!empty($_FILES['uploaded_file']))
{
     $path = "tmp/";
     $path = $path . basename( $_FILES['uploaded_file']['name']);
     if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $path)) {
          echo "The file ".  basename( $_FILES['uploaded_file']['name'])." has been uploaded";
          require_once dirname(__FILE__) . '/curl.php';
     } else{
        echo "There was an error uploading the file, please try again!";
     }

}