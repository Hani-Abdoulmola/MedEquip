<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserType extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    /**
     * الحقول القابلة للتعبئة
     */
    protected $fillable = ['name', 'slug', 'description'];

    /**
     * التحويلات (Casts)
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    /**
     * العلاقة: نوع المستخدم يمتلك عدة مستخدمين
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id');
    }

    /**
     * Mutator لتوليد slug تلقائيًا من الاسم
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = $value ?: Str::slug($this->name);
    }
}
