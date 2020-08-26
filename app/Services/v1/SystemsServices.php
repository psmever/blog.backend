<?php

namespace App\Services\v1;

use Illuminate\Support\Facades\Storage;

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
        if ($niticeExists == true) { // 있을때.

            $noticeContents = Storage::disk('sitedata')->get($noticeFileName);
            if ($noticeContents) {
                return [
                    'status' => true,
                    'data' => $noticeContents
                ];
            } else {
                return [
                    'status' => false,
                    'data' => null
                ];
            }
        } else { // 없을때.
            return [
                'status' => false,
                'data' => null
            ];
        }
    }

    /**
     * Site Base Data
     *
     * @return array
     */
    public function getSiteData() : array
    {

        return [
            'code' => $this->codeRepository->getAll()
        ];
    }

}
