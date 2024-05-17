<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use MattDaneshvar\Survey\Contracts\Entry;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Contracts\Section;
use MattDaneshvar\Survey\Contracts\Survey as SurveyContract;

class Survey extends Model implements SurveyContract
{
    use HasTranslations;

    public $translatable = ['name'];

    /**
     * Survey constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (!isset($this->table)) {
            $this->setTable(config('survey.database.tables.surveys'));
        }

        parent::__construct($attributes);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
        'has_order' => 'boolean',
        'has_promotional_material' => 'boolean',
    ];

    /**
     * Boot the survey.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $survey) {
            $survey->sections->each->delete();
        });
    }

    /**
     * The survey parent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(get_class(app()->make(Survey::class)), 'parent_id');
    }


    /**
     * The survey original.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo(get_class(app()->make(Survey::class)), 'original_id');
    }
  

    /**
     * The survey sections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany(get_class(app()->make(Section::class)))->orderBy('order');
    }

    /**
     * The survey questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function surveyQuestions()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->orderBy('section_id');
    }

    /**
     * The survey main questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function surveyQuestionsMain()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->whereNull('parent_id')->orderBy('section_id');
    }


     /**
     * The survey questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function surveyQuestionsSub()
    {
        return $this->hasMany(get_class(app()->make(SurveyQuestion::class)))->whereNotNull('parent_id')->orderBy('section_id');
    }

    /**
     * The survey questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questions()
    {
        return $this->hasManyThrough(get_class(app()->make(Question::class)), get_class(app()->make(SurveyQuestion::class)), 'survey_id', 'id', 'id', 'question_id')->orderBy('section_id');
    }

    /**
     * The survey main questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mainQuestions()
    {
        return $this->hasManyThrough(get_class(app()->make(Question::class)), get_class(app()->make(SurveyQuestion::class)), 'survey_id', 'id', 'id', 'question_id')->whereNull('survey_questions.parent_id')->orderBy('section_id');
    }

    /**
     * The survey sub questions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subQuestions()
    {
        return $this->hasManyThrough(get_class(app()->make(Question::class)), get_class(app()->make(SurveyQuestion::class)), 'survey_id', 'id', 'id', 'question_id')->whereNotNull('survey_questions.parent_id')->orderBy('section_id');
    }

    /**
     * The survey type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function surveyType()
    {
        return $this->belongsTo(SurveyType::class, 'type');
    }

    /**
     * The survey entries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany(get_class(app()->make(Entry::class)));
    }

    public function user()
    {
        return $this->belongsTo(config('iworking-survey.user-model'), 'author');
    }

    public function audit()
    {
        return $this->morphMany('App\Models\Audit', 'auditable')->orderBy('created_at');
    }

    /**
     * Check if survey accepts guest entries.
     *
     * @return bool
     */
    public function acceptsGuestEntries()
    {
        return $this->settings['accept-guest-entries'] ?? false;
    }

    /**
     * The maximum number of entries a participant may submit.
     *
     * @return int|null
     */
    public function limitPerParticipant()
    {
        if ($this->acceptsGuestEntries()) {
            return;
        }

        $limit = $this->settings['limit-per-participant'] ?? 1;

        return $limit !== -1 ? $limit : null;
    }

    /**
     * Survey entries by a participant.
     *
     * @param  Model  $participant
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entriesFrom(Model $participant)
    {
        return $this->entries()->where('participant', $participant->id);
    }

    /**
     * Last survey entry by a participant.
     *
     * @param  Model  $participant
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lastEntry(Model $participant = null)
    {
        return $participant === null ? null : $this->entriesFrom($participant)->first();
    }

    /**
     * Check if a participant is eligible to submit the survey.
     *
     * @param  Model|null  $model
     * @return bool
     */
    public function isEligible(Model $participant = null)
    {
        if ($participant === null) {
            return $this->acceptsGuestEntries();
        }

        if ($this->limitPerParticipant() === null) {
            return true;
        }

        return $this->limitPerParticipant() > $this->entriesFrom($participant)->count();
    }

    /**
     * Combined validation rules of the survey.
     *
     * @return mixed
     */
    public function getRulesAttribute()
    {
        return $this->questions->mapWithKeys(function ($question) {
            return [$question->key => $question->rules];
        })->all();
    }

    /**
     * @param $query
     * @param $search
     * @return void
     */
    public function scopeTableSearch($query, $search): void
    {
        if (!empty($search['survey_number'])) {
            $value = $search['survey_number'];
            $query->where('survey_number', 'like', '%' . $value . '%');
        }

        if (!empty($search['name'])) {
            $value = $search['name'];
            $query->where('name', 'like', '%' . $value . '%');
        }

        if (!empty($search['author'])) {
            $value = $search['author'];
            $query->where('author', 'like', '%' . $value . '%');
        }
        if (isset($search['status']) && $search['status'] !== '') {
            $value = $search['status'];
            $query->where('status', $value);
        }
        if (isset($search['type']) && $search['type'] !== '') {
            $query->where('type', $search['type']);
        }
    }
}
