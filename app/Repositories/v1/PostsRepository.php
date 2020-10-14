<?php

namespace App\Repositories\v1;

use App\Model\Posts;
use App\Model\PostsTags;
use App\Model\MediaFiles;
use App\Model\PostsThumbs;

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
     * @var MediaFiles
     */
    protected $MediaFiles;

    /**
     * @var PostsThumbs
     */
    protected $PostsThumbs;

    /**
     * @param Posts $posts
     * @param PostsTags $poststag
     */
    public function __construct(Posts $posts, PostsTags $poststags, MediaFiles $mediafiles, PostsThumbs $poststhumbs)
    {
        $this->Posts = $posts;
        $this->PostsTags = $poststags;
        $this->MediaFiles = $mediafiles;
        $this->PostsThumbs = $poststhumbs;
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
    public function getSlugTitle(String $title) : string
    {
        return $this->Posts->slugify($title);
    }

    /**
     * 글등록.
     *
     * @param Array $dataObject
     * @return void
     */
    public function createPosts(Array $dataObject) : object
    {
        return $this->Posts::create($dataObject);
    }

    /**
     * 글게시.
     *
     * @param Array $dataObject
     * @return void
     */
    public function publishPosts(Int $post_id) : bool
    {
        return $this->Posts::where('id', $post_id)->update([
            'post_publish' => 'Y'
        ]);
    }

    // 정상적인 공개 글이 있는지 체크
    public function postsExitsByUUID(String $post_uuid) : bool
    {
        return $this->Posts::where([
            ['post_uuid', $post_uuid],
            ['post_active', 'Y'],
            ['post_publish', 'Y']
        ])->exists();
    }

    /**
     * view_count 증가.
     *
     * @param String $post_uuid
     * @return boolean
     */
    public function incrementPostsViewCount(String $post_uuid) : bool
    {
        return $this->Posts::where('post_uuid', $post_uuid)->increment('view_count');
    }

    // 테그 등록.
    public function createPostsTags(Array $dataObject) : object
    {
        return $this->PostsTags::create($dataObject);
    }

    // 글 목록(페이징처리).
    public function posts_list(Int $pages)
    {
        return $this->Posts::with(['user', 'tag', 'thumb.file'])
            ->where([
                ['post_active', 'Y'], ['post_publish', 'Y']
            ])->orderBy('updated_at','DESC')->simplePaginate(env('DEFAULT_PAGEING_COUNT', 15), ['*'], 'page', $pages);
    }

    /**
     * slug URL 로 글 내용 보기.
     *
     * @param String $slug_title
     * @return object
     */
    public function posts_detail(String $slug_title) : object
    {
        return $this->Posts::with(['user', 'tag'])
            ->where([
                ['post_active', 'Y'], ['post_publish', 'Y']
            ])->where('slug_title' , $slug_title)
            ->firstOrFail();
    }

    /**
     * 일반 뷰.
     * 글 정보
     *
     * @param Int $id
     * @return object
     */
    public function postsViewById(Int $id) : object
    {
        return $this->Posts::with(['user', 'tag'])
            ->where([
                ['post_active', 'Y'], ['post_publish', 'Y']
            ])->where('id' , $id)
            ->firstOrFail();
    }

    /**
     * 일반 뷰.
     * 글 유무 체크.
     *
     * @param String $post_uuid
     * @return object
     */
    public function postsExits(String $post_uuid) : object
    {
        return $this->Posts::where('post_uuid', $post_uuid)->firstOrFail();
    }

    /**
     * 에디트용.
     * 글 정보.
     *
     * @param Int $id
     * @return object
     */
    public function editPostsViewById(Int $id) : object
    {
        return $this->Posts::with(['user', 'tag'])
            ->where('id' , $id)
            ->firstOrFail();
    }

    /**
     * 에디트용
     * 글 유무 체크.
     *
     * @param String $post_uuid
     * @return object
     */
    public function editPostsExits(String $post_uuid) : object
    {
        return $this->Posts::where('post_uuid', $post_uuid)->firstOrFail();
    }

    /**
     * 글 내용 업데이트.
     *
     * @param Int $post_id
     * @param Array $dataObject
     * @return boolean
     */
    public function updatePosts(Int $post_id, Array $dataObject) : bool
    {
        return $this->Posts::where('id', $post_id)->update($dataObject);
    }

    /**
     * 테그 삭제.
     *
     * @param Int $post_id
     * @return boolean
     */
    public function deletePostsTags(Int $post_id) : bool
    {
        return $this->PostsTags::where('post_id', $post_id)->delete();
    }

    /**
     * 글 삭제.
     *
     * @param Int $post_id
     * @return boolean
     */
    public function deletePosts(Int $post_id) : bool
    {
        return $this->Posts::where('id', $post_id)->delete();
    }

    /**
     * 업로드 파일 기록 테이블
     *
     * @param Array $dataObject
     * @return boolean
     */
    public function createMediaFiles(Array $dataObject) : object
    {
        return $this->MediaFiles::create($dataObject);
    }

    /**
     * 미디어 파일 id
     *
     * @param String $imageName
     * @return void
     */
    public function getMediaFilesId(String $imageName) : ?object
    {
        return $this->MediaFiles::where('file_name', $imageName)->first();
    }

    /**
     * 썸네일 생성.
     *
     * @param array $dataObject
     * @return object
     */
    public function createPostsThums($dataObject = []) : object
    {
        return $this->PostsThumbs::create($dataObject);
    }

    public function deletePostsThums(Int $post_id) : bool
    {
        return $this->PostsThumbs::where('post_id', $post_id)->delete();
    }

}
