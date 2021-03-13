<?php

namespace App\Repositories\v1;

use App\Models\SectionPosts;

class SectionPostsRepository implements SectionPostsRepositoryInterface
{

    protected $SectionPosts;


    public function __construct(SectionPosts $sectionposts)
    {
        $this->SectionPosts = $sectionposts;
    }


    /**
     * @return mixed
     */
    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @return mixed
     */
    public function find()
    {
        // TODO: Implement find() method.
    }

    /**
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * @return mixed
     */
    public function update()
    {
        // TODO: Implement update() method.
    }

    /**
     * @return mixed
     */
    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function createPosts(Array $dataObject)
    {
        return $this->SectionPosts::create($dataObject);

    }

    public function viewPosts(String $gubun = "")
    {
        return $this->SectionPosts::with(['user'])
            ->where([
                ['active', 'Y'], ['publish', 'Y']
            ])->where('gubun' , $gubun)
            ->firstOrFail();
    }

    public function postsExitsByPostUid(String $post_uuid)
    {
        return $this->SectionPosts::where([
            ['post_uuid', $post_uuid],
            ['active', 'Y'],
            ['publish', 'Y']
        ])->exists();
    }

    public function incrementPostsViewCount(String $post_uuid) : bool
    {
        return $this->SectionPosts::where('post_uuid', $post_uuid)->increment('view_count');
    }
}
