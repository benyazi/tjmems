<?php

namespace App\Console\Commands;

use App\Model\Mem;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TjEntryUpdaterBundle extends Command
{
    const GET_BUNDLE_URL = 'https://api.tjournal.ru/v1.3/entry/bundle?ids=';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tj:updatelikes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update entries data from TJ';

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
        $client = new Client(['headers' => ['Accept' => 'application/json']]);
        $mems = Mem::query()
            ->get();
        $memsIds = [];
        foreach ($mems as $mem) {
            if($mem->isMem) {
                $memsIds[] = $mem->entryId;
            }
        }
        $this->updateLikes($client, $memsIds);
        $this->info("ENDED UPDATE DATA, count = " . count($memsIds));
    }

    private function updateLikes($client, $memsIds)
    {
        /**
         * @var Mem $entry
         */
        $url = self::GET_BUNDLE_URL . join(",", $memsIds);
        $res = $client->request('GET', $url, []);
        if($res->getStatusCode() == 200) {
            $data = json_decode($res->getBody());
            $memArray = (array) $data->result;
            foreach ($memArray as $key => $memData) {
                $mem = Mem::query()->where('entryId', $key)->first();
                /**
                 * @var Mem $mem
                 */
                if(!empty($mem)) {
                    $mem->setAttribute('likes', $memData->likes->summ);
                    $mem->setAttribute('commentCount', $memData->commentsCount);
                    $mem->save();
                }
            }

        }
    }

}
