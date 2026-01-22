<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Permission Audit Model
 * 
 * Tracks all permission changes (who changed what, when)
 */
class PermissionAudit extends Model
{
    protected $fillable = [
        'admin_user_id',
        'action',
        'entity_type',
        'entity_id',
        'entity_name',
        'permissions_added',
        'permissions_removed',
        'permissions_count',
        'template_name',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'permissions_added' => 'array',
        'permissions_removed' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Admin user who made the change
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Get the entity (User or Role) that was modified
     */
    public function entity()
    {
        if ($this->entity_type === 'user') {
            return User::find($this->entity_id);
        } elseif ($this->entity_type === 'role') {
            return Role::find($this->entity_id);
        }
        
        return null;
    }

    /**
     * Create audit log for permission assignment
     */
    public static function logPermissionChange(
        string $action,
        string $entityType,
        int $entityId,
        ?string $entityName,
        array $permissionsAdded = [],
        array $permissionsRemoved = [],
        ?string $templateName = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'admin_user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'entity_name' => $entityName,
            'permissions_added' => $permissionsAdded,
            'permissions_removed' => $permissionsRemoved,
            'permissions_count' => count($permissionsAdded) + count($permissionsRemoved),
            'template_name' => $templateName,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get human-readable action name
     */
    public function getActionNameAttribute(): string
    {
        return match($this->action) {
            'assigned' => 'إضافة صلاحيات',
            'revoked' => 'إزالة صلاحيات',
            'synced' => 'تحديث صلاحيات',
            'template_applied' => 'تطبيق قالب',
            'bulk_assigned' => 'تحديث جماعي',
            'role_updated' => 'تحديث دور',
            default => $this->action,
        };
    }

    /**
     * Get formatted summary of changes
     */
    public function getSummaryAttribute(): string
    {
        $parts = [];
        
        if (!empty($this->permissions_added)) {
            $parts[] = 'إضافة ' . count($this->permissions_added) . ' صلاحية';
        }
        
        if (!empty($this->permissions_removed)) {
            $parts[] = 'إزالة ' . count($this->permissions_removed) . ' صلاحية';
        }
        
        if ($this->template_name) {
            $parts[] = "قالب: {$this->template_name}";
        }
        
        return implode(' | ', $parts) ?: 'لا توجد تغييرات';
    }
}
