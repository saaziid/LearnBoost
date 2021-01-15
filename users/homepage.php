<?php session_start(); ?>
<?php include('../db/connect.php'); ?>
<?php include('../partials/s_header.php'); ?>
<?php require_once('../helpers/function.php'); ?>

<?php if(!isset($_SESSION['userid'], $_SESSION['username']))
	redirect_to('../login.php');
?>

<!-- ---------------- Get user for specific id ----------------- -->
<?php
	$student=getUser($_SESSION['userid']); /*!< get the student user id .*/

	$studentId=$student['id']; /*!< store the student user id .*/
	$username=$student['username']; /*!< store the student username.*/
	$name=$student['name']; /*!< store the student name .*/
	$type=$student['type']; /*!< store the student type .*/
	$email=$student['email']; /*!< store the student user email .*/


	$profile=[];

	$query="SELECT * FROM user_profile WHERE userId='{$studentId}'";
	$res=mysqli_query($connection, $query);
	while($row=mysqli_fetch_array($res)){
		$profile=['image'=>$row['image'], 'nsuId'=>$row['nsuId'], 'about'=>$row['about']];
	}
?>

<!-- ----------------- Pagination --------------------- -->
<?php
	if(isset($_GET["items"])){
		$page=$_GET["items"];
		$ct=$page;

		if($page=="" || $page=="1"){
			$pg=0;
			$ct=1;
		}
		else{
			$pg=($page*5)-5;
		}
	}
	else{
		$ct=1;
		$pg=0;
	}
?>

<?php include('../partials/s_nav.php'); ?>

