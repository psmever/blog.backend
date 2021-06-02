<?php

namespace App\Repositories;

use App\Exceptions\ServerErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use stdClass;

class PassportRepository implements PassportRepositoryInterface
{
    /**
     * @var Model|Builder|object|null
     */
    protected $client;

    /**
     * PassportRepository constructor.
     */
    public function __construct() {
        $this->client = DB::table('oauth_clients')->where('id', 2)->first();
    }

    /**
     * @return object
     * @throws ServerErrorException
     */
    public function clientInfo() : object
    {
        /**
         * Passport 클라이언트 오류.
         */
        if($this->client == null) {
            /**
             * Passport 클라이언트 정보(id, secret)을 가지고 오지 못할떄
             */
            throw new ServerErrorException(__('default.exception.passport_client'));
        }

        $returnObj = new stdClass();

        // FIXME: id 경고
        $returnObj->client_id = $this->client->id;
        $returnObj->client_secret = $this->client->secret;

        return $returnObj;
    }
}
