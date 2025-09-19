<?php

require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Category extends Database {
    /**
     * Creates a new category.
     * @param string $name The category name.
     * @param string $description The category description.
     * @return int The ID of the newly created category.
     */
    public function createCategory($name, $description = '') {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $this->executeNonQuery($sql, [$name, $description]);
        return $this->lastInsertId();
    }

    /**
     * Retrieves categories from the database.
     * @param int|null $id The category ID to retrieve, or null for all categories.
     * @return array
     */
    public function getCategories($id = null) {
        if ($id) {
            $sql = "SELECT * FROM categories WHERE category_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->executeQuery($sql);
    }

    /**
     * Updates a category.
     * @param int $id The category ID to update.
     * @param string $name The new name.
     * @param string $description The new description.
     * @return int The number of affected rows.
     */
    public function updateCategory($id, $name, $description = '') {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$name, $description, $id]);
    }

    /**
     * Deletes a category.
     * @param int $id The category ID to delete.
     * @return int The number of affected rows.
     */
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    /**
     * Gets category name by ID.
     * @param int $id The category ID.
     * @return string|null The category name or null if not found.
     */
    public function getCategoryName($id) {
        $sql = "SELECT name FROM categories WHERE category_id = ?";
        $result = $this->executeQuerySingle($sql, [$id]);
        return $result ? $result['name'] : null;
    }
}
?>
