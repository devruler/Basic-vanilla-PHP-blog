<?php

include('../partials/header.php');

require_once('../../Connection.php');

if($_SERVER['REQUEST_METHOD'] === 'POST'){

	$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

	if ($contentType === "application/json") {

		//Receive the RAW post data.
		$content = trim(file_get_contents("php://input"));

		$decoded = json_decode($content, true);

	  	$con = new Connection();
		$con = $con->openConnection();

		$checkBlogTable = 'INSERT INTO articles(title, body) VALUES (?, ?);';

		$stmt = $con->prepare($checkBlogTable);
		$data = $stmt->execute([$decoded['title'], $decoded['body']]);

		var_dump($data);

		header('Content-type: application/json');
		echo json_encode( $data );

		$data = null;

	}

	

}

?>

	<main class="my-5">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<form>

						<div class="form-group">
						    <label for="body">Title</label>
							<input type='text' name="title" id='title' class="form-control" />
						</div>

						<div class="form-group">
						    <label for="body">Content</label>
						    <textarea class="form-control" name="body" id="body" rows="6"></textarea>
					  	</div>


					  	<div class="alert alert-danger" role="alert" id="error" style="display: none;"></div>
					  	<div class="alert alert-success" role="alert" id="success" style="display: none;">Articles has been successfully created!</div>

					  	 <button type="submit" class="btn btn-primary mb-2" id='submit'>Submit</button>

					</form>
				</div>
			</div>
		</div>
	</main>

	<script type="text/javascript">
		let submitButton = document.getElementById('submit');
		let error = document.getElementById('error');
		let success = document.getElementById('success');
		

		submitButton.addEventListener('click', (e) => {
			e.preventDefault();

			let title = document.getElementById('title').value;
			let body = document.getElementById('body').value;

			success.style.display = 'none';

			if(title.length > 100 || title === '') {
				error.innerHTML = 'Title cannot be empty OR more than 100 characters!';
				error.style.display = 'block';
			}else if(body === ''){
				error.innerHTML = 'Body cannot be empty!';
				error.style.display = 'block';

			}else{
				error.innerHTML = '';
				error.style.display = 'none';
				fetch("/blogs/create.php", {
				    method: "POST",
				    mode: "same-origin",
				    credentials: "same-origin",
				    headers: {
				      "Content-Type": "application/json"
				    },
				    body: JSON.stringify({
				      "title": title,
				      "body": body,
				    })
				  })
				.then(() => {
					success.style.display = 'block';
				})
			}

			
			
		})
	</script>

<?php

include('../partials/footer.php');

?>