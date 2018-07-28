<?php

namespace App\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;



class SaveTaskAirtableWork extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'task:work
                        {name : The name of the task} // required
                        {--E|estimated= : The ET of the task}
                        {--D|deadline= : The deadline}
                        {--I|importance= : The importance}
                        {--U|urgency= : The urgency}
                        {--C|comment : A markdown comment}
                        {--S|section : The Kanban Section}
                        {--L|lane : The Kanban lane}
                        ';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Captures task and saves it to Work-Airtable';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

    if($this->option('comment')){
        $comment = $this->ask('Note:');
    }
    
    if($this->option('lane')){
        $lane = $this->choice('Which lane?', ['Backlog', 'Woche', 'Morgen','Heute', 'Aktiv', 'Fertig', 'Warten', 'Archivieren']);
    }

    if($this->option('section')){
        $lane = $this->choice('Which section?', ['Cash', 'Camp', 'Cooperation', 'Community', 'Cleanup']);
    }

    // dd($this->formatInteger($this->option('urgency')));


    $myOptions = [
        'fields' => [
            'Beschreibung' => $this->argument('name'),
            'Notizen' => $comment ?? null,
            'Deadline' => Carbon::today()->addDays($this->option('deadline'))->format('Y-m-d'),
            'ET' => $this->option('estimated') * 60,
            'W' => $this->formatInteger($this->option('importance')),
            'D' => $this->formatInteger($this->option('urgency')),
            'Status' => $lane ?? 'Heute',
            'Section' => array($section ?? 'Cleanup'),
            'Person' => array(['id' => "usrbi3oJUtdzP5AMt", 'email' => "martin@code.design", 'name' => "Martin Betz"],)
    ]];

    $now = Carbon::now();    
    $this->saveApiData('https://api.airtable.com/v0/'.env('BASE_WORK').'/Tasks', $myOptions);
    
    $this->line('');
    $this->info('This worked!');
    $this->line('');
    $headers = ['Key', 'Value', 'Command'];

    $output = [
        ['Status Code',$this->status, '200'],
        ['Name',$this->argument('name'), '"Test Task"'],
        ['Notes', $this->option('estimated') ?? '', '-C'],
        ['Deadline', Carbon::today()->addDays($this->option('deadline'))->endOfDay()->diffForHumans(), '-D DAYS'],
        ['ET', $this->option('estimated') * 60, '-E MINUTES'],
        ['W', $this->option('importance') ?? 1, '-I 4 (1-5) '],
        ['D', $this->option('urgency') ?? 1, '-U 4 (1-5)'],
        ['Status', $lane ?? 'Heute', '-S'],
        // ['Section', $section ?? 'Erledigen', '-B'],
    ];

    $this->table($headers, $output);

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

    public function saveApiData($url, $options)
    {
        global $status;
        // dd($url);
        $client = new Client();
        $res = $client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer '.env('AIRTABLE_WORK'),
                'Content-type'     => 'application/json',
            ],
            'json' => $options,
        ]);
        $this->status = $res->getStatusCode();
    }

    // Input can be
    // not set
    // or integer
    public function formatInteger($var = 1){
        $var = intval($var);
        
        if ($var == 0) {
            return $var = 1;
        }
        return $var;
    }
}

