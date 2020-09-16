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
     * @param PostsTags $poststag
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
     * 슬러그 타이틀 생성.
     *
     * @param String $title
     * @return void
     */
    public function getSlugTitle(String $title)
    {
        return $this->Posts->slugify($title);
    }

    /**
     * 글등록
     *
     * @param Array $dataObject
     * @return void
     */
    public function createPosts(Array $dataObject) : object
    {
        return $this->Posts::create($dataObject);
    }

    // 테그 등록.
    public function createPostsTags(Array $dataObject) : object
    {
        return $this->PostsTags::insert($dataObject);
    }
}
