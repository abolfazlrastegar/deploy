<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DeployFront extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'front:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
	    $this->info("Deploying front...");

	    $projectPath = '/home/admin/domains/abolfazl.com/public_html';
	    $branch = 'main';

	    $this->info("🚀 Starting deployment for path: $projectPath on branch: $branch");

	    $scriptPath = base_path('deploy-git/front-deploy.sh');

	    if (!file_exists($scriptPath)) {
		    $this->error("❌ Script not found at: $scriptPath");
		    return Command::FAILURE;
	    }

	    $process = Process::fromShellCommandline("bash $scriptPath $projectPath $branch");

	    $process->run(function ($type, $buffer) {
		    echo $buffer;
	    });

	    if (!$process->isSuccessful()) {
		    $this->error('❌ Deployment failed.');
		    return Command::FAILURE;
	    }

	    $this->info('✅ Deployment completed successfully.');
	    return Command::SUCCESS;
    }
}
