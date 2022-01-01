<?php

namespace Source\Models\App;

class File extends \Source\Core\Model
{
    public function __construct()
    {
        parent::__construct("app_files", ["id"], ["name", "hash"]);
    }

    public function fullName()
    {
        return $this->name . '.' . $this->extension;
    }
}