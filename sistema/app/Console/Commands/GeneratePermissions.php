<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class       GeneratePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate_model_permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissionServce = new PermissionService();
        $permissionServce->updatePermissions();

        return Command::SUCCESS;
    }
}
