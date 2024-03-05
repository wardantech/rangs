<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Ticket\Ticket;
use App\Models\Outlet\Outlet;

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
        return $this->belongsTo(Outlet::class, 'outlet_id')->withTrashed();
    }
}
