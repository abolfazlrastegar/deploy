<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DeployStaging extends Command
{
	protected $signature = 'deploy:staging';

	public function handle()
	{
		$this->info("Deploying Staging....");

		$projectPath = '/home/admin/domains/dev.abolfazl.com/public_html';
		$branch = 'developer';

		$this->info("🚀 Starting deployment for path: $projectPath on branch: $branch");

		$scriptPath = base_path('deploy-git/deploy.sh');

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
