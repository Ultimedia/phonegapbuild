<?php // You need to add server side validation and better error handling here

$data = array();


if(isset($_GET['files']))
{
	$error = false;
	$files = array();
	$id = uniqid();

	$uploaddir =  $_SERVER['DOCUMENT_ROOT'] . "/common/uploads/";
	foreach($_FILES as $file)
	{
		if(move_uploaded_file($file['tmp_name'], $uploaddir .basename(  $id . $file['name'])))
		{
			$files[] = $uploaddir . $id .$file['name'];
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
