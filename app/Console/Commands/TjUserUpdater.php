<?php

namespace App\Console\Commands;

use App\Model\Mem;
use App\Model\TjUser;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TjUserUpdater extends Command
{
    const GET_URL = 'https://api.tjournal.ru/v1.3/user/{USERID}';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tj:update-users {--offset=1} {--limit=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all users from TJ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $offset = $this->option('offset');
        $limit = $this->option('limit');

        if($offset == 'last') {
            $lastUser = TjUser::query()->orderBy('tjId', 'desc')->first();
            $offset = ($lastUser->tjId + 1);
        }

        $client = new Client(['headers' => ['Accept' => 'application/json']]);
        $id = $offset;
        $iterationBlock = 1;
        while (true) {
            try {
                $result = $this->getUserData($client, $id);
            } catch (ServerException $e) {
                $this->error("EXCEPTION from API... wait some seconds and try again");
                sleep(10);
                $result = $this->getUserData($client, $id);
            }
            if(!$result) {
                break;
            }
            if($id >= ($offset + $limit)) {
                $this->info("LIMIT DONE");
                break;
            }
            $id++;
            if($iterationBlock >=15) {
                sleep(10);
                $iterationBlock = 0;
            } else {
                usleep(mt_rand(100000, 500000));
            }
            $iterationBlock++;
        }
        $this->info("ENDED GET USERS, count = " . $id);
    }

    /**
     * @param Client $client
     * @param integer $id
     * @return bool
     */
    private function getUserData($client, $id)
    {
        $this->info("GETTING USER DATA ID#" . $id);
        /**
         * @var Mem $entry
         */
        $url = str_replace("{USERID}",$id,self::GET_URL);
        $res = $client->request('GET', $url, []);
        if($res->getStatusCode() == 200) {
            $body = $res->getBody();
            $data = json_decode($body);
            if(!isset($data->error)) {
                $userData = $data->result;
                $user = new TjUser();

                $user->setRawAttributes([
                    'tjId' => $userData->id,
                    'name' => $userData->name,
                    'created' => date("Y-m-d H:i:s", $userData->created),
                    'avatarUrl' => $userData->avatar_url,
                    'karma' => $userData->karma,
                    'entryCount' => $userData->counters->entries,
                    'commentCount' => $userData->counters->comments,
                    'favoriteCount' => $userData->counters->favorites,
                ]);
                if(isset($userData->advanced_access->tj_subscription)
                    && isset($userData->advanced_access->tj_subscription->is_active)) {
                    $user->setAttribute('isSubscriptionActive', $userData->advanced_access->tj_subscription->is_active);
                }
                $user->setAttribute('tjObject', $body);
                return $user->save();

            } else {
                $this->info("ERROR " . $data->error->message);
                return false;
            }
        }
    }

}
