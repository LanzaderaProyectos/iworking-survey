<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use MattDaneshvar\Survey\Contracts\Survey as SurveyContract;

class Surveyed extends Model implements SurveyContract
{

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
