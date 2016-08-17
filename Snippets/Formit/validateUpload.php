<?php
if($_FILES["fileToUpload"]["name"] != "") {
error_reporting(E_ERROR | E_PARSE);
ini_set('memory_limit', '256M');

    //WE NEED TO STRIP ANY WHITE SPACES, IMAGE WILL NOT DISPLAY WITH WHITE SPACES
    $updatedImage = str_replace(' ', '_', $_FILES["fileToUpload"]["name"]); 

    $target_file = "assets/images/profile/" . basename($updatedImage);
    
    $uploadOk = $scriptProperties['param'];
    $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

    // Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    
    if($check !== false) {
        //return "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        return "File is not an image. ". $target_file;
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        return "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    $fileSize = $_FILES["fileToUpload"]["size"];
    if ($_FILES["fileToUpload"]["size"] > 900000000) {
        return "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "JPG" && $imageFileType != "jpeg" && $imageFileType != "JPEG"
    && $imageFileType != "gif" && $imageFileType != "GIF" && $imageFileType != "PNG" && $imageFileType != "PNG") {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        return "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        $dataURL = $_POST["imageData"];
        $dataURL = str_replace('data:image/png;base64,', '', $dataURL);
        $dataURL = str_replace(' ', '+', $dataURL);
        $image = base64_decode($dataURL);
        
        if (file_put_contents($target_file, $image)) {
            $photo_cookie_name = "userPhotoCookie";
            $photo_cookie_value = $target_file;
            session_start();    
            setcookie($photo_cookie_name, $photo_cookie_value, time() + (86400 * 1), "/family-tree/modx/"); // 86400 = 1 day
            return true;
        } else {
            return "Sorry, there was an error uploading your file.". $target_file;
        }
        
        
    }
}
