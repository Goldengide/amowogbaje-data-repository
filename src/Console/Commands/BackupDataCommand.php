<?php

namespace Amowogbaje\DatabaseRepository\Console\Commands;

use Illuminate\Console\Command;
use Amowogbaje\DatabaseRepository\DataRepository;

class BackupDataCommand extends Command
{
    protected $signature = 'data:backup';
    protected $description = 'Backup all data tables';

    protected $dataRepository;

    public function __construct(DataRepository $dataRepository)
    {
        parent::__construct();
        $this->dataRepository = $dataRepository;
    }

    public function handle()
    {
        $this->dataRepository->backupAllDatas();
        $this->info('Data backup completed.');
    }
}
