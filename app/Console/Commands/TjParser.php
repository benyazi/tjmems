<?php

namespace App\Console\Commands;

use App\Model\Mem;
use Illuminate\Console\Command;
use GuzzleHttp\Client;

class TjParser extends Command
{
    const GET_URL = 'https://tjournal.ru/category/%D0%BC%D0%B5%D0%BC%D1%8B/more/{LASTID}';
    const MAX_ITERATION = 10;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tj:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update data from TJ';

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
        $this->getEntries($client, 68762);
    }

    private function getEntries($client, $lastId, $index=0)
    {
        $break = false;
        $url = str_replace("{LASTID}", $lastId,self::GET_URL);
        $res = $client->request('GET', $url, []);
        if($res->getStatusCode() == 200) {
            $data = json_decode($res->getBody());
            if($data->rc == 200 && $data->rm == "successfull") {
                $itemsHtml = $data->data->items_html;
                $lastId = $data->data->last_id;
                $re = '/air-entry-id=\\"([0-9]*)\\"/';
                preg_match_all($re, $itemsHtml, $matches, PREG_SET_ORDER, 0);
                $this->saveIds($matches);

            } else {
                $this->error('RC AND RM ARE ERROR - ' . $data->rm);
            }
        }
        if($index < self::MAX_ITERATION && !$break && is_numeric($lastId)) {
            $index++;
            $this->getEntries($client, $lastId, $index);
        } else {
            $this->info('END WORKING');
        }
    }

    private function saveIds($matches)
    {
        foreach($matches as $match) {
            $entryId = $match[1];
            if(empty(Mem::find($entryId))) {
                Mem::create(['entryId' => $entryId]);
            }
        }
    }
}
