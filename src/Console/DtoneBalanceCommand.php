<?php

namespace Ghanem\Dtone\Console;

use Ghanem\Dtone\Request;
use Illuminate\Console\Command;

class DtoneBalanceCommand extends Command
{
    protected $signature = 'dtone:balance';

    protected $description = 'Display DT One account balances';

    public function handle()
    {
        $this->info('Fetching balances...');

        $balances = Request::balances();

        if (empty($balances)) {
            $this->warn('No balances found.');
            return 0;
        }

        $rows = [];
        foreach ($balances as $balance) {
            $rows[] = [
                $balance['amount'] ?? 'N/A',
                $balance['currency'] ?? 'N/A',
            ];
        }

        $this->table(['Amount', 'Currency'], $rows);

        return 0;
    }
}
