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
        // Check if user exists and is admin
        $db = new Database();
        $sql = "SELECT * FROM school_publication_users WHERE email = ? AND is_admin = 1 LIMIT 1";
        $user = $db->executeQuerySingle($sql, [$email]);

        if ($user && password_verify($password, $user['password'])) {
            // Login success: set session, redirect, etc.
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: ../index.php");
            exit;
        } else {
            $_SESSION['message'] = "Invalid email or password, or not an admin.";
            $_SESSION['status'] = "400";
            header("Location: ../login.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = "400";
        header("Location: ../login.php");
        exit;
    }
}

if (isset($_GET['logoutUserBtn'])) {
	$userObj->logout();
	header("Location: ../index.php");
}

if (isset($_POST['insertAdminArticleBtn'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$author_id = $_SESSION['user_id'];
	$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
	if ($articleObj->createArticle($title, $description, $author_id, $category_id)) {
		header("Location: ../index.php");
	}

}

if (isset($_POST['editArticleBtn'])) {
	$title = $_POST['title'];
	$description = $_POST['description'];
	$article_id = $_POST['article_id'];
	$category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
	if ($articleObj->updateArticle($article_id, $title, $description, $category_id)) {
		header("Location: ../articles_submitted.php");
	}
}

if (isset($_POST['deleteArticleBtn'])) {
	$article_id = $_POST['article_id'];
	echo $articleObj->deleteArticle($article_id);
}

if (isset($_POST['updateArticleVisibility'])) {
	$article_id = $_POST['article_id'];
	$status = $_POST['status'];
	echo $articleObj->updateArticleVisibility($article_id,$status);
}

// Category Management
if (isset($_POST['addCategoryBtn'])) {
	$name = trim($_POST['category_name']);
	$description = trim($_POST['category_description']);
	
	if (!empty($name)) {
		if ($categoryObj->createCategory($name, $description)) {
			$_SESSION['message'] = "Category added successfully!";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Error adding category!";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "Category name is required!";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_POST['updateCategoryBtn'])) {
	$id = $_POST['edit_category_id'];
	$name = trim($_POST['edit_category_name']);
	$description = trim($_POST['edit_category_description']);
	
	if (!empty($name)) {
		if ($categoryObj->updateCategory($id, $name, $description)) {
			$_SESSION['message'] = "Category updated successfully!";
			$_SESSION['status'] = '200';
		} else {
			$_SESSION['message'] = "Error updating category!";
			$_SESSION['status'] = '400';
		}
	} else {
		$_SESSION['message'] = "Category name is required!";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}

if (isset($_POST['deleteCategoryBtn'])) {
	$id = $_POST['category_id'];
	
	if ($categoryObj->deleteCategory($id)) {
		$_SESSION['message'] = "Category deleted successfully!";
		$_SESSION['status'] = '200';
	} else {
		$_SESSION['message'] = "Error deleting category!";
		$_SESSION['status'] = '400';
	}
	header("Location: ../categories.php");
}
