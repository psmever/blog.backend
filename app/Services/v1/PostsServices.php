<?php

namespace App\Services\v1;

use Illuminate\Http\Request;
use App\Repositories\v1\PostsRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Supports\Facades\GuitarClass;

class PostsServices
{
    protected $postsRepository;

    function __construct(PostsRepository $postsRepository) {
        $this->postsRepository = $postsRepository;
    }

    /**
     * 글 리스트 ( 페이징 처리 ).
     *
     * @param Int $page
     * @return array
     */
    public function posts(Int $page = 1) : array
    {
        $result = collect($this->postsRepository->posts_list($page))->toArray();
        $items = array_map(function($e){
            $user = function($e) {
                return [
                    'user_uuid' => $e['user_uuid'],
                    'name' => $e['name'],
                    'nickname' => $e['nickname'],
                    'email' => $e['email'],
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

            $category_thumb = function($e) {
                return [
                    'code_id' => $e['code_id'],
                    'code_name' => $e['code_name'],
                    'category_thumb_url' => env("MEDIA_URL") . "/blog/post-category/" . $e['code_id'] . ".jpg",
                ];
            };

            $list_contents = function($contents) {
                return Str::limit(strip_tags(htmlspecialchars_decode($contents)), 400);
            };

            $list_created = function($timestamp) {
                return GuitarClass::convertTimeToString(strtotime($timestamp));
            };

            return [
                'post_id' => $e['id'],
                'post_uuid' => $e['post_uuid'],
                'user' => $user($e['user']),
                'post_title' => $e['title'],
                'slug_title' => $e['slug_title'],
                'category_thumb' => $category_thumb($e['category_thumb']),
                'list_contents' => $list_contents($e['contents_html']),
                'markdown' => $e['markdown'],
                'tags' => $tags($e['tag']),
                'post_active' => $e['post_active'],
                'post_publish' => $e['post_publish'],
                'list_created' => $list_created($e['updated_at'])
            ];
        }, $result['data']);

        return [
            // 'result' => $result,
            'per_page' => $result['per_page'],
            'current_page' => $result['current_page'],
            'posts' => $items
        ];
    }

    /**
     * 글 게시.
     *
     * @param String $post_uuid
     * @return void
     */
    public function publishPosts(String $post_uuid) : void
    {
        $postsData = $this->postsRepository->editPostsExits($post_uuid);
        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new \App\Exceptions\ForbiddenErrorException();
        }

        $this->postsRepository->publishPosts($postsData->id);

        return;
    }

    /**
     * 글 내용.
     *
     * @param String $slug_title
     * @return array
     */
    public function detailPosts(String $slug_title) : array
    {
        $result = $this->postsRepository->posts_detail($slug_title);
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

        $detail_created = function($timestamp) {
            return GuitarClass::convertTimeToString(strtotime($timestamp));
        };

        $detail_updated = function($timestamp) {
            return GuitarClass::convertTimeToString(strtotime($timestamp));
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
            'detail_created' => $detail_created($result->created_at),
            'detail_updated' => $detail_updated($result->updated_at),
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
                'category_thumb' => 'required|exists:codes,code_id',
                'tags' => 'required|array|min:1',
                'tags.*' => 'required|array|min:1',
                'contents' => 'required|array|min:2',
                'contents.*' => 'required|string|min:1',
            ],
            [
                'title.required'=> __('default.post.title_required'),
                'category_thumb.required'=> __('default.post.category_thumb_required'),
                'category_thumb.exists'=> __('default.post.category_thumb_required'),
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
            'category_thumb' => $request->input('category_thumb'),
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
            'category_thumb' => 'required|exists:codes,code_id',
            'tags' => 'required|array|min:1',
            'tags.*' => 'required|array|min:1',
            'contents' => 'required|array|min:2',
            'contents.*' => 'required|string|min:1',
        ],
        [
            'title.required'=> __('default.post.title_required'),
            'category_thumb.required'=> __('default.post.category_thumb_required'),
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
            'category_thumb' => $request->input('category_thumb'),
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

    /**
     * 글 정보.
     *
     * @param String $post_uuid
     * @return array
     */
    public function editPosts(String $post_uuid) : array
    {
        $postsData = $this->postsRepository->editPostsExits($post_uuid);
        $user = Auth::user();

        if ($postsData->user_id != $user->id) {
            throw new \App\Exceptions\ForbiddenErrorException();
        }

        $result = $this->postsRepository->editPostsViewById($postsData->id);
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

        $categoryThumb = function($e) {
            return [
                'code_id' => $e->code_id,
                'code_name' => $e->code_name,
                'category_thumb_url' => env("MEDIA_URL") . "/blog/post-category/" . $e->code_id . ".jpg",

            ];
        };

        return [
            'post_id' => $result->id,
            'post_uuid' => $result->post_uuid,
            'user' => $user($result->user),
            'post_title' => $result->title,
            'slug_title' => $result->slug_title,
            'category_thumb' => $categoryThumb($result->categoryThumb),
            'contents_html' => $result->contents_html,
            'contents_text' => $result->contents_text,
            'markdown' => $result->markdown,
            'tags' => $tags($result->tag->toarray()),
            'post_active' => $result->post_active,
            'post_publish' => $result->post_publish,
            'created' => \Carbon\Carbon::parse($result->created_at)->format('Y-m-d H:s'),
            'updated' => \Carbon\Carbon::parse($result->updated_at)->format('Y-m-d H:s'),
        ];
    }
}
