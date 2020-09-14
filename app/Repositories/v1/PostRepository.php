<?php

namespace App\Repositories\v1;

use App\Model\Posts;

class PostRepository implements PostRepositoryInterface
{
    /**
     * @var Posts
     */
    protected $Posts;

    /**
     * CodeRepository construct
     *
     * @param Posts $posts
     */
    public function __construct(Posts $posts)
    {
        $this->Posts = $posts;
    }

    public function getAll()
    {
        return $this->Posts::All();
    }

    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}

    public function getAllData() : object
    {
        return $this->Posts::where('active', 'Y')
            ->orderBy('id', 'asc')
            ->get();
    }
}
