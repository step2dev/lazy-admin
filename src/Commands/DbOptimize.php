<?php

namespace Step2dev\LazyAdmin\Commands;

use Illuminate\Console\Command as BaseCommand;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use RuntimeException;

final class DbOptimize extends BaseCommand
{
    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Optimize table/s of the database';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'Table optimizer for database';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize
                        {--database=default : Default database is set in the config. Database that needs to be optimized.}
                        {--table=* : Defaulting to all tables in the default database.}';

    private $progress;

    public function __construct(private readonly Builder $db)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Starting Optimization.');
        $this->getTables()
            ->tap(fn ($collection) => $this->progress = $this->output->createProgressBar($collection->count()))
            ->each(fn ($table) => $this->optimize($table));
        $this->info(PHP_EOL.'Optimization Completed');
    }

    /**
     * Get all the tables that need to the optimized
     */
    private function getTables(): Collection
    {
        $tableList = collect($this->option('table'));
        if ($tableList->isEmpty()) {
            return $this->db
                ->newQuery()
                ->selectRaw('TABLE_NAME')
                ->fromRaw('INFORMATION_SCHEMA.TABLES')
                ->whereRaw("TABLE_SCHEMA = '{$this->getDatabase()}'")
                ->whereRaw("TABLE_TYPE = 'BASE TABLE'")
                ->whereRaw("ENGINE = 'InnoDB'")
                ->whereRaw("TABLE_NAME NOT LIKE 'pma%'")
                ->pluck('TABLE_NAME');
        }
        // Check if the table exists
        if ($this->existsTables($tableList)) {
            return $tableList;
        }
        throw new RuntimeException("One or more tables provided doesn't exists.");
    }

    /**
     * Get database which need optimization
     */
    protected function getDatabase(): string
    {
        $database = $this->option('database');

        if ($database === 'default') {
            $database = config('database.default');

            return config('database.connections.'.$database.'.database');
        }
        // Check if the database exists
        if (is_string($database) && $this->existsDatabase($database)) {
            return $database;
        }
        throw new RuntimeException("This database {$database} doesn't exists.");
    }

    /**
     * Check if the database exists
     */
    private function existsDatabase(string &$databaseName): bool
    {
        return (bool) $this->db
            ->newQuery()
            ->selectRaw('SCHEMA_NAME')
            ->fromRaw('INFORMATION_SCHEMA.SCHEMATA')
            ->whereRaw("SCHEMA_NAME = '{$databaseName}'")
            ->count();
    }

    /**
     * Check if the table exists
     */
    private function existsTables(Collection $tables): bool
    {
        return $this->db
            ->newQuery()
            ->from('INFORMATION_SCHEMA.TABLES')
            ->where('TABLE_SCHEMA', $this->getDatabase())
            ->whereIn('TABLE_NAME', $tables)
            ->count() === $tables->count();
    }

    /**
     * Optimize the table
     */
    protected function optimize(string $table): void
    {
        $result = $this->db->getConnection()->select("OPTIMIZE TABLE `{$table}`");

        if (collect($result)->pluck('Msg_text')->contains('OK')) {
            $this->progress->advance();
        }
    }
}
