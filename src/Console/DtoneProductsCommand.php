<?php

namespace Ghanem\Dtone\Console;

use Ghanem\Dtone\Request;
use Illuminate\Console\Command;

class DtoneProductsCommand extends Command
{
    protected $signature = 'dtone:products
                            {--country= : Filter by country ISO code}
                            {--type= : Filter by product type}
                            {--service= : Filter by service ID}
                            {--page=1 : Page number}
                            {--per-page=10 : Items per page}';

    protected $description = 'List DT One products';

    public function handle()
    {
        $this->info('Fetching products...');

        $result = Request::products(
            $this->option('type'),
            $this->option('service') ? (int) $this->option('service') : null,
            $this->option('country'),
            [],
            (int) $this->option('page'),
            (int) $this->option('per-page')
        );

        $data = $result['data'] ?? [];

        if (empty($data)) {
            $this->warn('No products found.');
            return 0;
        }

        $rows = [];
        foreach ($data as $product) {
            $rows[] = [
                $product['id'] ?? 'N/A',
                $product['name'] ?? 'N/A',
                $product['type'] ?? 'N/A',
                isset($product['operator']['name']) ? $product['operator']['name'] : 'N/A',
            ];
        }

        $this->table(['ID', 'Name', 'Type', 'Operator'], $rows);

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
