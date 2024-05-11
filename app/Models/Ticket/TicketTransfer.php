<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ticket\Ticket;
use App\Models\Outlet\Outlet;
use App\Models\User;

class TicketTransfer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'recommended_outlet_id')->withTrashed();
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
