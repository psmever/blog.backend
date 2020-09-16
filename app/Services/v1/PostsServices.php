<?php

namespace App\Services\v1;

use Illuminate\Http\Request;
use App\Repositories\v1\PostsRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostsServices
{
    protected $postsRepository;

    function __construct(PostsRepository $postsRepository) {
        $this->postsRepository = $postsRepository;
    }

    public function createPosts(Request $request)
    {
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

        $user = Auth::user();

        // $postTask = $this->postsRepository->createPosts([
        //     'user_id' => $user->id,
        //     'post_uuid' => Str::uuid(),
        //     'title' => $request->input('title'),
        //     'slug_title' => $this->postsRepository->getSlugTitle($request->input('title')),
        //     'contents_html' => $request->input('contents.html'),
        //     'contents_text' => $request->input('contents.text'),
        //     'markdown' => 'Y'
        // ]);

        // print_r($request->input('tags'));

        $this->postsRepository->createPostsTags($request->input('tags'));

        return [
            // 'post_uuid' => $postTask->post_uuid,
            // 'slug_title' => $postTask->slug_title,
        ];
    }
}