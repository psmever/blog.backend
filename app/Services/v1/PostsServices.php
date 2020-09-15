<?php

namespace App\Services\v1;

use Illuminate\Http\Request;
use App\Repositories\v1\PostsRepository;
use Illuminate\Support\Facades\Validator;

class PostsServices
{
    protected $postsRepository;

    function __construct(PostsRepository $postsRepository) {
        $this->postsRepository = $postsRepository;
    }

    public function createPosts(Request $request)
    {
        // print_r($request->input('tags'));
        $validator = Validator::make($request->all(), [
                'title' => 'required',
                'tags' => 'required|array|min:1',
                'tags.*' => 'required|array|min:1',
                'contents' => 'required|array|min:2',
                'contents.*' => 'required|string|min:1',
            ],
            [
                'title.required'=> __('default.post.title_required'),
                'tags.required'=> __('default.post.tags_required'),
                'contents.required'=> __('default.post.contents_required'),
                'contents.*.required'=> __('default.post.contents_required'),
        ]);

        //$validator->passes()
        if( $validator->fails() ) {
            throw new \App\Exceptions\CustomException($validator->errors()->first());
        }

        // TODO 2020-09-15 00:00  유효성 통과.
    }
}
