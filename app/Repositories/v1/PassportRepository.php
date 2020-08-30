<?php

namespace App\Repositories\v1;

use Illuminate\Support\Facades\DB;

class PassportRepository implements PassportRepositoryInterface
{
    /**
     * @var client
     */
    protected $client;

    /**
     * PassportRepository constructor.
     */
    public function __construct() {
        $this->client = DB::table('oauth_clients')->where('id', 2)->first();
    }

    /**
     * Passport Client 정보.
     *
     * @return void
     */
    public function clientInfo() : object
    {
        $returnObj = new \stdClass();
        // // Passport 클라이언트 오류.
        if($this->client == null) {
            // FIXME 서버 에러 로그
            throw new \App\Exceptions\ServerErrorException(__('default.exception.passport_client'));
        }

        $returnObj = new \stdClass();
        $returnObj->client_id = $this->client->id;
        $returnObj->client_secret = $this->client->secret;

        return $returnObj;
    }

}
