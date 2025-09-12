<?php
require_once 'classloader.php';

echo "<h2>Admin Login Test</h2>";

// Test database connection
try {
    $db = new Database();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if there are any admin users
    $sql = "SELECT COUNT(*) as admin_count FROM school_publication_users WHERE is_admin = 1";
    $result = $db->executeQuerySingle($sql);
    echo "<p>Admin users in database: " . $result['admin_count'] . "</p>";
    
    // List all users
    $sql = "SELECT user_id, username, email, is_admin FROM school_publication_users";
    $users = $db->executeQuery($sql);
    echo "<h3>All Users:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Is Admin</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user['user_id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['email'] . "</td>";
        echo "<td>" . ($user['is_admin'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test session
echo "<h3>Session Test:</h3>";
echo "<p>Session status: " . session_status() . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

// Test User class
echo "<h3>User Class Test:</h3>";
try {
    $userObj = new User();
    echo "<p style='color: green;'>✓ User class instantiated successfully</p>";
    echo "<p>Is logged in: " . ($userObj->isLoggedIn() ? 'Yes' : 'No') . "</p>";
    echo "<p>Is admin: " . ($userObj->isAdmin() ? 'Yes' : 'No') . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ User class error: " . $e->getMessage() . "</p>";
}
?>
