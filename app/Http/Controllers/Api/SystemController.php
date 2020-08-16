<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiRootController;
use Symfony\Component\Process\Process;
use Illuminate\Http\Request;

class SystemController extends ApiRootController
{
    /**
     * GitHub WebHook Deploy
     * ANCHOR GitHub WebHook Deploy
     * @param Request $request
     * @return void
     */
    public function deploy(Request $request)
    {
        $appEnv = env('APP_ENV');

        if($appEnv == 'production') {

            echo ":: Production Start ::".PHP_EOL;

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

            echo PHP_EOL.":: Production End ::".PHP_EOL;

        } else {

            echo $appEnv." Not Deploy ".PHP_EOL;

        }
    }
}
