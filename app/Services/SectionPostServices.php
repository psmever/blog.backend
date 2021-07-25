<?php


namespace App\Services;

use App\Exceptions\CustomException;
use App\Supports\Facades\GuitarClass;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Repositories\SectionPostsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Parsedown;

/**
 * Class SectionPostServices
 * @package App\Services
 */
class SectionPostServices
{
    /**
     * @var SectionPostsRepository
     */
    protected SectionPostsRepository $sectionPostsRepository;

    protected array $sectionGubunCode = [
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

        $parsedown = new Parsedown();
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
            throw new CustomException(__('default.exception.pdo_exception'));
        }
    }

    /**
     * @param String $post_uuid
     */
    public function updateHideSectionPostDisplayFlag(String $post_uuid) : void
    {
        if(!$this->sectionPostsRepository->updatePostsDisplayFlagHidden($post_uuid)) {
            throw new ModelNotFoundException('');
        }
    }

    /**
     * @param String $post_uuid
     */
    public function updateDisplaySectionPostDisplayFlag(String $post_uuid) : void
    {
        if(!$this->sectionPostsRepository->updatePostsDisplayFlagDisplay($post_uuid)) {
            throw new ModelNotFoundException('');
        }
    }

    /**
     * @param String $gubun
     * @param Int $page
     * @return array
     */
    public function sectionPostHistorys(String $gubun = 'S07010', Int $page = 1) : array
    {
        $task = collect($this->sectionPostsRepository->sectionPostHistoryList($gubun, $page))->toArray();
        return [
            'per_page' => $task['per_page'],
            'current_page' => $task['current_page'],
            'hasmore' => !((count($task['data']) < env('DEFAULT_PAGEING_COUNT', 15))),
            'historys' => array_map(function($item) {
                $smallContents = function($contents) {
                    $text = Str::limit(strip_tags(htmlspecialchars_decode($contents)), 400);
                    $textArray = array_filter(explode("\n", $text), fn($value) => !empty($value));
                    return $textArray[0];
                };

                $created_time = function($timestamp) {
                    return GuitarClass::convertTimeToString(strtotime($timestamp));
                };

                return [
                    'post_uuid' => $item['post_uuid'],
                    'gubun' => [
                        'code_id' => $item['gubun']['code_id'],
                        'code_name' => $item['gubun']['code_name'],
                    ],
                    'smal_content' => $smallContents($item['contents_html']),
                    'created_at' => Carbon::parse($item['created_at'])->format('Y년 m월 d일'),
                    'created_time' => $created_time($item['created_at'])
                ];
            } , $task['data'])
        ];
    }

    /**
     * @param String $gubun
     * @param String $post_uuid
     * @return array
     */
    public function sectionHistoryView(String $gubun, String $post_uuid) : array
    {
        $task = $this->sectionPostsRepository->sectionPostHistoryView($gubun, $post_uuid);
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

    public function sectionPostTotalHistorys(String $gubun = 'S07010', Int $page = 1 ) : array
    {
        $task = collect($this->sectionPostsRepository->sectionPostHistoryTotalList($gubun, $page))->toArray();
        return [
            'per_page' => $task['per_page'],
            'current_page' => $task['current_page'],
            'hasmore' => !((count($task['data']) < env('DEFAULT_PAGEING_COUNT', 15))),
            'historys' => array_map(function($item) {
                $smallContents = function($contents) {
                    $text = Str::limit(strip_tags(htmlspecialchars_decode($contents)), 400);
                    $textArray = array_filter(explode("\n", $text), fn($value) => !empty($value));
                    return $textArray[0];
                };

                $created_time = function($timestamp) {
                    return GuitarClass::convertTimeToString(strtotime($timestamp));
                };

                return [
                    'post_uuid' => $item['post_uuid'],
                    'gubun' => [
                        'code_id' => $item['gubun']['code_id'],
                        'code_name' => $item['gubun']['code_name'],
                    ],
                    'smal_content' => $smallContents($item['contents_html']),
                    'publish' => $item['publish'],
                    'active' => $item['active'],
                    'display_flag' => $item['display_flag'],
                    'created_at' => Carbon::parse($item['created_at'])->format('Y년 m월 d일'),
                    'created_time' => $created_time($item['created_at'])
                ];
            } , $task['data'])
        ];
    }
}
