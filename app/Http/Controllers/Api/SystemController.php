<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiRootController;

use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

use App\Traits\v1\SystemTrait;

class SystemController extends ApiRootController
{
    use SystemTrait;

    /**
     * ANCHOR 서버 상태 체크용.
     *
     * php artisan up && php artisan down
     *
     * @param Request $request
     * @return void
     */
    public function check_status(Request $request)
    {
        return Response::success_no_content();
    }

    /**
     * ANCHOR 시스템 공지 사항
     *
     * @param Request $request
     * @return void
     */
    public function check_notice(Request $request)
    {
        $task = SystemTrait::checkSystemNotice();

        if($task['data']) {
            return Response::success([
                'notice_message' => $task['data']
            ]);
        } else {
            return Response::success_no_content();
        }
    }

    public function base_data(Request $request) {}


    /**
     * GitHub WebHook Deploy
     * ANCHOR GitHub WebHook Deploy
     * @param Request $request
     * @return void
     */
    public function deploy(Request $request)
    {
        if(app()->environment('production')) {

            echo ":: Production Deploy Start ::".PHP_EOL;

            $githubPayload = $request->getContent();
            $githubHash = $request->header('X-Hub-Signature');

            $localToken = config('app.deploy_secret');
            $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);

            if (hash_equals($githubHash, $localHash)) {
                $process = Process::fromShellCommandline(base_path('deploy.sh'));
                $process->run(function ($type, $buffer) {
                    echo $buffer;
                });
            }

            echo PHP_EOL.":: Production Deploy End ::".PHP_EOL;

        } else {

            echo " app environment : ".App::environment()." Not Deploy ".PHP_EOL;

        }
    }
}
