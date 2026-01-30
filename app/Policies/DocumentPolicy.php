<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Document;
use App\Models\Lead;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any documents
     */
    public function viewAny(Admin $user): bool
    {
        // All authenticated admin users can view documents
        return true;
    }

    /**
     * Determine if the user can view the document
     */
    public function view(Admin $user, Document $document): bool
    {
        // Global access - everyone can view all documents
        return true;
    }

    /**
     * Determine if the user can create documents
     */
    public function create(Admin $user): bool
    {
        // All staff members can create documents (not client portal users)
        return $user->role !== 7;
    }

    /**
     * Determine if the user can update the document
     */
    public function update(Admin $user, Document $document): bool
    {
        // Global access - everyone can update all documents
        return true;
    }

    /**
     * Determine if the user can delete the document
     */
    public function delete(Admin $user, Document $document): bool
    {
        // Global access - everyone can delete documents (only if not signed)
        return $document->status !== 'signed';
    }

    /**
     * Determine if the user can view all documents (admin-only view)
     */
    public function viewAll(Admin $user): bool
    {
        // Only super admins can view all documents
        return $user->role === 1;
    }

    /**
     * Determine if the user can send reminders for this document
     */
    public function sendReminder(Admin $user, Document $document): bool
    {
        // Global access - everyone can send reminders for any document
        return true;
    }

    /**
     * Determine if the user can void this document
     */
    public function void(Admin $user, Document $document): bool
    {
        // Global access - everyone can void documents (only if not signed)
        return $document->status !== 'signed';
    }

    /**
     * Determine if the user can associate/detach documents
     */
    public function associate(Admin $user, Document $document): bool
    {
        // Global access - everyone can associate/detach documents
        return true;
    }
}

