<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\RouteListCommand as BaseRouteListCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'route:list')]
class RouteListCommand extends BaseRouteListCommand
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('name-only')) {
            $names = collect($this->getRoutes())
                ->pluck('name')
                ->filter()
                ->values();

            $this->output->writeln(
                $this->option('json') ? $names->toJson() : $names->implode(PHP_EOL)
            );

            return 0;
        }

        if ($columns = $this->option('columns')) {
            $routes = collect($this->getRoutes());

            if ($this->option('json')) {
                $this->output->writeln($routes->values()->toJson());

                return 0;
            }

            $this->renderColumnsTable($routes);

            return 0;
        }

        return parent::handle();
    }

    /**
     * Render the plucked route columns as a simple table.
     *
     * @param  \Illuminate\Support\Collection  $routes
     * @return void
     */
    protected function renderColumnsTable($routes)
    {
        $rows = $routes->map(function ($route) {
            return array_map(function ($value) {
                return is_string($value) && str_contains($value, "\n")
                    ? preg_replace('/\s*\n\s*/', ' ', $value)
                    : $value;
            }, $route);
        })->values()->all();

        $headers = $routes->isEmpty() ? [] : array_keys($routes->first());

        $table = new Table($this->output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        $table->render();
    }

    /**
     * Get the column names to show (lowercase table headers).
     *
     * @return array
     */
    protected function getColumns()
    {
        if ($columns = $this->option('columns')) {
            return $this->parseColumns([$columns]);
        }

        return parent::getColumns();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['columns', null, InputOption::VALUE_OPTIONAL, 'Columns to include in the output (comma separated)'],
            ['name-only', null, InputOption::VALUE_NONE, 'Output only the route names'],
        ]);
    }
}
