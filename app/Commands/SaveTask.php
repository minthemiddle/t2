<?php

namespace App\Commands;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;


class SaveTask extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'task:create
                        {title : The name of the task} // required
                        {type : p (private) or w (work)}
                        {et : The ET of the task}
                        {importance : Importance 1-5}
                        {urgency : Urgency 1-5}
                        {--D|deadline= : Deadline in +D days}
                        {--C|camp= : Camp}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Captures task and saves it to SQLite';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

    $now = Carbon::now();    

    // dd($this->options());    

    DB::table('tasks')->insert(
        ['title' => $this->argument('title'),
         'type' => $this->argument('type'),
         'et' => $this->argument('et'),
         'importance' => $this->argument('importance'),
         'urgency' => $this->argument('urgency'),
         'deadline' => Carbon::now()->addDays($this->option('deadline')),
         'camp' => $this->option('camp'),
         'status' => 'open',
         'created_at' => Carbon::now(),  
        ]
    );

    $task = DB::table('tasks')->where('created_at', $now)->first();

    Log::error($this->argument('title'));
    $this->notify($task->title, "ET:" . $task->et . ", I:" . $task->importance . ", U:" . $task->urgency);


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
