<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    public $dates = [
        'dob', 'joining_date', 'leaving_date'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'middle_name', 'username', 'email', 'personal_email', 'password', 'dob', 'avatar', 'address',
        'mobile_no', 'skype', 'trello', 'slack', 'github', 'twitter', 'linkedin', 'weekly_hours_credit', 'base_salary',
        'joining_date', 'leaving_date', 'is_active', 'remarks'
    ];

    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get avatar url
     *
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return asset($this->avatar);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany(UserAttendance::class);
    }

    /**
     * @return UserAttendance|object|null
     */
    public function getTodayAttendanceAttribute()
    {
        return $this->attendanceOfTheDay(today());
    }

    /**
     * @param $date
     * @return UserAttendance|null|object
     */
    public function attendanceOfTheDay($date)
    {
        return $this->attendance()
            ->where('date', $date)
            ->first();
    }

    /**
     * @return UserAttendance|object|null
     */
    public function getWeekAttendancesAttribute()
    {
        $days = generateDateRange(today()->startOfWeek(), today()->endOfWeek(), '1 day');
        $attandances = $this->attendance()
            ->where('date', '>=', today()->startOfWeek())
            ->get();

        return collect($days)->map(function (Carbon $day) use($attandances) {
            $attendance = $attandances->where('date', $day->format('Y-m-d'))->first();

            return [
                'id' => $attendance ? $attendance->getKey() : null,
                'date' => $day->format('Y-m-d'),
                'hours' => $attendance ? $attendance->hours : 0,
                'is_on_leave' => $attendance && $attendance->is_on_leave,
            ];
        });
    }

    /**
     * @return UserAttendance|\Illuminate\Database\Eloquent\Model
     */
    public function firstOrCreateAttendance()
    {
        if ($attendance = $this->today_attendance) {
            return $attendance;
        }

        return $this->attendance()->create([
            'date' => today()
        ]);
    }

    /**
     * Get recent notifications
     *
     * @return mixed
     */
    public function getRecentNotifications()
    {
        return $this->notifications()->take(5)->get();
    }

    /**
     * Detect is user object is mine
     */
    public function isMe()
    {
        return $this->id == auth()->id();
    }

    public function isAdmin()
    {
        return in_array($this->email, config('rm.admin'));;
    }


}
