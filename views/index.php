<?php
	include('./partials/header.php');

  require_once('../Connection.php');

  

  function getArticles(){
    $con = new Connection();
    $con = $con->openConnection();

    $sql = 'SELECT * FROM articles;';

    $stmt = $con->query($sql);
    $articles = $stmt->fetchAll();

    $con = null;

    return $articles;
  }

  function deleteArticle($id){
    $con = new Connection();
    $con = $con->openConnection();

    $sql = 'DELETE FROM articles WHERE id = ?;';

    $stmt = $con->prepare($sql);

    $res = $stmt->execute([$id]);

    $con = null;

  }

  function updateArticle($id, $article){
    $con = new Connection();
    $con = $con->openConnection();

    $sql = 'UPDATE articles SET title = ?, body = ? WHERE id = ?;';

    $stmt = $con->prepare($sql);

    $res = $stmt->execute([$article['title'], $article['body'], $id]);

    $con = null;

  }

  $articles = getArticles();

  // var_dump($articles);

  if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    
    deleteArticle($_GET['id']);

  }

  if($_SERVER['REQUEST_METHOD'] === 'PUT'){

    //Receive the RAW post data.
    $content = trim(file_get_contents("php://input"));

    $decoded = json_decode($content, true);

    var_dump($decoded);

    updateArticle($_GET['id'], $decoded);

  }


?>

<main role="main">

  <section class="jumbotron text-center">
    <div class="container">
      <h1>Vanilla PHP Blog</h1>
      <p class="lead text-muted">Something short and leading about the collection below—its contents, the creator, etc. Make it short and sweet, but not too short so folks don’t simply skip over it entirely.</p>
      <p>
        <a href="/blogs/create.php" class="btn btn-primary my-2">New Article</a>
        <a href="#" class="btn btn-secondary my-2">Latest Articles</a>
      </p>
    </div>
  </section>

  <div class="album py-5 bg-light">
    <div class="container">

      <div class="row">

        <?php foreach ($articles as $article) { ?>
        
            <div class="col-md-4">
              <div class="card mb-4 shadow-sm">
                <div class="card-body">
                  <div class="card-title"><h3><?php echo $article['title']; ?></h3></div>
                  <p class="card-text"><?php echo substr($article['body'], 0, 200); ?></p>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-secondary" <?php echo 'onclick="deleteArticle(' . $article['id'] . ')"'; ?> >Delete</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary" <?php echo 'onclick=\'showArticle(' . json_encode($article) . ')\''; ?> >Edit</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

        <?php } ?>

        <!-- Modal -->
      <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <form>
              <div class="modal-header">
                <h5 class="modal-title">Edit Article <span id="articleId"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                <label for="body">Title</label>
              <input type='text' name="title" id='title' class="form-control" />
            </div>

            <div class="form-group">
                <label for="body">Content</label>
                <textarea class="form-control" name="body" id="body" rows="6"></textarea>
              </div>


              <div class="alert alert-danger" role="alert" id="error" style="display: none;"></div>
              <div class="alert alert-success" role="alert" id="success" style="display: none;">Articles has been successfully updated!</div>

               
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary mb-2" id='update'>Submit</button>
              </div>
            </form>
            
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>

</main>

<script type="text/javascript">

  function showArticle(article){

    $('#staticBackdrop').modal('show');

    let error = document.getElementById('error');
    let success = document.getElementById('success');

    let idEl = document.getElementById('articleId');
    let titleInput = document.getElementById('title');
    let bodyInput = document.getElementById('body');

    idEl.innerHTML = article.id;
    titleInput.value = article.title;
    bodyInput.value = article.body;

    document.getElementById('update').addEventListener('click', (e) => {
      e.preventDefault();

      let title = titleInput.value;
      let body = bodyInput.value;

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
        fetch("/?id=" + article.id, {
            method: "PUT",
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
        .finally(() => {
          location.reload();
        })
      }
    })

  }

  function deleteArticle(id){
    if(confirm('Do you want to proceed and delete this article?'))
      fetch("/?id=" + id, {
        method: "DELETE",
        mode: "same-origin",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/json"
        },})
      .then(() => location.reload())

  }
</script>

<?php
	include('./partials/footer.php');
?>