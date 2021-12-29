<?php

namespace Source\Models\Post;

use Source\Core\Model;

class Gallery extends Model
{
    public function __construct()
    {
        parent::__construct("post_gallery", ["id"], ["post_id", "images"]);
    }

    public function findByPost(int $postId): Gallery
    {
        return $this->find("post_id = :post", "post={$postId}")->fetch();
    }
}