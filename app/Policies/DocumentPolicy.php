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
        // Super admin can view all
        if ($user->role === 1) {
            return true;
        }

        // Creator can view
        if ($document->created_by === $user->id) {
            return true;
        }

        // Signer can view
        if ($document->signers()->where('email', $user->email)->exists()) {
            return true;
        }

        // If document is associated with an entity the user owns/manages
        if ($document->documentable_type && $document->documentable_id) {
            // For Admin (client) associations
            if ($document->documentable_type === Admin::class) {
                // User can view if they're assigned to this client
                // or if it's their own record
                if ($document->documentable_id === $user->id) {
                    return true;
                }
                
                // Check if user is assigned to this client (you may need to adjust based on your assignment logic)
                // For now, allow if user has appropriate role
                if (in_array($user->role, [1, 2, 3])) { // Super Admin, Admin, Agent
                    return true;
                }
            }

            // For Lead associations
            if ($document->documentable_type === Lead::class) {
                $lead = Lead::find($document->documentable_id);
                if ($lead && $lead->user_id === $user->id) {
                    return true;
                }
            }
        }

        return false;
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
        // Super admin can update all
        if ($user->role === 1) {
            return true;
        }

        // Creator can update their own documents
        if ($document->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the document
     */
    public function delete(Admin $user, Document $document): bool
    {
        // Super admin can delete all
        if ($user->role === 1) {
            return true;
        }

        // Creator can delete their own documents (only if not signed)
        if ($document->created_by === $user->id && $document->status !== 'signed') {
            return true;
        }

        return false;
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
        // Super admin can send reminders for any document
        if ($user->role === 1) {
            return true;
        }

        // Creator can send reminders
        if ($document->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can void this document
     */
    public function void(Admin $user, Document $document): bool
    {
        // Super admin can void any document
        if ($user->role === 1) {
            return true;
        }

        // Creator can void their own documents (only if not signed)
        if ($document->created_by === $user->id && $document->status !== 'signed') {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can associate/detach documents
     */
    public function associate(Admin $user, Document $document): bool
    {
        // Super admin can associate any document
        if ($user->role === 1) {
            return true;
        }

        // Creator can associate their own documents
        if ($document->created_by === $user->id) {
            return true;
        }

        return false;
    }
}

