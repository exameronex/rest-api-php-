<?php 
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *, Authorization");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Credentials: true");
    header("Content-type: application/json; charset=UTF-8");

    require "bootstrap.php";

    $method = $_SERVER['REQUEST_METHOD'];

    $parts = explode("/", $_SERVER["REQUEST_URI"]);

    switch ($method) {
        case "GET":
            if ($parts[1] == "posts") {
                if (isset($parts[2])) {
                    $query = "SELECT * FROM posts WHERE id_post = {$parts[2]}";
                    $posts = $conn->query($query);      
        
                    if (mysqli_num_rows($posts) == 0) {
                        http_response_code(404);
                        exit;
                    } else {
                        $post = mysqli_fetch_assoc($posts);
                        echo json_encode($post);
                    }
                } else {
                    $query = "SELECT * FROM posts";
                    $posts = $conn->query($query); 
        
                    while($post = mysqli_fetch_assoc($posts)) {
                        $postsList[] = $post;
                    }
                    
                    echo json_encode($postsList);
                }
            }
            break;

        case "POST":
            if ($parts[1] == "posts") {
                $query = "INSERT INTO posts (title, body) VALUES ('{$_POST['title']}', '{$_POST['body']}')";
                $conn->query($query); 
                http_response_code(201);

                echo json_encode([
                    "status" => true,
                    "post_id" => $conn->insert_id
                ]);
            }
            break;
        
        case "PATCH":
            if ($parts[1] == "posts") {
                if (isset($parts[2])) {
                    $data = json_decode(file_get_contents("php://input"), true);
                    $query = "UPDATE posts SET title = '{$data['title']}', body = '{$data['body']}' WHERE id_post={$parts[2]}";
                    $conn->query($query); 
                    http_response_code(200);

                    echo json_encode([
                    "status" => true,
                    "message" => "Post is updated"]);
                }
            }
        break;

        case "DELETE":
            if ($parts[1] == "posts") {
                if (isset($parts[2])) {
                    $query = "DELETE FROM posts WHERE id_post = {$parts[2]}";
                    $conn->query($query); 
                    http_response_code(201);

                    echo json_encode([
                        "status" => true,
                        "message" => "Post is deleted"]);
                }
            }

    }

?>