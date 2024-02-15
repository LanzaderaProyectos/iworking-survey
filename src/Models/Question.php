<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use MattDaneshvar\Survey\Contracts\Answer;
use MattDaneshvar\Survey\Contracts\Survey;
use MattDaneshvar\Survey\Contracts\Section;
use MattDaneshvar\Survey\Contracts\Question as QuestionContract;

class Question extends Model implements QuestionContract
{

    use HasTranslations;

    public $translatable = ['content', 'options'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type', 'options', 'content', 'rules', 'survey_id', 'section_id', 'original_id', 'parent_id'];

    protected $casts = [
        'rules' => 'array',
        'options' => 'array',
    ];

    /**
     * Boot the question.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        //Ensure the question's survey is the same as the section it belongs to.
        static::creating(function (self $question) {
            $question->load('section');

            if ($question->section) {
                $question->survey_id = $question->section->survey_id;
            }
        });
    }

    /**
     * Question constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (!isset($this->table)) {
            $this->setTable(config('survey.database.tables.questions'));
        }

        parent::__construct($attributes);
    }

    /**
     * The survey the question belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function survey()
    {
        return $this->belongsTo(get_class(app()->make(Survey::class)));
    }

    /**
     * The section the question belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(get_class(app()->make(Section::class)));
    }

    /**
     * The answers that belong to the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(get_class(app()->make(Answer::class)));
    }

    public function isMain()
    {
        return $this->original_id === $this->id && $this->parent_id == null;
    }

    public function isSub()
    {
        return $this->original_id !== $this->id && $this->parent_id !== null;
    }

    public function originalQuestion(){
        return $this->belongsTo(get_class(app()->make(Question::class)), 'original_id');
    }

    public function parentQuestion(){
        return $this->belongsTo(get_class(app()->make(Question::class)), 'parent_id');
    }

     /**
     * The answers that belong to the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subQuestions()
    {
        return $this->hasMany(get_class(app()->make(Question::class)), 'parent_id');
    }

    /**
     * The question's validation rules.
     *
     * @param $value
     * @return array|mixed
     */
    public function getRulesAttribute($value)
    {
        $value = $this->castAttribute('rules', $value);

        return $value !== null ? $value : [];
    }

    /**
     * The unique key representing the question.
     *
     * @return string
     */
    public function getKeyAttribute()
    {
        return "q{$this->id}";
    }

    /**
     * Scope a query to only include questions that
     * don't belong to any sections.
     *
     * @param $query
     * @return mixed
     */
    public function scopeWithoutSection($query)
    {
        return $query->where('section_id', null);
    }
}
