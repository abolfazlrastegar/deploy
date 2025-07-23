<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DeployProduction extends Command
{
	protected $signature = 'deploy:prod';

	public function handle()
	{
		$this->info("Deploying Production...");

		$projectPath = '/home/admin/domains/api.abolfazl.com/public_html';

		$branch = 'main';

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
