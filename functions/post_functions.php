<?php
function getAllPosts($pdo) {
    $stmt = $pdo->query("
        SELECT p.*, u.username 
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchPosts($pdo, $search) {
    if (strpos($search, '#') === 0) {
        $hashtag = substr($search, 1);
        $stmt = $pdo->prepare("
            SELECT p.*, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.hashtags LIKE ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute(["%$hashtag%"]);
    } else {
        $stmt = $pdo->prepare("
            SELECT p.*, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.content LIKE ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute(["%$search%"]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createPost($pdo, $user_id, $content, $media_path, $hashtags) {
    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, content, media_path, hashtags) 
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$user_id, $content, $media_path, $hashtags]);
}

function extractHashtags($content) {
    preg_match_all('/#(\w+)/', $content, $matches);
    return implode(',', array_unique($matches[1]));
}

function countPostsByHashtag($pdo, $hashtag) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM posts 
        WHERE hashtags LIKE ?
    ");
    $stmt->execute(["%$hashtag%"]);
    return $stmt->fetchColumn();
}

function formatPostContent($content) {
    // Convert hashtags to links
    $content = preg_replace_callback(
        '/#(\w+)/', 
        function($matches) {
            return '<a href="/pages/feed.php?search=%23'.urlencode($matches[1]).'" class="hashtag">#'.htmlspecialchars($matches[1]).'</a>';
        },
        htmlspecialchars($content)
    );
    // Convert newlines to <br>
    return nl2br($content);
}

function deletePost($pdo, $post_id, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    return $stmt->execute([$post_id, $user_id]);
}
?>