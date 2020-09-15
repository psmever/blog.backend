<?php

namespace App\Repositories\v1;

use App\Model\Posts;
use App\Model\PostsTags;

class PostsRepository implements PostsRepositoryInterface
{
    /**
     * @var Posts
     */
    protected $Posts;

    /**
     * @var PostsTag
     */
    protected $PostsTags;

    /**
     * @param Posts $posts
     * @param PostsTag $poststag
     */
    public function __construct(Posts $posts, PostsTags $poststags)
    {
        $this->Posts = $posts;
        $this->PostsTags = $poststags;
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

    /**
     * 글등록
     *
     * @param [type] $dataObject
     * @return void
     */
    public function createPosts($dataObject)
    {
        // print_r($dataObject);
        $task = $this->Posts::create($dataObject);

        if(!$task) {
            return false;
        }

        return $task->id;
    }

    // 테그 등록.
    public function createPostsTags($dataObject)
    {
        $task = $this->PostsTags::create($dataObject);
    }
}
