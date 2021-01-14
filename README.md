# LearnBoost
a 
<?php session_start(); ?>
<?php include('../db/connect.php'); ?>
<?php include('../helpers/function.php'); ?>

<?php
	if(isset($_POST['course-add'])){
		$userId=$_POST['userId'];
		$title=$_POST['title'];
		$name=$_POST['name'];
		$tagId=(int)$_POST['tag'];
		$capacity=$_POST['capacity'];

		addCourse($userId, $title, $name, $capacity, $tagId);
		$_SESSION['courseAddSuccess']="Successfully course added!";
		redirect_to('../users/classes.php');
	}
?>

<!-- ------------------- Delete Class -------------------- -->
<?php
	if(isset($_POST['deleteClass'])){
		$cid=(int)$_POST['classId'];
		$uid=(int)$_POST['facId'];

		$query2="DELETE FROM class_posts WHERE classId='{$cid}'";
		$res2=mysqli_query($connection, $query2);

		$query3="DELETE FROM class_materials WHERE classId='{$cid}'";
		$res3=mysqli_query($connection, $query3);

		$query4="DELETE FROM mark_dis WHERE classId='{$cid}'";
		$res4=mysqli_query($connection, $query4);

		$query5="SELECT id FROM enrollments WHERE classId='{$cid}'";
		$res5=mysqli_query($connection, $query5);

		while($row=mysqli_fetch_array($res5)){
			$eid=(int)$row['id'];
			$query6="DELETE FROM exam_marks WHERE enrollmentId='{$eid}'";
			$res6=mysqli_query($connection, $query6);
		}

		$query1="DELETE FROM enrollments WHERE classId='{$cid}'";
		$res1=mysqli_query($connection, $query1);

		$query7="DELETE FROM classes WHERE id='{$cid}'";
		$res7=mysqli_query($connection, $query7);

		redirect_to('../users/classes.php');
	}
?>

<?php include('../db/close.php'); ?>
