<?php 

//insert posts data into database
function createPost($pdo, $user_id, $content, $media_path, $hashtags) {
    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, content, media_path, hashtags) 
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$user_id, $content, $media_path, $hashtags]);
}

//extract hashtags from post content
function extractHashtags($content) {
    preg_match_all('/#(\w+)/', $content, $matches);
    return implode(',', array_unique($matches[1]));
}

//hashtag counter
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
    //convert hashtags to links
    $content = preg_replace_callback(
        '/#(\w+)/', 
        function($matches) {
            return '<a href="/pages/feed.php?search=%23'.urlencode($matches[1]).'" class="hashtag">#'.htmlspecialchars($matches[1]).'</a>';
        },
        htmlspecialchars($content)
    );
    //convert newlines to <br>
    return nl2br($content);
}

function deletePost($pdo, $post_id, $user_id) {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    return $stmt->execute([$post_id, $user_id]);
}
?>