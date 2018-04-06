<?php

namespace App\Console\Commands;

use App\Model\Mem;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TjEntryUpdater extends Command
{
    const GET_URL = 'https://api.tjournal.ru/v1.3/entry/{ENTRYID}';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tj:mems';

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
            ->where('name', null)
            ->get();
        foreach ($mems as $mem) {
            $this->getEntryData($client, $mem);
            $this->info("ENDED GETTING DATA TO - " . $mem->name);
            sleep(1);
        }
        $this->info("ENDED UPDATE DATA, count = " . $mems->count());
    }

    private function getEntryData($client, $entry)
    {
        /**
         * @var Mem $entry
         */
        $url = str_replace("{ENTRYID}", $entry->entryId,self::GET_URL);
        $res = $client->request('GET', $url, []);
        if($res->getStatusCode() == 200) {
            $data = json_decode($res->getBody());
            $result = $data->result;
            $entry->setAttribute('likes', $result->likes->summ);
            $entry->setAttribute('name', $result->title);
            $entry->setAttribute('entryTitle', $result->title);
            $entry->setAttribute('commentCount', $result->commentsCount);
            if(mb_stripos($result->title, "Мем:") !== false) {
                $entry->setAttribute('ismem', true);
            }
            $entry->save();
        }
    }

}
