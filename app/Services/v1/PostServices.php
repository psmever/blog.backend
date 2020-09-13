<?php

namespace App\Services\v1;

use Illuminate\Http\Request;
use App\Repositories\v1\PostRepository;
use Illuminate\Support\Facades\Validator;

class PostServices
{
    protected $currentRequest;
    protected $postRepository;

    function __construct(Request $request, PostRepository $postRepository) {
        $this->currentRequest = $request;
        $this->postRepository = $postRepository;
    }

    public function createPosts()
    {
        $request = $this->currentRequest;
        $validator = Validator::make($request->all(), [
                'editorTitle' => 'required',
                'editorContents' => 'required',
                'editorTagContents' => 'required',
            ],
            [
                'editorTitle.required'=> __('default.post.email_required'),
                'editorTagContents.required'=> __('default.post.tag_required'),
                'editorContents.required'=> __('default.post.contents_required'),
        ]);

        if( $validator->fails() ) {
            throw new \App\Exceptions\CustomException($validator->errors()->first());
        }

    }

}
