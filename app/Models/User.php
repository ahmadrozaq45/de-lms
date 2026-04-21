<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi Eloquent
    // Relasi untuk guru yang mengajar kursus
    public function teacherCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // Relasi untuk siswa yang mendaftar ke kursus
    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    public function grades(): HasMany 
    {
        return $this->hasMany(Grade::class);
    }
}
