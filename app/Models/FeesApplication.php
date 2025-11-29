<?php

namespace App\Models;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

class FeesApplication extends Model
{
    protected $table = 'fees_applications';

    protected $guarded  = [];
    
    protected $casts = [
        'action_completed' => 'boolean',
        'action_completed_at' => 'datetime',
        'action_details' => 'array',
    ];

    /**
     * Get the invoice for this application
     */
    public function invoice()
    {
        return $this->morphOne(Invoice::class, 'refference','reference_model');
    }
    
    /**
     * Get the configuration for this application
     */
    public function configuration()
    {
        return $this->belongsTo(FeesConfiguration::class, 'configuration_id');
    }
    
    /**
     * Check if post-payment action is pending
     */
    public function isActionPending()
    {
        return $this->status === 'Paid' && !$this->action_completed && $this->type !== 'general';
    }
    
    /**
     * Mark action as completed
     */
    public function markActionCompleted(array $details = [])
    {
        $this->update([
            'action_completed' => true,
            'action_completed_at' => now(),
            'action_details' => $details,
        ]);
    }
    
    /**
     * Scope to get applications with pending actions
     */
    public function scopePendingActions($query)
    {
        return $query->where('status', 'Paid')
                     ->where('action_completed', false)
                     ->whereNotNull('type')
                     ->where('type', '!=', 'general');
    }
}
