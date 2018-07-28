<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class ShowTasks extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'task:show
                            {--type : Filter by type (w/p)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Show all tasks';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $tasks = DB::table('tasks')->get(['title', 'et'])->toArray();
        dd($tasks);
        $headers = ['Title', 'ET'];

        $this->table($headers, $tasks);
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
