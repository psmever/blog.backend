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
        $returnObject = function($codes) {
            // 공통 코드 그룹 별로 구분.
            $code_group = array();
            array_map(function($element) use (&$code_group) {
                $code_group[$element['group_id']][] = [
                    'code_id' => $element['code_id'],
                    'code_name' => $element['code_name'],
                ];
            }, array_filter($codes, function($e) {
                return $e['code_id'];
            }));

            // 코드 명으로 분리.
            $code_name = array();
            array_map(function($element) use (&$code_name) {
                $code_name[$element['code_id']] = $element['code_name'];
            }, array_filter($codes, function($e) {
                return $e['code_id'];
            }));

            return [
                'code_name' => $code_name,
                'code_group' => $code_group
            ];
        };

        return [
            'codes' => $returnObject($this->codeRepository->getAllData()->toArray())
        ];
    }

}
