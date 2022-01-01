<?php

namespace Source\Models\App;

use Source\Core\Model;

class Note extends Model
{
    /**
     * Notes constructor.
     */
    public function __construct()
    {
        parent::__construct("app_notes", ["id"], ["name", "file"]);
    }

    public function file()
    {
        return (new File())->findById($this->file);
    }
}