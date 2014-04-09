<?php // You need to add server side validation and better error handling here
include('SimpleImage.php');
$data = array();


if(isset($_GET['files']))
{
	$error = false;
	$files = array();
	$id = uniqid();

	$uploaddir =  $_SERVER['DOCUMENT_ROOT'] . "/common/badges/";
	foreach($_FILES as $file)
	{
		if(move_uploaded_file($file['tmp_name'], $uploaddir .basename(  $id . $file['name'])))
		{
			$files[] = $uploaddir . $id .$file['name'];
			$file_location = $uploaddir; # Image folder Path
			$image = new SimpleImage();
			$image->load($file_location. $id . $file['name']);
			$image->resizeToHeight(300);
			$image->save('../common/badges/'. $id .$file['name']);
		}
		else
		{
		    $error = true;
		}
	}
	$data = ($error) ? array('error' => 'There was an error uploading your files') : array('files' => $files);
}
else
{
	$data = array('success' => 'Form was submitted', 'formData' => $_POST);
}

echo json_encode($data);

?>
