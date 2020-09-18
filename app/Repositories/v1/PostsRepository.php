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
        return $this->PostsTags::create($dataObject);
    }

    // 글 목록.
    public function posts_list(Int $pages)
    {
        return $this->Posts::with(['user', 'tag'])->where('post_active', 'Y')->get();
    }

    /**
     * slug URL 로 글 내용 보기.
     *
     * @param String $slug_title
     * @return object
     */
    public function posts_view(String $slug_title) : object
    {
        return $this->Posts::with(['user', 'tag'])->where('post_active', 'Y')->where('slug_title' , $slug_title)->firstOrFail();
    }
}
