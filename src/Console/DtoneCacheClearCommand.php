<?php

namespace Ghanem\Dtone\Console;

use Ghanem\Dtone\Request;
use Illuminate\Console\Command;

class DtoneCacheClearCommand extends Command
{
    protected $signature = 'dtone:cache-clear
                            {endpoint? : Specific endpoint to clear (e.g. services, countries)}';

    protected $description = 'Clear DT One cached API responses';

    public function handle()
    {
        $endpoint = $this->argument('endpoint');

        Request::clearCache($endpoint);

        if ($endpoint) {
            $this->info('Cleared cache for: ' . $endpoint);
        } else {
            $this->info('Cleared all DT One cache.');
        }

        return 0;
    }
}
