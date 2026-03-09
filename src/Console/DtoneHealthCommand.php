<?php

namespace Ghanem\Dtone\Console;

use Ghanem\Dtone\Request;
use Illuminate\Console\Command;

class DtoneHealthCommand extends Command
{
    protected $signature = 'dtone:health';

    protected $description = 'Check DT One API connectivity and account status';

    public function handle()
    {
        $env = config('dtone.is_production') ? 'Production' : 'Sandbox';
        $this->info('Checking DT One API (' . $env . ')...');

        try {
            $balances = Request::balances();

            $this->info('API Connection: OK');

            if (!empty($balances)) {
                $rows = [];
                foreach ($balances as $balance) {
                    $rows[] = [
                        $balance['amount'] ?? 'N/A',
                        $balance['currency'] ?? 'N/A',
                    ];
                }
                $this->table(['Balance', 'Currency'], $rows);
            }

            // Try fetching services to verify read access
            $services = Request::services(1, 1);
            $total = $services['meta']['total'] ?? 'unknown';
            $this->info('Services available: ' . $total);

            $this->info('Health check passed!');

            return 0;
        } catch (\Exception $e) {
            $this->error('Health check failed: ' . $e->getMessage());
            return 1;
        }
    }
}
