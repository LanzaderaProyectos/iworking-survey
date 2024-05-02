<?php
namespace MattDaneshvar\Survey\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyType extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $fillable = [
        'name',
        'description',
        'default_sections',
        'has_order',
        'has_promotional_material'
    ];

    protected $casts = [
        'default_sections' => 'array',
    ];

    public function surveys()
    {
        return $this->hasMany(Survey::class,'type');
    }

}
