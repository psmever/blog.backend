<?php


namespace App\Services\v1;

use App\Exceptions\CustomException;
use App\Supports\Facades\GuitarClass;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Repositories\v1\SectionPostsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Class SectionPostServices
 * @package App\Services\v1
 */
class SectionPostServices
{
    /**
     * @var SectionPostsRepository
     */
    protected $sectionPostsRepository;

    protected $sectionGubunCode = [
        'scribble' => 'S07010',
        'blogs' => 'S07020',
        'mingun' => 'S07030',
    ];

    function __construct(SectionPostsRepository $sectionPostsRepository)
    {
        $this->sectionPostsRepository = $sectionPostsRepository;
    }

    /**
     * @param Request $request
     * @return array
     * @throws CustomException
     */
    public function createPosts(Request $request) : array
    {
        $user_id = Auth::user()->id;

        $validator = Validator::make($request->all(), [
            'contents' => 'required|array|min:2',
            'contents.*' => 'required|string|min:1',
            ],
            [
                'contents.required'=> __('default.post.contents_required'),
                'contents.*.required'=> __('default.post.contents_required'),
        ]);

        //$validator->passes()
        if( $validator->fails() ) {
            throw new CustomException($validator->errors()->first());
        }


        $routeNameArrayDot = explode(".", Route::currentRouteName());
        $GubunRouteName = end($routeNameArrayDot);

        $parsedown = new \Parsedown();
        $markdownHtmlContents = $parsedown->text($request->input('contents.text'));

        // 글 등록.
        $postTask = $this->sectionPostsRepository->createPosts([
            'user_id' => $user_id,
            'post_uuid' => Str::uuid(),
            'gubun' => $this->sectionGubunCode[$GubunRouteName],
            'title' => '',
            'contents_html' => $markdownHtmlContents,
            'contents_text' => $request->input('contents.text'),
            'markdown' => 'Y',
            'publish' => 'Y'
        ]);

        return [
            'post_uuid' => $postTask->post_uuid,
        ];
    }

    /**
     * @return array
     */
    public function viewPosts() : array
    {
        $routeNameArrayDot = explode(".", Route::currentRouteName());
        $GubunRouteName = end($routeNameArrayDot);

        $task = $this->sectionPostsRepository->viewPosts($this->sectionGubunCode[$GubunRouteName]);
        $detail_created = function($timestamp) {
            return GuitarClass::convertTimeToString(strtotime($timestamp));
        };


        return [
            'post_uuid' => $task->post_uuid,
            'contents_html' => $task->contents_html,
            'contents_text' => $task->contents_text,
            'markdown' => $task->markdown,
            'created' => $detail_created($task->created_at),
        ];
    }

    /**
     * @param String $post_uuid
     * @throws CustomException
     */
    public function incrementPostsViewCount(String $post_uuid) : void
    {
        if(!$this->sectionPostsRepository->postsExitsByPostUid($post_uuid)) {
            throw new ModelNotFoundException('');
        }

        if(!$this->sectionPostsRepository->incrementPostsViewCount($post_uuid)) {
            throw new \App\Exceptions\CustomException(__('default.exception.pdo_exception'));
        }
    }

}