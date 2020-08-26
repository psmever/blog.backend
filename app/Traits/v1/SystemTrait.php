<?php
namespace App\Traits\v1;

use Illuminate\Support\Facades\Storage;

trait SystemTrait
{
    // 테스트
    public static function traitsTest()
    {
        // echo env('APP_ENV');
    }

    /**
     * NOTE 시스템 공지사항 체크.
     * storage/sitedata/notice.txt
     *
     * @return void
     */
    public static function checkSystemNotice() : array
    {
        $noticeFileName = 'notice.txt';
        $niticeExists = Storage::disk('sitedata')->exists($noticeFileName);
        if($niticeExists == true) { // 있을때.

            $noticeContents = Storage::disk('sitedata')->get($noticeFileName);
            if($noticeContents) {
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
}
