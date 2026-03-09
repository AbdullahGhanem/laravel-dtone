<?php

namespace Ghanem\Dtone\Console;

use Ghanem\Dtone\Request;
use Illuminate\Console\Command;

class DtoneTransactionCommand extends Command
{
    protected $signature = 'dtone:transaction
                            {id? : Transaction ID to look up}
                            {--page=1 : Page number (for listing)}
                            {--per-page=10 : Items per page (for listing)}';

    protected $description = 'List transactions or get a transaction by ID';

    public function handle()
    {
        $id = $this->argument('id');

        if ($id) {
            return $this->showTransaction((int) $id);
        }

        return $this->listTransactions();
    }

    /**
     * @return int
     */
    private function showTransaction(int $id)
    {
        $this->info('Fetching transaction #' . $id . '...');

        $transaction = Request::transactionById($id);

        if (empty($transaction)) {
            $this->error('Transaction not found.');
            return 1;
        }

        $this->table(['Field', 'Value'], [
            ['ID', $transaction['id'] ?? 'N/A'],
            ['External ID', $transaction['external_id'] ?? 'N/A'],
            ['Status', $transaction['status'] ?? 'N/A'],
            ['Product ID', $transaction['product_id'] ?? ($transaction['product']['id'] ?? 'N/A')],
            ['Created At', $transaction['creation_date'] ?? 'N/A'],
        ]);

        return 0;
    }

    /**
     * @return int
     */
    private function listTransactions()
    {
        $this->info('Fetching transactions...');

        $result = Request::transactions(
            (int) $this->option('page'),
            (int) $this->option('per-page')
        );

        $data = $result['data'] ?? [];

        if (empty($data)) {
            $this->warn('No transactions found.');
            return 0;
        }

        $rows = [];
        foreach ($data as $transaction) {
            $rows[] = [
                $transaction['id'] ?? 'N/A',
                $transaction['external_id'] ?? 'N/A',
                $transaction['status'] ?? 'N/A',
                $transaction['creation_date'] ?? 'N/A',
            ];
        }

        $this->table(['ID', 'External ID', 'Status', 'Created At'], $rows);

        $meta = $result['meta'] ?? [];
        if ($meta) {
            $this->line(sprintf(
                'Page %s of %s (Total: %s)',
                $meta['page'] ?? '?',
                $meta['total_pages'] ?? '?',
                $meta['total'] ?? '?'
            ));
        }

        return 0;
    }
}
