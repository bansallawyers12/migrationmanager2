<?php

namespace App\Support;

use App\Enums\PortalTaskType;

class WorkflowPortalTasklistDefaults
{
    public const WORKFLOW_ID = 11;

    /**
     * Client-facing portal tasks keyed by CRM stage name.
     *
     * @return array<string, list<array{name: string, description: ?string, task_type: string, allow_client: bool, is_required: bool}>>
     */
    public static function byStageName(int $workflowId): array
    {
        if ($workflowId !== self::WORKFLOW_ID) {
            return [];
        }

        return [
            'Awaiting Client Action' => [
                [
                    'name' => 'Service agreement',
                    'description' => 'Review and sign · 4 pages',
                    'task_type' => PortalTaskType::Sign->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
                [
                    'name' => 'Form 956 — appointment of agent',
                    'description' => 'Review and sign · 2 pages',
                    'task_type' => PortalTaskType::Sign->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
                [
                    'name' => 'Initial payment',
                    'description' => 'Pay in app or upload receipt',
                    'task_type' => PortalTaskType::Pay->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
            ],
            'Documents Review & Completion' => [
                [
                    'name' => 'Bank statement',
                    'description' => 'Last 3 months · PDF or photo',
                    'task_type' => PortalTaskType::Upload->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
                [
                    'name' => 'Passport bio page',
                    'description' => 'Clear colour scan',
                    'task_type' => PortalTaskType::Upload->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
                [
                    'name' => 'English test result',
                    'description' => 'IELTS / PTE certificate',
                    'task_type' => PortalTaskType::Upload->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
            ],
            'Client Draft Approval' => [
                [
                    'name' => 'Draft application',
                    'description' => 'Read and approve · or request changes',
                    'task_type' => PortalTaskType::Sign->value,
                    'allow_client' => true,
                    'is_required' => true,
                ],
            ],
        ];
    }
}
