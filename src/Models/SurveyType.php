<?php
namespace MattDaneshvar\Survey\Models;


use App\Models\ProjectType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Iworking\IworkingBoilerplate\Traits\AutoGenerateUuid;

class SurveyType extends Model
{
    use HasFactory, SoftDeletes;
    use AutoGenerateUuid;
    public $incrementing = false;
    protected $keyType = 'string';

    
    protected $fillable = [
        'id',
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
    public function projectType()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

}
