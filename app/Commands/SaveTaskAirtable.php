<?php

namespace App\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;



class SaveTaskAirtable extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'task:air
                        {name : The name of the task} // required
                        {--E|estimated= : The ET of the task}
                        {--D|deadline= : The deadline}
                        {--I|importance= : The importance}
                        {--U|urgency= : The urgency}
                        {--C|comment : A markdown comment}
                        {--S|section : The Kanban Section}
                        {--B|branch : Yes/No, a branch/bereich switch - NOT for Alfred t2, only Alfred >}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Captures task and saves it to Airtable';

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

    if($this->option('branch')){
        $branch = $this->choice('Which branch?', ['Geist', 'Körper', 'Gefühle', 'Karriere', 'Finanzen', 'Beziehungen', 'Spaß', 'Leben']);
    }
    
    if($this->option('section')){
        $section = $this->choice('Which section?', ['Backlog', 'Woche', 'Heute', 'Aktiv', 'Fertig', 'Warten', 'Archivieren']);
    }

    // dd($this->formatInteger($this->option('urgency')));


    $myOptions = [
        'fields' => [
            'Name' => $this->argument('name'),
            'Notes' => $comment ?? null,
            'Deadline' => Carbon::today()->addDays($this->option('deadline'))->format('Y-m-d'),
            'ET' => $this->option('estimated') * 60,
            'I' => $this->formatInteger($this->option('importance')),
            'U' => $this->formatInteger($this->option('urgency')),
            'Section' => $section ?? 'Backlog',
            'Bereich' => $branch ?? 'Erledigen',
    ]];

    $now = Carbon::now();    
    $this->saveApiData('https://api.airtable.com/v0/'.env('BASE_PRIVATE').'/ToDos', $myOptions);
    
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
        ['I', $this->option('importance') ?? 1, '-I 4 (1-5) '],
        ['U', $this->option('urgency') ?? 1, '-U 4 (1-5)'],
        ['Section', $section ?? 'Backlog', '-S'],
        ['Branch', $branch ?? 'Erledigen', '-B'],
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
                'Authorization' => 'Bearer '.env('AIRTABLE'),
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

