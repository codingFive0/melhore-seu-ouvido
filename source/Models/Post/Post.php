<?php

namespace Source\Models\Post;

use Source\Core\Model;
use Source\Models\Category;
use Source\Models\User;

/**
 * Class Post
 * @package Source\Models
 */
class Post extends Model
{
    /**
     * Post constructor.
     * @param bool $all = ignore status and post_at
     */
    public function __construct()
    {
        parent::__construct("posts", ["id"], ["title", "uri", "subtitle", "content"]);
    }

    /**
     * @param string|null $terms
     * @param string|null $params
     * @param string $columns
     * @return Model|null
     */
    public function findPost(?string $terms = null, ?string $params = null, string $columns = "*"): ?Model
    {
        $terms = "status = :status AND post_at <= NOW()" . ($terms ? " AND {$terms}" : "");
        $params = "status=post" . ($params ? "&{$params}" : "");

        return parent::find($terms, $params, $columns);
    }

    /**
     * @param string $uri
     * @param string $columns
     * @return Post|null
     */
    public function findByUri(string $uri, string $columns = "*"): ?Post
    {
        $find = $this->findPost("uri = :uri", "uri={$uri}", $columns);
        return $find->fetch();
    }

    /**
     * @return User|null
     */
    public function author(): ?User
    {
        if ($this->author) {
            return (new User())->findById($this->author);
        }
        return null;
    }

    /**
     * @return Category|null
     */
    public function category(): ?Category
    {
        if ($this->category) {
            return (new Category())->findById($this->category);
        }
        return null;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $chechUri = (new Post())->find("uri = :uri AND id != :id", "uri={$this->uri}&id={$this->id}");
        if($chechUri->count()){
            $this->uri = "{$this->uri}-{$this->lastId()}";
        }

        return parent::save();
    }
}