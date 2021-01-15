<?php session_start(); ?>
<?php include('../db/connect.php'); ?>
<?php include('../helpers/function.php'); ?>

<?php
	if(isset($_POST['mark-distribution'])){
		$classId=$_POST['class-id'];
		$attendance=(int)$_POST['attendance'];
		$quizzes=(int)$_POST['quizzes'];
		$midterms=(int)$_POST['midterms'];
		$hws=(int)$_POST['hws'];
		$final=(int)$_POST['final'];

		$total=$attendance+$quizzes+$midterms+$hws+$final;
		
		if($total!=100){
			$_SESSION['markDistributionFailed']="Total marks should be equivalent to <b>100</b>";
			redirect_to('../users/classroom.php?class_id='.$classId);
		}else{
			$query="INSERT INTO mark_dis(classId, attendance, quiz, midterm, hw, final) VALUES('{$classId}', '{$attendance}', '{$quizzes}', '{$midterms}', '{$hws}', '{$final}')";
			if(mysqli_query($connection, $query))
				$_SESSION['markDistributionSuccess']="Successfully marks distributed!";
			redirect_to('../users/classroom.php?class_id='.$classId);
		}
	}
?>

<?php
	if(isset($_POST['mark-distribution-update'])){
		$classId=$_POST['class-id'];
		$attendance=(int)$_POST['attendance'];
		$quizzes=(int)$_POST['quizzes'];
		$midterms=(int)$_POST['midterms'];
		$hws=(int)$_POST['hws'];
		$final=(int)$_POST['final'];

		$total=$attendance+$quizzes+$midterms+$hws+$final;
		
		if($total!=100){
			$_SESSION['markDistributionFailed']="Total marks should be equivalent to <b>100</b>";
			redirect_to('../users/classroom.php?class_id='.$classId);
		}else{
			$query="UPDATE mark_dis SET classId='{$classId}', attendance='{$attendance}', quiz='{$quizzes}', midterm='{$midterms}', hw='{$hws}', final='{$final}' WHERE classId='{$classId}'";
			if(mysqli_query($connection, $query))
				$_SESSION['markDistributionSuccess']="Successfully marks distribution updated!";
			redirect_to('../users/classroom.php?class_id='.$classId);
		}
	}
?>



<!-- ---------------------------------- Course material upload --------------------------------- -->
<?php
	if(isset($_POST['upload-material'])){
		$classId=(int)$_POST['classId'];
		$fileName=$_FILES['file']['name'];
		$tmpFile=$_FILES['file']['tmp_name'];

		$extension='.'.pathinfo($fileName, PATHINFO_EXTENSION);
		$file=explode($extension, $fileName)[0];

		$file=preg_replace('/[ ]+/', '_', $file);
		$file=preg_replace('/[-]+/', '_', $file);
		$file=preg_replace('/[^A-Za-z0-9_\-]/', '', $file);
		$file=preg_replace('/[_]+/', '_', $file);

		$file='class'.$classId.'-'.$file.$extension;
		$text=mysqli_real_escape_string($connection, $_POST['text']);

		$check="SELECT id FROM class_materials WHERE file='{$file}'";
		$resC=mysqli_query($connection, $check);

		if(mysqli_num_rows($resC)>0){
			$_SESSION['materialExists']='It seems file <b>'.$file.'</b> already uploaded! You can do following to solve it:
					<ul>
						<li>Rename file which you want to upload.</li>
						<li>Delete file which is already uploaded.</li>
					</ul>';
		}else{
			move_uploaded_file($tmpFile, '../users/files/'.$file);

			$query="INSERT INTO class_materials(classId, text, file) VALUES('{$classId}', '{$text}', '{$file}')";
			$res=mysqli_query($connection, $query);

			if($res)
				$_SESSION['materialSuccess']="File successfully upload!";
			else
				$_SESSION['materialError']="File upload error! try again.";
		}
		redirect_to('../users/classroom.php?class_id='.$classId);
	}
?>

<!-- ------------------------- Course material delete --------------------------- -->
<?php
	if(isset($_POST['delete-material'])){
		$id=(int)$_POST['id'];
		$classId=(int)$_POST['classId'];
		$file=$_POST['file'];

		$query="DELETE FROM class_materials WHERE id='{$id}'";
		mysqli_query($connection, $query);
		unlink('../users/files/'.$file);
		$_SESSION['materialDeleteSuccess']='File deleted!';
		redirect_to('../users/classroom.php?class_id='.$classId);
	}
?>

<!-- ---------------------------- Download course material ---------------------------- -->
<?php
	if(isset($_POST['download-material'])){
		$file=$_POST['file'];

		if(file_exists($file)){
			header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
		}
	}
?>


<!-- ----------------------------- Add post to class -------------------------- -->
<?php
	if(isset($_POST['class-post'])){
		$classId=(int)$_POST['classId'];
		$userId=(int)$_POST['userId'];
		$text=mysqli_real_escape_string($connection, $_POST['text']);

		$query="INSERT INTO class_posts(classId, userId, text) VALUES('{$classId}', '{$userId}', '{$text}')";
		$res=mysqli_query($connection, $query);
		if($res)
			$_SESSION['classPostAdded']='Post added successfully!';
		else
			$_SESSION['classPostAddFailed']='Error! Try again.';
		
		redirect_to('../users/classroom.php?class_id='.$classId);
	}
?>

<?php include('../db/close.php'); ?>