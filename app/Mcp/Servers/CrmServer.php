<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetContactTool;
use App\Mcp\Tools\ListOverdueFollowUpsTool;
use App\Mcp\Tools\LogFollowUpTool;
use App\Mcp\Tools\LogNoteTool;
use App\Mcp\Tools\SearchContactsTool;
use App\Mcp\Tools\UpdateContactOrMatterStatusTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;

#[Name('CRM Server')]
#[Version('1.0.0')]
#[Instructions('Migration Manager CRM MCP server. Authenticate with a Staff Sanctum Bearer token. Contacts are admins (clients/leads/companies). Follow-ups and notes live in the notes table. Matters are client_matters. Always search before updating. Non–super-admin staff only see assigned overdue actions and records they can access in the CRM.')]
class CrmServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<Tool>>
     */
    protected array $tools = [
        SearchContactsTool::class,
        GetContactTool::class,
        ListOverdueFollowUpsTool::class,
        LogNoteTool::class,
        LogFollowUpTool::class,
        UpdateContactOrMatterStatusTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<Server\Resource>>
     */
    protected array $resources = [
        //
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<Prompt>>
     */
    protected array $prompts = [
        //
    ];
}
