<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use MattDaneshvar\Survey\Contracts\Answer as AnswerContract;
use MattDaneshvar\Survey\Contracts\Entry;
use MattDaneshvar\Survey\Contracts\Question;

class SurveyQuestion extends Model 
{
    /**
     * Answer constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (!isset($this->table)) {
            $this->setTable(config('survey.database.tables.survey_questions'));
        }

        parent::__construct($attributes);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question_id',
        'survey_id',
        'position',
        'parent_id',
        'original_id',
        'section_id',
        'condition',
        'mandatory',
        'target',
        'indicated',
        'order',
        'disabled',
        'disabled_by',
        'disabled_at'
    ];

    protected $casts = [
        'mandatory' => 'boolean',
        'indicated' => 'boolean',
        'target'    => 'boolean',

    ];

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function survey()
    {
        return $this->belongsTo(get_class(app()->make(Survey::class)));
    }

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function children()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)),'parent_id')->where('disabled','!=',true)->orderBy('position','asc');
    }

    /**
     * The question the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(get_class(app()->make(Question::class)));
    }

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(get_class(app()->make(Section::class)));
    }

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(get_class(app()->make(SurveyQuestion::class)),'parent_id');
    }

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo(get_class(app()->make(SurveyQuestion::class)),'original_id');
    }

    /**
     * The entry the answer belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function answers()
    {
        return $this->hasMany(get_class(app()->make(Answer::class)),'question_id');
    }
}
