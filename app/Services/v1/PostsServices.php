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

    // TODO 글리스트 페이징 형식 처리 필요.
    public function posts(Int $page = 1) : array
    {
        $result = collect($this->postsRepository->posts_list($page))->toArray();
        return array_map(function($e){
            $user = function($e) {
                return [
                    'user_uuid' => $e['user_uuid'],
                    'user_type' => [
                        'code_id' => $e['user_type']['code_id'],
                        'code_name' => $e['user_type']['code_name'],
                    ],
                    'user_level' => [
                        'code_id' => $e['user_level']['code_id'],
                        'code_name' => $e['user_level']['code_name'],
                    ],
                    'name' => $e['name'],
                    'nickname' => $e['nickname'],
                    'email' => $e['email'],
                    'active' => $e['active'],
                ];
            };

            $tags = function($e) {
                return array_map(function($e){
                    return [
                        'tag_id' => $e['tag_id'],
                        'tag_text' => $e['tag_text'],
                    ];
                }, $e);
            };

            return [
                'post_uuid' => $e['post_uuid'],
                'user' => $user($e['user']),
                'post_title' => $e['title'],
                'slug_title' => $e['slug_title'],
                'contents_html' => $e['contents_html'],
                'contents_text' => $e['contents_text'],
                'markdown' => $e['markdown'],
                'tags' => $tags($e['tag']),
                'post_active' => $e['post_active'],
                'created' => \Carbon\Carbon::parse($e['created_at'])->format('Y-m-d H:s'),
                'updated' => \Carbon\Carbon::parse($e['updated_at'])->format('Y-m-d H:s'),
            ];
        }, $result['data']);
    }

    /**
     * 글 내용.
     *
     * @param String $slug_title
     * @return array
     */
    public function viewPosts(String $slug_title) : array
    {
        $result = $this->postsRepository->posts_view($slug_title);
        $user = function($user) {
            return [
                'user_uuid' => $user->user_uuid,
                'user_type' => [
                    'code_id' => $user->userType->code_id,
                    'code_name' => $user->userType->code_name,
                ],
                'user_level' => [
                    'code_id' => $user->userLevel->code_id,
                    'code_name' => $user->userLevel->code_name,
                ],
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'active' => $user->active,
            ];
        };

        $tags = function($e) {
            return array_map(function($e){
                return [
                    'tag_id' => $e['tag_id'],
                    'tag_text' => $e['tag_text'],
                ];
            }, $e);
        };

        return [
            'post_uuid' => $result->post_uuid,
            'user' => $user($result->user),
            'post_title' => $result->title,
            'slug_title' => $result->slug_title,
            'contents_html' => $result->contents_html,
            'contents_text' => $result->contents_text,
            'markdown' => $result->markdown,
            'tags' => $tags($result->tag->toarray()),
            'post_active' => $result->post_active,
            'created' => \Carbon\Carbon::parse($result->created_at)->format('Y-m-d H:s'),
            'updated' => \Carbon\Carbon::parse($result->updated_at)->format('Y-m-d H:s'),
        ];
    }

    /**
     * 글등록.
     *
     * @param Request $request
     * @return void
     */
    public function createPosts(Request $request) : array
    {
        $user_id = Auth::user()->id;

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

        // 글 등록.
        $postTask = $this->postsRepository->createPosts([
            'user_id' => $user_id,
            'post_uuid' => Str::uuid(),
            'title' => $request->input('title'),
            'slug_title' => $this->postsRepository->getSlugTitle($request->input('title')),
            'contents_html' => $request->input('contents.html'),
            'contents_text' => $request->input('contents.text'),
            'markdown' => 'Y'
        ]);

        // 테그 등록.
        foreach($request->input('tags') as $element) :
            $this->postsRepository->createPostsTags([
                'post_id' => $postTask->id,
                'tag_id' => $element['tag_id'],
                'tag_text' => $element['tag_text'],
            ]);
        endforeach;

        return [
            'post_uuid' => $postTask->post_uuid,
            'slug_title' => $postTask->slug_title,
        ];
    }

    /**
     * 글 업데이트
     *
     * @param Request $request
     * @param String $post_uuid
     * @return array
     */
    public function updatePosts(Request $request, String $post_uuid) : array
    {
        $postsData = $this->postsRepository->postsExits($post_uuid);
        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new \App\Exceptions\ForbiddenErrorException();
        }

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

        //내용 업데이트
        $slug_title = $this->postsRepository->getSlugTitle($request->input('title'));
        $this->postsRepository->updatePosts($postsData->id, [
            'title' => $request->input('title'),
            'slug_title' => $slug_title,
            'contents_html' => $request->input('contents.html'),
            'contents_text' => $request->input('contents.text'),
            'markdown' => 'Y'
        ]);

        // 기존 테그 삭제.
        $this->postsRepository->deletePostsTags($postsData->id);

        // 테그 추가.
        foreach($request->input('tags') as $element) :
            $this->postsRepository->createPostsTags([
                'post_id' => $postsData->id,
                'tag_id' => $element['tag_id'],
                'tag_text' => $element['tag_text'],
            ]);
        endforeach;

        return [
            'post_uuid' => $postsData->id,
            'slug_title' => $slug_title,
        ];
    }

    /**
     * 글 삭제.
     *
     * @param String $post_uuid
     * @return void
     */
    public function deletePosts(String $post_uuid) : void
    {
        $postsData = $this->postsRepository->postsExits($post_uuid);
        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new \App\Exceptions\ForbiddenErrorException();
        }

        $this->postsRepository->deletePosts($postsData->id);

        return;
    }
}
