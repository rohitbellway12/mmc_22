<?php

namespace Modules\BookingModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class BookingQuestionAnswer extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'booking_id',
        'provider_question_id',
        'answer_value'
    ];

    /**
     * Get the booking that owns the answer
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the question this answer belongs to
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(\Modules\ProviderManagement\Entities\ProviderQuestion::class, 'provider_question_id');
    }
}
