<?php

namespace App\Console\Commands\Mcp;

use App\Models\Staff;
use Illuminate\Console\Command;

class IssueCrmMcpTokenCommand extends Command
{
    protected $signature = 'mcp:issue-crm-token
                            {email=ajay@bansalimmigration.com.au : Staff email to issue the token for}
                            {--name=mcp-crm : Sanctum token name}
                            {--revoke : Revoke existing tokens with the same name first}';

    protected $description = 'Issue a Sanctum personal access token for the CRM MCP endpoint (/mcp/crm)';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $tokenName = (string) $this->option('name');

        $staff = Staff::query()->where('email', $email)->first();

        if (! $staff) {
            $this->error("No staff found with email [{$email}].");

            return self::FAILURE;
        }

        if ((int) $staff->status !== 1) {
            $this->error("Staff [{$email}] is inactive (status={$staff->status}).");

            return self::FAILURE;
        }

        if ($this->option('revoke')) {
            $deleted = $staff->tokens()->where('name', $tokenName)->delete();
            $this->info("Revoked {$deleted} existing token(s) named [{$tokenName}].");
        }

        $plainText = $staff->createToken($tokenName)->plainTextToken;

        $this->newLine();
        $this->info('CRM MCP token created (shown once).');
        $this->line("Staff: {$staff->first_name} {$staff->last_name} (#{$staff->id}) role={$staff->role}");
        $this->line('Endpoint: '.url('/mcp/crm'));
        $this->line('Header: Authorization: Bearer <token>');
        $this->newLine();
        $this->line($plainText);
        $this->newLine();
        $this->warn('Sanctum expiration is currently '.((int) config('sanctum.expiration')).' minutes (~7 days). Store the token securely; it cannot be retrieved again.');

        return self::SUCCESS;
    }
}
