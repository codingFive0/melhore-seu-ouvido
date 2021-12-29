<?php

namespace Source\Models;

use Source\Core\Model;
use Source\Models\Post\Post;

/**
 * Class Category
 * @package Source\Models
 */
class Category extends Model
{
    /**
     * Category constructor.
     */
    public function __construct()
    {
        parent::__construct("categories", ["id"], ["title", "uri"]);
    }

    /**
     * @param string $uri
     * @param string $columns
     * @return Category|null
     */
    public function findByUri(string $uri, string $columns = "*"): ?Category
    {
        $find = $this->find("uri = :uri", "uri={$uri}", $columns);
        return $find->fetch();
    }

    /**
     * @return mixed|Model
     */
    public function posts()
    {
        return (new Post())->find("category = :id", "id={$this->id}");
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $chechUri = (new Category())->find("uri = :uri AND id != :id", "uri={$this->uri}&id={$this->id}");
        if($chechUri->count()){
            $this->uri = "{$this->uri}-{$this->lastId()}";
        }

        return parent::save();
    }
}