<div class="container">
	<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;Information</div>
					<div class="panel-body">
						<div class="text-center">
						<?php if(!empty($profile) && $profile['image']!='empty'){ ?>
							<img src="images/<?php echo $profile['image']; ?>" alt="" width="200px" height="200px">
						<?php }else{ if($type=='faculty'){ ?>
							<img src="https://lh3.googleusercontent.com/-ps7fYBDY170/AAAAAAAAAAI/AAAAAAAAAB4/O7ry2Z2mruA/photo.jpg" alt="" width="200px" height="200px">
						<?php }else{ ?>
							<img src="https://www.meine-erste-homepage.com/avatars24.png" alt="" width="200px" height="200px">
						<?php }} ?>
						</div>
						
						<br><br>

						<div class="list-group">
							<button type="button" class="list-group-item">Name: <b><?php echo ucwords($name); ?></b></button>
							<?php if(!empty($profile) && $profile['nsuId']!='empty'){ ?>
							<button type="button" class="list-group-item">NSU ID: <b><?php echo $profile['nsuId']; ?></b></button>
							<?php } ?>
							<button type="button" class="list-group-item">Username: <b><?php echo $username; ?></b></button>
							<button type="button" class="list-group-item">Email: <b><?php echo $email; ?></b></button>
							<button type="button" class="list-group-item">Type: <b><?php echo $type; ?></b></button>

							<?php if(!empty($profile) && $profile['about']!=''){ ?>
							<button type="button" class="list-group-item list-group-item-warning">
								<b><?php echo nl2br($profile['about']); ?></b>
							</button>
							<?php } ?>
						</div>
					</div>
					<div class="panel-footer">
						<a href="userprofile.php?userid=<?php echo $studentId; ?>&user=<?php echo profileGetParamName($name); ?>" class="btn btn-default">Edit</a>
					</div>
				</div>
			</div>

			<div class="col-md-8">
				<div class="np-title" style="text-align: right">
					<button class="btn btn-warning btn-block">New Post</button>
					<br>
				</div>
				<div class="panel panel-default np-area" hidden>
					<form action="../controllers/usersPost.php" method="POST">
					<div class="panel-heading"><span class="glyphicon glyphicon-th" aria-hidden="true"></span>&nbsp;&nbsp;Your Panel</div>
					<div class="panel-body">
							<div class="row">
								<div class="col-xs-12 col-md-8">
									<input type="hidden" name="userId" value="<?php echo $studentId; ?>">
									<div class="form-group">
										<input type="text" name="post-title" class="form-control" placeholder="Enter title" required>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<select name="post-tags[]" class="multi-tags form-control" multiple="multiple">
										<?php
											$query="SELECT * FROM tags ORDER BY tag ASC";
											$res=mysqli_query($connection, $query);
											while($row=mysqli_fetch_array($res)){
										?>
										<option value="<?php echo $row['id']; ?>"><?php echo strtoupper($row['tag']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<textarea name="post-content" class="form-control" cols="30" rows="2" placeholder="Enter post" required style="resize: none"></textarea>
							</div>
					</div>
					<div class="panel-footer">
						<button type="submit" class="btn btn-default btn-primary" name="student-post">Create new post</button>
					</div>
					</form>
				</div>

				<?php
					if(isset($_SESSION['postDeleteSuccess'])){
				?>
				<div class="alert alert-warning alert-dismissible" role="alert" style="transition: all .3s">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <?php echo $_SESSION['postDeleteSuccess']; ?>
				</div>
				<?php
						unset($_SESSION['postDeleteSuccess']);
					}if(isset($_SESSION['postAddSuccess'])){
				?>
				<div class="alert alert-info alert-dismissible" role="alert" style="transition: all .3s">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <?php echo $_SESSION['postAddSuccess']; ?>
				</div>
				<?php
						unset($_SESSION['postAddSuccess']);
					}
				?>

				<div class="post">
					<?php
						$query="SELECT * FROM posts ORDER BY id DESC LIMIT {$pg}, 5";
						$res=mysqli_query($connection, $query);
						while($row=mysqli_fetch_array($res)){
							$query1="SELECT tags.* FROM tags JOIN posttags ON tags.id=posttags.tagId WHERE posttags.postId={$row['id']}";
							$res1=mysqli_query($connection, $query1);
					?>
					<div class="user-post">
						<h3><?php echo $row['title']; ?></h3>
						<p><?php echo nl2br($row['content']); ?></p>
						<hr>
						<div class="user">
							<div class="row">
							<div class="col-md-4 user-info">
								<p><?php echo ucfirst(getUser($row['userId'])['type']); ?>: <a href="userprofile.php?userid=<?php echo $row['userId']; ?>&user=<?php echo profileGetParamName(getUser($row['userId'])['name']); ?>"><?php echo ucwords(getUser($row['userId'])['name']); ?></a></p>
							</div>
							<div class="col-md-4 text-right">
							<?php
								while($row1=mysqli_fetch_array($res1)){
							?>
								<span class="label label-default"><?php echo $row1['tag']; ?></span>
							<?php
								}
							?>
							</div>
							<div class="col-md-4 text-right">
								<button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#postEditModal<?php echo $row['id']; ?>">View</button>
								<?php
									if($studentId==$row['userId']){
								?>
								<form action="../controllers/usersPost.php" method="POST" style="display: inline">
									<input type="hidden" name="post-id" value="<?php echo $row['id']; ?>">
									<button type="submit" name="post-delete" class="btn btn-warning btn-xs" onclick="return confirm('Deleting Post: Are you sure?')">Delete</button>
								</form>
								<?php
									}
								?>
								<button class="btn btn-info btn-xs commentVisible" data-target=".commentArea<?php echo $row['id']; ?>">Comments</button>
							</div>
							<div class="col-md-12 commentArea<?php echo $row['id']; ?>" hidden>
								<hr>
								<!-- --------------------- Comment section -------------------- -->
								<div class="comment-section">
									<div class="row">
										<div class="col-md-10">
											<input type="hidden" class="cUserId<?php echo $row['id']; ?>" name="post-id" value="<?php echo $studentId; ?>">
											<input type="hidden" class="cPostId<?php echo $row['id']; ?>" name="post-id" value="<?php echo $row['id']; ?>">
											<textarea name="user-comment" class="form-control cUserComment<?php echo $row['id']; ?>" cols="30" rows="2" placeholder="Enter comment" style="resize: none"></textarea>
											<p class="comment-error<?php echo $row['id']; ?>" hidden style="color: red">** Minimum 5 characters!</p>
										</div>
										<div class="col-md-2">
											<button name="post-comment" class="btn btn-info postComment<?php echo $row['id']; ?>">Comment</button>
										</div>
									</div>
								</div>

								<div class="user-comments">
									<div class="row">
									<div class="col-md-12 allComments<?php echo $row['id']; ?>">

									</div>
									</div>
								</div>
							</div>
							</div>
						</div>
					</div>
					<!-- ------------------------------ Post edit modal ------------------------------ -->
					<div class="modal" id="postEditModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title" id="myModalLabel"><?php echo $row['title']; ?></h4>
					      </div>
					      <div class="modal-body">
					        <?php echo nl2br($row['content']); ?>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      </div>
					    </div>
					  </div>
					</div>

					<?php } ?>
				</div>
				
				<nav aria-label="Page navigation">
					<ul class="pagination">
						<?php
							$i=for_pagination('posts');
							$act=$pg+1;
							for($b=1; $b<=$i; $b++){
						?>

				    	<li><a href="homepage.php?items=<?php echo $b; ?>">
				    		<?php echo $b; ?>
				    	</a></li>

				    	<?php } ?>

				  	</ul>
				</nav>

			</div>
		</div>
	</div>
	</div>
</div>

<?php include('../partials/s_footer.php'); ?>
<?php include('../db/close.php'); ?>
