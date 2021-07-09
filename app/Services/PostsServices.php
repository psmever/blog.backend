<?php


namespace App\Services;

use App\Exceptions\CustomException;
use App\Exceptions\ForbiddenErrorException;
use App\Exceptions\SomethingErrorException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Repositories\PostsRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Supports\Facades\GuitarClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Parsedown;

/**
 * Class PostsServices
 * @package App\Services
 */
class PostsServices
{
    /**
     * @var PostsRepository
     */
    protected PostsRepository $postsRepository;

    function __construct(PostsRepository $postsRepository) {
        $this->postsRepository = $postsRepository;
    }

    /**
     * 마크다운 텍스트에서 썸네일 뽑아오기.
     *
     * @param String $markdownText
     * @return string
     */
    private static function getThumbNailInContents(String $markdownText = '') : string
    {
        preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $markdownText, $matches);
        $imageMatches = isset($matches[1]) && $matches[1] ? $matches[1] : [];

        if(isset($imageMatches[0]) && $imageMatches[0]) {
            return basename($imageMatches[0]);
        }

        return '';
    }

    /**
     * 글 리스트 생성.
     *
     * @param array $Item
     * @return array
     */
    private static function gneratorPostListResponse(Array $Item) : array
    {
        return array_map(function($e){
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

            $thumb = function($e) {
                if(isset($e['file']) && $e['file']) {
                    return env('MEDIA_URL') . $e['file']['dest_path'].'/'.$e['file']['file_name'];
                } else {
                    return env("MEDIA_URL") . "/assets/blog/img/post_list.png";
                }
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
                'list_contents' => $list_contents($e['contents_html']),
                'markdown' => $e['markdown'],
                'tags' => $tags($e['tag']),
                'thumb_url' => $thumb($e['thumb']),
                'view_count' => $e['view_count'],
                'post_active' => $e['post_active'],
                'post_publish' => $e['post_publish'],
                'list_created' => $list_created($e['created_at'])
            ];
        }, $Item);
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

        $items = self::gneratorPostListResponse($result['data']);

        return [
            'per_page' => $result['per_page'],
            'current_page' => $result['current_page'],
            'hasmore' => !((count($items) < env('DEFAULT_PAGEING_COUNT', 15))),
            'posts' => $items
        ];
    }

    /**
     * 글검색.
     *
     * @param String $searchItem
     * @return array
     */
    public function postsSearch(String $searchItem) : array
    {
        $result = $this->postsRepository->posts_search_list($searchItem)->get()->toArray();

        return self::gneratorPostListResponse($result);
    }

    /**
     * 글 게시.
     *
     * @param String $post_uuid
     * @throws ForbiddenErrorException
     */
    public function publishPosts(String $post_uuid) : void
    {
        $postsData = $this->postsRepository->editPostsExits($post_uuid);
        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new ForbiddenErrorException();
        }

        $this->postsRepository->publishPosts($postsData->id);
    }

    /**
     * 글 숨김.
     *
     * @param String $post_uuid
     * @return bool
     * @throws ForbiddenErrorException
     */
    public function hidePosts(String $post_uuid) : bool
    {
        $postsData = $this->postsRepository->editPostsExits($post_uuid);
        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new ForbiddenErrorException();
        }

        return $this->postsRepository->hidePosts($postsData->id);
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
                // TODO: 정리 필요.
                return [
                    'id' => $e['tag_id'],
                    'tag_id' => $e['tag_id'],
                    'text' => $e['tag_text'],
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
            'view_count' => $result->view_count,
            'post_active' => $result->post_active,
            'post_publish' => $result->post_publish,
            'detail_created' => $detail_created($result->created_at),
            'detail_updated' => $detail_updated($result->updated_at),
        ];
    }

    /**
     * 글등록.
     *
     * @param Request $request
     * @return array
     * @throws CustomException
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
            throw new CustomException($validator->errors()->first());
        }

        $parsedown = new Parsedown();
        $markdownHtmlContents = $parsedown->text($request->input('contents.text'));

        // 글 등록.
        $postTask = $this->postsRepository->createPosts([
            'user_id' => $user_id,
            'post_uuid' => Str::uuid(),
            'title' => $request->input('title'),
            'slug_title' => $this->postsRepository->getSlugTitle($request->input('title')),
            'contents_html' => $markdownHtmlContents,
            'contents_text' => $request->input('contents.text'),
            'markdown' => 'Y'
        ]);

        foreach($request->input('tags') as $element) :
            $this->postsRepository->createPostsTags([
                'post_id' => $postTask->id,
                'tag_id' => $element['tag_id'],
                'tag_text' => $element['tag_text'],
            ]);
        endforeach;

        // 썸네일 이미지.
        $getThumbnail = self::getThumbNailInContents($markdownHtmlContents);

        $file = $this->postsRepository->getMediaFilesId($getThumbnail);

        if($file) {
            $this->postsRepository->createPostsThums([
                'post_id' =>  $postTask->id,
                'media_file_id' => $file->id
            ]);
        }

        $postsData = $this->postsRepository->editPostsExits($postTask->post_uuid);
        return [
            'post_uuid' => $postsData->post_uuid,
            'slug_title' => $postsData->slug_title,
            'post_active' => $postsData->post_active,
            'post_publish' => $postsData->post_publish,
        ];
    }

    /**
     * 글 업데이트
     *
     * @param Request $request
     * @param String $post_uuid
     * @return array
     * @throws CustomException
     * @throws ForbiddenErrorException
     */
    public function updatePosts(Request $request, String $post_uuid) : array
    {
        $postsData = $this->postsRepository->postsExits($post_uuid);
        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new ForbiddenErrorException();
        }

        // FIXME : 코드 중복 처리.
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
            throw new CustomException($validator->errors()->first());
        }

        $parsedown = new Parsedown();
        $markdownHtmlContents = $parsedown->text($request->input('contents.text'));

        //내용 업데이트

        $slug_title = strcmp($request->input('title'), $postsData->title) !== 0 ? $this->postsRepository->getSlugTitle($request->input('title')) : $postsData->slug_title;
        $this->postsRepository->updatePosts($postsData->id, [
            'title' => $request->input('title'),
            'slug_title' => $slug_title,
            'contents_html' => $markdownHtmlContents,
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

        // 썸네일 이미지.
        $getThumbnail = self::getThumbNailInContents($markdownHtmlContents);
        $file = $this->postsRepository->getMediaFilesId($getThumbnail);

        if($file) {
            $this->postsRepository->deletePostsThums($postsData->id);
            $this->postsRepository->createPostsThums([
                'post_id' =>  $postsData->id,
                'media_file_id' => $file->id
            ]);
        }

        return [
            'post_uuid' => $post_uuid,
            'slug_title' => $slug_title,
            'post_active' => $postsData->post_active,
            'post_publish' => $postsData->post_publish,
        ];
    }


    /**
     * 글 삭제.
     *
     * @param String $post_uuid
     * @throws ForbiddenErrorException
     */
    public function deletePosts(String $post_uuid) : void
    {
        // TODO: NotFoundHttpException 처리.
        $postsData = $this->postsRepository->postsExits($post_uuid);

        $user = Auth::user();

        if($postsData->user_id != $user->id) {
            throw new ForbiddenErrorException();
        }

        $this->postsRepository->deletePosts($postsData->id);
    }

    /**
     * 글 정보.
     *
     * @param String $post_uuid
     * @return array
     * @throws ForbiddenErrorException
     */
    public function editPosts(String $post_uuid) : array
    {
        $postsData = $this->postsRepository->editPostsExits($post_uuid);
        $user = Auth::user();

        if ($postsData->user_id != $user->id) {
            throw new ForbiddenErrorException();
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

        return [
            'post_id' => $result->id,
            'post_uuid' => $result->post_uuid,
            'user' => $user($result->user),
            'post_title' => $result->title,
            'slug_title' => $result->slug_title,
            'contents_html' => $result->contents_html,
            'contents_text' => $result->contents_text,
            'markdown' => $result->markdown,
            'tags' => $tags($result->tag->toarray()),
            'post_active' => $result->post_active,
            'post_publish' => $result->post_publish,
            'created' => Carbon::parse($result->created_at)->format('Y-m-d H:s'),
            'updated' => Carbon::parse($result->updated_at)->format('Y-m-d H:s'),
        ];
    }

    /**
     * 이미지 등록 및 기록.
     *
     * @param Request $request
     * @return array
     * @throws CustomException
     * @throws SomethingErrorException
     */
    public function createImage(Request $request) : array
    {
        if ($request->hasFile('image')) {

            if ($request->file('image')->isValid()) {

                $validator = Validator::make($request->all(), [
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ],
                    [
                        'image.required' => __('default.post.image_required'),
                        'image.image' => __('default.post.image_image'),
                        'image.mimes' => __('default.post.image_mimes'),
                        'image.max' => __('default.post.image_max'),
                    ]);

                if( $validator->fails() ) {
                    throw new CustomException($validator->errors()->first());
                }

                $imageExtension = $request->image->extension();

                $uploadFullFileName = GuitarClass::randomNumberUUID() . '.' . $imageExtension;

                Storage::putFileAs('blog/tmp_images/', $request->file('image'), $uploadFullFileName);

                $photo = fopen(storage_path('app/blog/tmp_images' . '/' . $uploadFullFileName), 'r');
                // $photo = file_get_contents(storage_path('app/blog/images/'.sha1(date("Ymd")) . '/' . $uploadFullFileName));

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Client-Token' => env('MEDIA_IMAGE_UPLOAD_TOKEN')
                ])
                    ->attach('media_file', $photo)
                    ->post(env('MEDIA_UPLOAD_API_URL'), [
                        'media_category' => 'blog',
                    ]);

                Storage::delete('blog/tmp_images/' . $uploadFullFileName);

                if(!$response->successful()) {
                    $result = $response->json();
                    throw new SomethingErrorException($result['message']);
                }

                $result = json_decode($response->body())->data;

                $this->postsRepository->createMediaFiles([
                    'dest_path' => $result->dest_path,
                    'file_name' => $result->new_file_name,
                    'original_name' => $result->original_name,
                    'file_type' => $result->file_type,
                    'file_size' => $result->file_size,
                    'file_extension' => $result->file_extension,
                ]);

                return [
                    'media_url' => $result->media_url
                ];
            }
        }

        throw new CustomException(__('default.post.image_required'));
    }

    /**
     * view_count 증가.
     *
     * @param String $post_uuid
     * @throws CustomException
     */
    public function incrementPostsViewCount(String $post_uuid) : void
    {
        if(!$this->postsRepository->postsExitsByUUID($post_uuid)) {
            throw new ModelNotFoundException('');
        }

        if(!$this->postsRepository->incrementPostsViewCount($post_uuid)) {
            throw new CustomException(__('default.exception.pdo_exception'));
        }
    }

    /**
     * 포스트 테그 그룹 리스트
     *
     * @return array
     */
    public function postsTagList() : array
    {
        $result = $this->postsRepository->postsTagGroupList()->get()->toArray();

        if(empty($result)) {
            throw new ModelNotFoundException;
        }

        return array_map(function($e){
            return [
                'value' => $e['tag_text'],
                'count' => $e['count'],
            ];
        }, $result);
    }

    /**
     * 테그 검색 리스트.
     *
     * @param String $search_item
     * @return array
     */
    public function postsTagItemSearch(String $search_item) : array
    {
        $result = $this->postsRepository->postsSearchByTagItem($search_item)->get()->toArray();

        return self::gneratorPostListResponse(array_map(function($e){
            return $e['posts'];
        }, array_filter($result, function($e) {
            return !empty($e['posts']);
        })));
    }

    /**
     * 게시전 글 리스트.
     *
     * @return array
     */
    public function waitingPostsList() : array
    {
        $result = $this->postsRepository->posts_waiting_list()->get()->toArray();
        return array_map(function($e){
            return [
                'post_uuid' => $e['post_uuid'],
                'post_title' => $e['title']
            ];
        }, $result);
    }
}