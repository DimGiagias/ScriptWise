<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Use the 'slug' column for route model binding instead of the 'id'.
     * Example: Route::get('/courses/{course}', ...) will find by the slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * A Course has many Modules.
     * Orders modules by the 'order' column.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }
}
