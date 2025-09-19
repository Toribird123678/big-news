<?php  
require_once '../classloader.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = htmlspecialchars(trim($_POST['username']));
	$email = htmlspecialchars(trim($_POST['email']));
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			if (!$userObj->usernameExists($username)) {

				if ($userObj->registerUser($username, $email, $password)) {
					header("Location: ../login.php");
				}

				else {
					$_SESSION['message'] = "An error occured with the query!";
					$_SESSION['status'] = '400';
					header("Location: ../register.php");
				}
			}

			else {
				$_SESSION['message'] = $username . " as username is already taken";
				$_SESSION['status'] = '400';
				header("Location: ../register.php");
			}
		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}
	}
	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$email = trim($_POST['email']);
	$password = trim($_POST['password']);

	if (!empty($email) && !empty($password)) {

		if ($userObj->loginUser($email, $password)) {
			header("Location: ../index.php");
		}
		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../login.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}

if (isset($_POST['insertArticleBtn'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$author_id = $_SESSION['user_id'];
	$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
	$newId = $articleObj->createArticle($title, $description, $author_id, $category_id);
	if ($newId) {
		// Handle optional image upload
		if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
			$uploadDir = dirname(__DIR__) . '/uploads';
			if (!is_dir($uploadDir)) {
				@mkdir($uploadDir, 0777, true);
			}
			$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$basename = 'article_' . $newId . '_' . time() . '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext);
			$destFs = $uploadDir . '/' . $basename;
			if (move_uploaded_file($_FILES['image']['tmp_name'], $destFs)) {
				// Web path relative to writer/
				$webPath = 'uploads/' . $basename;
				$articleObj->updateArticleImage($newId, $webPath);
			}
		}
		header("Location: ../index.php");
	}
}

if (isset($_POST['editArticleBtn'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$article_id = $_POST['article_id'];
	$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
	// Enforce permission: only author or approved editor
	if ($articleObj->canUserEditArticle($article_id, $_SESSION['user_id']) && $articleObj->updateArticle($article_id, $title, $description, $category_id)) {
		// Optional image replace
		if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
			$uploadDir = dirname(__DIR__) . '/uploads';
			if (!is_dir($uploadDir)) {
				@mkdir($uploadDir, 0777, true);
			}
			$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$basename = 'article_' . $article_id . '_' . time() . '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext);
			$destFs = $uploadDir . '/' . $basename;
			if (move_uploaded_file($_FILES['image']['tmp_name'], $destFs)) {
				$webPath = 'uploads/' . $basename;
				$articleObj->updateArticleImage($article_id, $webPath);
			}
		}
		header("Location: ../articles_submitted.php");
	}
    else {
        $_SESSION['message'] = "You do not have permission to edit this article.";
        $_SESSION['status'] = '400';
        header("Location: ../index.php");
    }
}

if (isset($_POST['deleteArticleBtn'])) {
	$article_id = $_POST['article_id'];
	echo $articleObj->deleteArticle($article_id);
}

// Handle edit request from one writer to another writer's article
if (isset($_POST['requestEditBtn'])) {
	$article_id = (int)$_POST['article_id'];
	$requester_id = (int)$_SESSION['user_id'];

	$result = $articleObj->requestEditForArticle($article_id, $requester_id);

	if ($result === 'created') {
		$_SESSION['message'] = "Edit request sent to the article author.";
		$_SESSION['status'] = '200';
	} elseif ($result === 'duplicate') {
		$_SESSION['message'] = "You already have a pending request for this article.";
		$_SESSION['status'] = '400';
	} elseif ($result === 'own_article') {
		$_SESSION['message'] = "You cannot request to edit your own article.";
		$_SESSION['status'] = '400';
	} else {
		$_SESSION['message'] = "Article not found.";
		$_SESSION['status'] = '400';
	}
	header("Location: ../index.php");
}

// Approve/reject edit requests (only article author)
if (isset($_POST['approveEditRequestBtn']) || isset($_POST['rejectEditRequestBtn'])) {
	$request_id = (int)$_POST['request_id'];
	$author_id = (int)$_SESSION['user_id'];
	$newStatus = isset($_POST['approveEditRequestBtn']) ? 'accepted' : 'rejected';

	$updated = $articleObj->updateEditRequestStatus($request_id, $author_id, $newStatus);
	if ($updated) {
		$_SESSION['message'] = $newStatus == 'accepted' ? 'Request approved.' : 'Request rejected.';
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = 'Unable to update request.';
		$_SESSION['status'] = '400';
	}
	header("Location: ../articles_submitted.php");
}