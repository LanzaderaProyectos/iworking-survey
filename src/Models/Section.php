<?php

namespace MattDaneshvar\Survey\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use MattDaneshvar\Survey\Contracts\Question;
use MattDaneshvar\Survey\Contracts\Section as SectionContract;

class Section extends Model implements SectionContract
{
    use HasTranslations;
    public $incrementing = false;
    protected $keyType = 'string';

    public $translatable = ['name'];
    
    /**
     * Section constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (! isset($this->table)) {
            $this->setTable(config('survey.database.tables.sections'));
        }

        parent::__construct($attributes);
    }

    
    /**
     * Boot the section.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::uuid4();
        });

        static::deleting(function (self $section) {
            $section->questions->each->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','survey_id','name'];


     /**
     * The survey questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function surveyQuestions()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->where('disabled','!=',true)->orderBy('position','asc');
    }

    /**
     * The survey main questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function surveyQuestionsMain()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->where('disabled','!=',true)->whereNull('parent_id')->orderBy('position','asc');
    }

    public function surveyQuestionsMainIgnoreDisabled()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->whereNull('parent_id')->orderBy('position','asc');
    }

    /**
     * The survey sub questions.
     */
    public function surveyQuestionsSub()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->where('disabled','!=',true)->whereNotNull('parent_id')->orderBy('position','asc');
    }

    public function surveyQuestionsSubIgnoreDisabled()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->whereNotNull('parent_id')->orderBy('position','asc');
    }

    /**
     * The questions of the section.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasMany(get_class(app()->make(Question::class)));
    }

    /**
     * The section main questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mainQuestions()
    {
        return $this->hasMany(get_class(app()->make(Question::class)))->whereNull('parent_id')->orderBy('section_id');
    }
}
