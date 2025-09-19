<?php  

require_once 'Database.php';
require_once 'User.php';
/**
 * Class for handling Article-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Article extends Database {
    /**
     * Creates a new article.
     * @param string $title The article title.
     * @param string $content The article content.
     * @param int $author_id The ID of the author.
     * @param int|null $category_id The ID of the category (optional).
     * @return int The ID of the newly created article.
     */
    public function createArticle($title, $content, $author_id, $category_id = null) {
        $sql = "INSERT INTO articles (title, content, author_id, category_id, is_active) VALUES (?, ?, ?, ?, 0)";
        $this->executeNonQuery($sql, [$title, $content, $author_id, $category_id]);
        return $this->lastInsertId();
    }

    /**
     * Retrieves articles from the database.
     * @param int|null $id The article ID to retrieve, or null for all articles.
     * @return array
     */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username, c.name as category_name 
                    FROM articles a 
                    LEFT JOIN school_publication_users u ON a.author_id = u.user_id 
                    LEFT JOIN categories c ON a.category_id = c.category_id 
                    WHERE a.article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT a.*, u.username, c.name as category_name 
                FROM articles a 
                LEFT JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql);
    }

    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT a.*, u.username, c.name as category_name 
                    FROM articles a 
                    LEFT JOIN school_publication_users u ON a.author_id = u.user_id 
                    LEFT JOIN categories c ON a.category_id = c.category_id 
                    WHERE a.article_id = ? AND a.is_active = 1";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT a.*, u.username, c.name as category_name 
                FROM articles a 
                LEFT JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.is_active = 1 ORDER BY a.created_at DESC";
                
        return $this->executeQuery($sql);
    }

    public function getArticlesByUserID($user_id) {
        $sql = "SELECT a.*, u.username, c.name as category_name 
                FROM articles a 
                LEFT JOIN school_publication_users u ON a.author_id = u.user_id 
                LEFT JOIN categories c ON a.category_id = c.category_id 
                WHERE a.author_id = ? ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    /**
     * Updates an article.
     * @param int $id The article ID to update.
     * @param string $title The new title.
     * @param string $content The new content.
     * @param int|null $category_id The new category ID (optional).
     * @return int The number of affected rows.
     */
    public function updateArticle($id, $title, $content, $category_id = null) {
        $sql = "UPDATE articles SET title = ?, content = ?, category_id = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$title, $content, $category_id, $id]);
    }

    /**
     * Updates the image path for an article.
     * @param int $id The article ID to update.
     * @param string $imagePath The relative web path to the stored image.
     * @return int Affected rows.
     */
    public function updateArticleImage($id, $imagePath) {
        $sql = "UPDATE articles SET image_path = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$imagePath, $id]);
    }

    /**
     * Returns articles shared (accepted requests) with a given requester user.
     * @param int $requesterId
     * @return array
     */
    public function getArticlesSharedWithUser($requesterId) {
        $sql = "SELECT a.article_id, a.title, a.content, a.image_path, a.created_at, a.author_id,
                       u.username AS author_username
                FROM edit_requests er
                JOIN articles a ON er.article_id = a.article_id
                JOIN school_publication_users u ON a.author_id = u.user_id
                WHERE er.requester_id = ? AND er.status = 'accepted'
                ORDER BY a.created_at DESC";
        return $this->executeQuery($sql, [$requesterId]);
    }
    
    /**
     * Toggles the visibility (is_active status) of an article.
     * This operation is restricted to admin users only.
     * @param int $id The article ID to update.
     * @param bool $is_active The new visibility status.
     * @return int The number of affected rows.
     */
    public function updateArticleVisibility($id, $is_active) {
        $userModel = new User();
        if (!$userModel->isAdmin()) {
            return 0;
        }
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [(int)$is_active, $id]);
    }


    /**
     * Deletes an article.
     * @param int $id The article ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    /**
     * Retrieves a single article with author info.
     * @param int $articleId
     * @return array|null
     */
    public function getArticleWithAuthor($articleId) {
        $sql = "SELECT * FROM articles WHERE article_id = ? LIMIT 1";
        return $this->executeQuerySingle($sql, [$articleId]);
    }

    /**
     * Creates an edit request from one writer to another writer's article.
     * Prevents requesting on own article and duplicate pending requests.
     * @param int $articleId
     * @param int $requesterId
     * @return string One of: 'created', 'duplicate', 'own_article', 'not_found'
     */
    public function requestEditForArticle($articleId, $requesterId) {
        $article = $this->getArticleWithAuthor($articleId);
        if (!$article) {
            return 'not_found';
        }
        if ((int)$article['author_id'] === (int)$requesterId) {
            return 'own_article';
        }
        // Check duplicate pending request
        $checkSql = "SELECT COUNT(*) AS cnt FROM edit_requests WHERE article_id = ? AND requester_id = ? AND status = 'pending'";
        $row = $this->executeQuerySingle($checkSql, [$articleId, $requesterId]);
        if ($row && (int)$row['cnt'] > 0) {
            return 'duplicate';
        }
        // Create new pending request
        $insertSql = "INSERT INTO edit_requests (article_id, requester_id, status) VALUES (?, ?, 'pending')";
        $this->executeNonQuery($insertSql, [$articleId, $requesterId]);
        return 'created';
    }

    /**
     * Returns whether a user can edit an article (author or approved editor).
     */
    public function canUserEditArticle($articleId, $userId) {
        // Author can edit
        $ownSql = "SELECT COUNT(*) AS cnt FROM articles WHERE article_id = ? AND author_id = ?";
        $own = $this->executeQuerySingle($ownSql, [$articleId, $userId]);
        if ($own && (int)$own['cnt'] > 0) {
            return true;
        }
        // Approved request exists
        $reqSql = "SELECT COUNT(*) AS cnt FROM edit_requests WHERE article_id = ? AND requester_id = ? AND status = 'accepted'";
        $row = $this->executeQuerySingle($reqSql, [$articleId, $userId]);
        return $row && (int)$row['cnt'] > 0;
    }

    /**
     * Get pending edit requests for articles authored by the given user.
     */
    public function getPendingEditRequestsForAuthor($authorId) {
        $sql = "SELECT er.request_id, er.article_id, er.requester_id, er.status, er.created_at,
                       u.username AS requester_username, a.title
                FROM edit_requests er
                JOIN articles a ON er.article_id = a.article_id
                JOIN school_publication_users u ON er.requester_id = u.user_id
                WHERE a.author_id = ? AND er.status = 'pending'
                ORDER BY er.created_at DESC";
        return $this->executeQuery($sql, [$authorId]);
    }

    /**
     * Update request status ensuring the request belongs to an article by this author.
     */
    public function updateEditRequestStatus($requestId, $authorId, $newStatus) {
        if (!in_array($newStatus, ['accepted', 'rejected'])) {
            return 0;
        }
        $sql = "UPDATE edit_requests er
                JOIN articles a ON er.article_id = a.article_id
                SET er.status = ?
                WHERE er.request_id = ? AND a.author_id = ?";
        return $this->executeNonQuery($sql, [$newStatus, $requestId, $authorId]);
    }
}
?>