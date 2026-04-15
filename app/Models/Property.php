<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PropertyImage;
use Illuminate\Support\Str;

class Property extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'bedrooms',
        'bathrooms',
        'half_bathrooms',
        'parking_spots',
        'm2_construction',
        'm2_land',
        'address',
        'neighborhood',
        'type',
        'status',
        'contract_type',
        'active',
        'state',
        'city',
        'show_public_address',
        'is_featured',
        'seller_id',
        'client_id',
        'cp',
        'slug',
        'latitude',
        'longitude',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($property) {
            if (empty($property->slug)) {
                $property->slug = static::generateUniqueSlug($property->title);
            }
        });

        static::updating(function ($property) {
            if ($property->isDirty('title')) {
                $property->slug = static::generateUniqueSlug($property->title, $property->id);
            }
        });
    }

    /**
     * Lógica para garantizar que el slug sea único
     */
    private static function generateUniqueSlug(string $title, $currentId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (
            static::where('slug', $slug)
                ->when($currentId, function ($query, $currentId) {
                    return $query->where('id', '!=', $currentId);
                })
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function thumbnail()
    {
        return $this->hasOne(PropertyImage::class, 'property_id')->where('is_main', 1);
    }
}
