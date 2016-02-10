


<?php
include 'vendor/autoload.php';
use Aws\Resource\Aws;

use Aws\S3\S3Client;

// Instantiate an Amazon S3 client.
$s3 = new S3Client([
    'version' => 'latest', 
    'region' => 'us-west-2', 
    'http' => ['verify' => false], 
    'credentials' => array(
        'key' => 'AKIAIS7A7YPIMGFAUTAA', 
        'secret' => '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'
)]);
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {   
        try {// Storing user images on a AWS S3 for permanent storage.
            $s3 -> putObject(['Bucket' => 'mohammad786', 
            'Key' => 'uploads/' . basename($_FILES["fileToUpload"]["name"]), 
            'Body' => fopen($target_file, 'r'), 'ACL' => 'public-read', ]);
        } catch (Aws\Exception\S3Exception $e) {
            echo "There was an error uploading the file to S3.\n";
            die();
        }
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?> 
