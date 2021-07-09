<?php

namespace App\Repositories;

use App\Models\SectionPosts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SectionPostsRepository implements SectionPostsRepositoryInterface
{

    /**
     * @var SectionPosts
     */
    protected SectionPosts $SectionPosts;

    /**
     * SectionPostsRepository constructor.
     * @param SectionPosts $sectionposts
     */
    public function __construct(SectionPosts $sectionposts)
    {
        $this->SectionPosts = $sectionposts;
    }

    /**
     *
     */
    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    /**
     *
     */
    public function find()
    {
        // TODO: Implement find() method.
    }

    /**
     *
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     *
     */
    public function update()
    {
        // TODO: Implement update() method.
    }

    /**
     *
     */
    public function delete()
    {
        // TODO: Implement delete() method.
    }

    /**
     * 생성
     * @param array $dataObject
     * @return SectionPosts|Model
     */
    public function createPosts(Array $dataObject)
    {
        return $this->SectionPosts::create($dataObject);

    }

    /**
     * 상세
     * @param String $gubun
     * @return SectionPosts|Builder|Model|\Illuminate\Database\Query\Builder
     */
    public function viewPosts(String $gubun = "")
    {
        return $this->SectionPosts::with(['user'])
            ->where([
                ['active', 'Y'], ['publish', 'Y']
            ])->where('gubun' , $gubun)->orderBy("id", 'desc')
            ->firstOrFail();
    }

    /**
     * 존재 확인.
     * @param String $post_uuid
     * @return SectionPosts|bool
     */
    public function postsExitsByPostUid(String $post_uuid)
    {
        return $this->SectionPosts::where([
            ['post_uuid', $post_uuid],
            ['active', 'Y'],
            ['publish', 'Y']
        ])->exists();
    }

    /**
     * 뷰카운트 증가.
     * @param String $post_uuid
     * @return bool
     */
    public function incrementPostsViewCount(String $post_uuid) : bool
    {
        return $this->SectionPosts::where('post_uuid', $post_uuid)->increment('view_count');
    }
}