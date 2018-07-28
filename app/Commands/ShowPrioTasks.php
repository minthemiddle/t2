<?php

namespace App\Commands;


use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Illuminate\Support\Collection;


class ShowPrioTasks extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'task:prio';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {


    // https://stackoverflow.com/a/38370987

    // $options = [
    //     'fields' => [
    //         'Name' => $this->argument('name'),
    //         'Notes' => $comment ?? null,
    //         'Deadline' => Carbon::today()->addDays($this->option('deadline'))->format('Y-m-d'),
    //         'ET' => $this->option('estimated') * 60,
    //         'I' => $this->formatInteger($this->option('importance')),
    //         'U' => $this->formatInteger($this->option('urgency')),
    //         'Section' => $section ?? 'Backlog',
    //         'Bereich' => $branch ?? 'Erledigen',
    // ]];

    $basicauth = new Client(['base_uri' => 'https://api.airtable.com']);

    $res = $basicauth->request('GET', '/v0/app7aDiEnc9N4t2io/ToDos?maxRecords=3&view=Alles',
        [
            'headers' => [
                'Authorization' => 'Bearer '.env('AIRTABLE'),
                'Content-type'     => 'application/json',
            ]
        ],
        ['query' => ['maxRecords' => 100, 'pageSize' => 100]
        ])->getBody()->getContents();

    $array = json_decode($res);
    $col = collect($array)->all();

    if ($col instanceof Collection) {
        print_r('Yeah!');
    }
    else {
        print_r('No collection');
    }

    }

    public function getApiData($url)
    {
        global $status;
        $client = new Client();
        $res = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer '.env('AIRTABLE'),
            ],
        ]);
        $this->status = $res->getStatusCode();
    }

    



    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
