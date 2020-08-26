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

        return [
            'code' => $this->codeRepository->getAll()
        ];
    }

}
