<?php

namespace App\Services\v1;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

use App\Repositories\v1\CodeRepository;

class SystemsServices
{
    /**
     *
     * @var codeRepository
     */
    protected $codeRepository;

    /**
     * SystemsServices construct
     *
     * @param CodeRepository $codeRepository
     */
    public function __construct(CodeRepository $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    /**
     * Check Notice
     *
     * @return array
     */
    public function checkSystemNotice() : array
    {
        $noticeFileName = 'notice.txt';
        $niticeExists = Storage::disk('sitedata')->exists($noticeFileName);

        // 없을때.
        if($niticeExists == false) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        // 있을때.
        $noticeContents = Storage::disk('sitedata')->get($noticeFileName);
        if ($noticeContents) {
            return [
                'status' => true,
                'data' => $noticeContents
            ];
        }

        return [
            'status' => false,
            'data' => null
        ];
    }

    /**
     * Site Base Data
     *
     * @return array
     */
    public function getSiteData() : array
    {
        $codeGroupList = array();

        // FIXME 2020-08-27 22:32  라라벨 Collection 으로 변경 요망.
        $codes = $this->codeRepository->getAllData()->toArray();

        foreach($codes as $element):
            $group_id = $element['group_id'];
            $code_id = $element['code_id'];
            $code_name = $element['code_name'];

            if($code_id) {
                $codeGroupList[$group_id][] = [
                    'code_id' => $code_id,
                    'code_name' => $code_name,
                ];
            }
        endforeach;

        return [
            'codes' => [
                'code_name' => array_values(array_map(function($e) {
                    $code_id = $e['code_id'];
                    $code_name = $e['code_name'];
                    return [
                        $code_id => $code_name
                    ];
                }, array_filter($codes, function($e) {
                    return $e['code_id'];
                }))),
                'code_group' => $codeGroupList
            ]
        ];
    }

}
