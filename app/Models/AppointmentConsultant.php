<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AppointmentConsultant extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'appointment_consultants';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'calendar_type',
        'location',
        'specializations',
        'is_active',
        'show_in_filter',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'specializations' => 'array',
            'is_active' => 'boolean',
            'show_in_filter' => 'boolean',
        ];
    }

    /**
     * Get all appointments for this consultant.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(BookingAppointment::class, 'consultant_id');
    }

    /**
     * Get upcoming appointments for this consultant.
     */
    public function upcomingAppointments(): HasMany
    {
        return $this->hasMany(BookingAppointment::class, 'consultant_id')
            ->where('appointment_datetime', '>=', now())
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->orderBy('appointment_datetime');
    }

    /**
     * Scope: Active consultants only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Consultants included in CRM appointment list filter and calendar transfer dropdowns.
     * Set show_in_filter to false on a row to hide it from those UIs (rare).
     */
    public function scopeShownInFilter($query)
    {
        return $query->where('show_in_filter', true);
    }

    /**
     * Scope: By calendar type
     */
    public function scopeByCalendarType($query, string $type)
    {
        return $query->where('calendar_type', $type);
    }

    /**
     * Scope: Melbourne consultants
     */
    public function scopeMelbourne($query)
    {
        return $query->where('location', 'melbourne');
    }

    /**
     * Scope: Adelaide consultants
     */
    public function scopeAdelaide($query)
    {
        return $query->where('location', 'adelaide');
    }

    /**
     * Get calendar type display name
     */
    protected function calendarTypeDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->calendar_type) {
                'paid' => 'Employer Sponsored Calendar',
                'jrp' => 'JRP/Skill Assessment',
                'education' => 'Education/Student Visa',
                'tourist' => 'Tourist Visa',
                'adelaide' => 'Adelaide Office',
                'ajay' => 'Ajay Calendar',
                'kunal' => 'Kunal Calendar',
                'arun' => 'Arun Calendar',
                default => ucfirst($this->calendar_type)
            }
        );
    }

    /**
     * CRM-facing label: use calendar type title for employer-sponsored (paid); for hidden rows use type title;
     * otherwise use the consultant name (e.g. team calendars).
     */
    protected function crmDisplayLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->calendar_type === 'paid') {
                    return $this->calendar_type_display;
                }

                if ($this->show_in_filter === false) {
                    return $this->calendar_type_display;
                }

                return $this->name;
            }
        );
    }

    /**
     * Get location display name
     */
    protected function locationDisplay(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->location) {
                'melbourne' => 'Melbourne',
                'adelaide' => 'Adelaide',
                default => ucfirst($this->location)
            }
        );
    }

    /**
     * Check if consultant handles specific NOE ID
     */
    public function handlesNoeId(int $noeId): bool
    {
        return in_array($noeId, $this->specializations ?? []);
    }

    /**
     * Get appointments count for today (method, not accessor to avoid N+1)
     */
    public function getTodayAppointmentsCount(): int
    {
        return $this->appointments()
            ->whereDate('appointment_datetime', today())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->count();
    }

    /**
     * Get upcoming appointments count (method, not accessor to avoid N+1)
     */
    public function getUpcomingAppointmentsCount(): int
    {
        return $this->appointments()
            ->where('appointment_datetime', '>=', now())
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->count();
    }

    /**
     * Get specialization names
     */
    public function getSpecializationNames(): array
    {
        $noeNames = [
            1 => 'TR (Temporary Residency)',
            2 => 'JRP',
            3 => 'Skill Assessment',
            4 => 'Tourist Visa',
            5 => 'Education/Student',
            6 => 'PR Complex',
            7 => 'Other Services',
            8 => 'Other Services',
        ];

        return array_map(
            fn($noeId) => $noeNames[$noeId] ?? "Service $noeId",
            $this->specializations ?? []
        );
    }
}

