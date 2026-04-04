<?php

namespace App\Console\Commands;

use App\Jobs\RunPageSpeedJob;
use App\Models\Web;
use Illuminate\Console\Command;

class RunScheduledPageSpeed extends Command
{
    protected $signature = 'pagespeed:run-scheduled
                            {--web= : Run for a specific web ID only}
                            {--force : Force run regardless of schedule}';

    protected $description = 'Dispatch PageSpeed jobs for sites with an active schedule (daily/weekly/monthly).';

    public function handle(): int
    {
        if ($webId = $this->option('web')) {
            $web = Web::find((int) $webId);
            if (!$web) {
                $this->error("Web #{$webId} not found.");
                return self::FAILURE;
            }
            RunPageSpeedJob::dispatch($web->id);
            $this->info("Dispatched PageSpeed job for: {$web->name} ({$web->url})");
            return self::SUCCESS;
        }

        $query = Web::whereIn('pagespeed_schedule', ['daily', 'weekly', 'monthly'])
            ->whereNotNull('url')
            ->where('url', '!=', '');

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('pagespeed_last_checked_at')
                  ->orWhere(function ($q2) {
                      // daily: not checked in last 23h
                      $q2->where('pagespeed_schedule', 'daily')
                         ->where('pagespeed_last_checked_at', '<', now()->subHours(23));
                  })
                  ->orWhere(function ($q2) {
                      // weekly: not checked in last 6 days
                      $q2->where('pagespeed_schedule', 'weekly')
                         ->where('pagespeed_last_checked_at', '<', now()->subDays(6));
                  })
                  ->orWhere(function ($q2) {
                      // monthly: not checked in last 27 days
                      $q2->where('pagespeed_schedule', 'monthly')
                         ->where('pagespeed_last_checked_at', '<', now()->subDays(27));
                  });
            });
        }

        $webs = $query->get();

        if ($webs->isEmpty()) {
            $this->info('No sites due for analysis.');
            return self::SUCCESS;
        }

        foreach ($webs as $web) {
            RunPageSpeedJob::dispatch($web->id);
            $this->line("  ➜ Queued: {$web->name} ({$web->url}) [{$web->pagespeed_schedule}]");
        }

        $this->info("Dispatched {$webs->count()} PageSpeed job(s).");
        return self::SUCCESS;
    }
}
