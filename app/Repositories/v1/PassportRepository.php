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
            // Passport 클라이언트 정보(id, secret)을 가지고 오지 못할떄
            throw new \App\Exceptions\ServerErrorException(__('default.exception.passport_client'));
        }

        $returnObj = new \stdClass();
        $returnObj->client_id = $this->client->id;
        $returnObj->client_secret = $this->client->secret;

        return $returnObj;
    }

}
