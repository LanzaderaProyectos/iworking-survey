<?php

namespace MattDaneshvar\Survey\Models;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use MattDaneshvar\Survey\Models\Section;
use Spatie\Translatable\HasTranslations;
use MattDaneshvar\Survey\Contracts\Answer;
use MattDaneshvar\Survey\Contracts\Survey;
use MattDaneshvar\Survey\Models\SurveyQuestion;
use MattDaneshvar\Survey\Contracts\Question as QuestionContract;

class Question extends Model implements QuestionContract
{

    use HasTranslations;
    public $incrementing = false;
    protected $keyType = 'string';

    public $translatable = ['content', 'options'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','type','section_type', 'options', 'content', 'rules', 'survey_id', 'section_id', 'original_id', 'parent_id', 'order', 'condition', 'code', 'disabled', 'mandatory', 'survey_type','disabled_at'];

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

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4();
        });

        //Ensure the question's survey is the same as the section it belongs to.
        static::creating(function (self $question) {
            //Section on pivot table
            // $question->load('section');

            // if ($question->section) {
            //     $question->survey_id = $question->section->survey_id;
            // }
        });

        static::deleting(function (self $question) {
            $question->subQuestions->each->delete();
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
        return $this->hasManyThrough(get_class(app()->make(Survey::class)), get_class(app()->make(SurveyQuestion::class)));
    }

    /**
     * The section the question belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->hasManyThrough(Section::class, SurveyQuestion::class, 'question_id', 'id', 'id', 'section_id');
    }

    /**
     * The answers that belong to the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasManyThrough(Answer::class, SurveyQuestion::class);
    }

    public function isMain()
    {
        return $this->original_id === $this->id && $this->parent_id == null;
    }

    public function isSub()
    {
        return $this->original_id !== $this->id && $this->parent_id !== null;
    }

    public function originalQuestion()
    {
        return $this->belongsTo(get_class(app()->make(Question::class)), 'original_id');
    }

    public function parentQuestion()
    {
        return $this->belongsTo(get_class(app()->make(Question::class)), 'parent_id');
    }

    /**
     * The answers that belong to the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subQuestions()
    {
        return $this->hasMany(get_class(app()->make(Question::class)), 'parent_id')->orderBy('created_at', 'asc');
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

    public function scopeFilters($query,$filters)
    {
        if(!empty($filters['code'])){
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }
        if(!empty($filters['name'])){
            $query->where('content', 'like', '%' . $filters['name'] . '%');
        }
        if(!empty($filters['type'])){
            $query->where('type', 'like', '%' . $filters['type'] . '%');
        }
        if(!empty($filters['survey_type'])){
            $query->where('form_type', 'like', '%' . $filters['survey_type'] . '%');
        }
        if(!empty($filters['created_from'])){
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if(!empty($filters['created_to'])){
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        if(!empty($filters['disabled'])){
            $query->where('disabled', $filters['disabled']);
        }
        if(!empty($filters['disabled_from'])){
            $query->whereDate('disabled_at', '>=', $filters['disabled_from']);
        }
        if(!empty($filters['disabled_to'])){
            $query->whereDate('disabled_at', '<=', $filters['disabled_to']);
        }

    }
}
