<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use Iworking\IworkingBoilerplate\Traits\AutoGenerateUuid;
use MattDaneshvar\Survey\Contracts\Survey as SurveyContract;

class Surveyed extends Model implements SurveyContract
{
    use AutoGenerateUuid;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'survey_id',
        'name',
        'vat_number',
        'contact_person',
        'email',
        'lang',
        'manager'
    ];
}
