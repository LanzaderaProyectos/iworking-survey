<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use MattDaneshvar\Survey\Contracts\Answer;
use MattDaneshvar\Survey\Contracts\Entry as EntryContract;
use MattDaneshvar\Survey\Contracts\Survey;
use MattDaneshvar\Survey\Exceptions\GuestEntriesNotAllowedException;
use MattDaneshvar\Survey\Exceptions\MaxEntriesPerUserLimitExceeded;
use MattDaneshvar\Survey\Models\Answer as ModelsAnswer;
use App\Models\EntryOrder;
use App\Models\EntryPromotionalMaterial;

class Entry extends Model implements EntryContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['survey_id', 'participant', 'participant_type', 'lang', 'status','assigned_user_id'];

    protected $appends = ['sum_score'];

    /**
     * Boot the entry.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        //Prevent submission of entries that don't meet the parent survey's constraints.
        static::creating(function (self $entry) {
            $entry->validateParticipant();
            $entry->validateMaxEntryPerUserRequirement();
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function element(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'participant_type', 'participant');
    }

    /**
     * Entry constructor.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (!isset($this->table)) {
            $this->setTable(config('survey.database.tables.entries'));
        }

        parent::__construct($attributes);
    }

    /**
     * The answers within the entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(get_class(app()->make(Answer::class)));
    }

    /**
     * The survey the entry belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function survey()
    {
        return $this->belongsTo(get_class(app()->make(Survey::class)));
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    // /**
    //  * The participant that the entry belongs to.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  */
    // public function participant()
    // {
    //     return $this->belongsTo(User::class, 'participant');
    // }

    public function surveyed()
    {
        return $this->belongsTo(Surveyed::class, 'participant', 'email');
    }

    public function orders()
    {
        return $this->hasMany(EntryOrder::class, 'entry_id', 'id');
    }

    public function promotionalMaterials()
    {
        return $this->hasMany(EntryPromotionalMaterial::class, 'entry_id', 'id');
    }

    /**
     * Set the survey the entry belongs to.
     *
     * @param  Survey  $survey
     * @return $this
     */
    public function for(Survey $survey)
    {
        $this->survey()->associate($survey);

        return $this;
    }

    /**
     * Set the participant who the entry belongs to.
     *
     * @param  Model|null  $model
     * @return $this
     */
    public function by(Model $model = null)
    {
        $this->participant()->associate($model);

        return $this;
    }

    /**
     * Create an entry from an array.
     *
     * @param  array  $values
     * @return $this
     */
    public function fromArray(array $values)
    {
        foreach ($values as $key => $value) {
            if ($value === null) {
                continue;
            }

            $answer_class = get_class(app()->make(Answer::class));

            if (gettype($value) === 'array') {
                $value = implode(', ', $value);
            }

            $this->answers->add($answer_class::make([
                'question_id' => substr($key, 1),
                'entry_id' => $this->id,
                'value' => $value,
            ]));
        }

        return $this;
    }

    /**
     * The answer for a given question.
     *
     * @param  Question  $question
     * @return mixed|null
     */
    public function answerFor(Question $question)
    {
        $answer = $this->answers()->where('question_id', $question->id)->first();

        return isset($answer) ? $answer->value : null;
    }

    /**
     * Save the model and all of its relationships.
     * Ensure the answers are automatically linked to the entry.
     *
     * @return bool
     */
    public function push()
    {
        $this->save();

        foreach ($this->answers as $answer) {
            $answer->entry_id = $this->id;
        }

        return parent::push();
    }

    /**
     * Validate participant's legibility.
     *
     * @throws GuestEntriesNotAllowedException
     */
    public function validateParticipant()
    {
        if ($this->survey->acceptsGuestEntries()) {
            return;
        }

        if ($this->participant !== null) {
            return;
        }

        throw new GuestEntriesNotAllowedException();
    }

    /**
     * Validate if entry exceeds the survey's
     * max entry per participant limit.
     *
     * @throws MaxEntriesPerUserLimitExceeded
     */
    public function validateMaxEntryPerUserRequirement()
    {
        $limit = $this->survey->limitPerParticipant();

        if ($limit === null) {
            return;
        }

        $count = static::where('participant', $this->participant)
            ->where('survey_id', $this->survey->id)
            ->count();

        if ($count >= $limit) {
            throw new MaxEntriesPerUserLimitExceeded();
        }
    }

    public function getSumScoreAttribute()
    {
        return $this->answers->sum('score');
    }

    public function scopeTableSearch($query, $search): void
    {
        if (!empty($search['surveyed'])) {
            $value = $search['surveyed'];
            $query->whereHas('surveyed', function ($q) use ($value) {
                $q->where('name', 'like', '%' . $value . '%');
            });
        }

        if (!empty($search['manager'])) {
            $value = $search['manager'];
            $query->whereHas('surveyed', function ($q) use ($value) {
                $q->where('manager', 'like', '%' . $value . '%');
            });
        }

        if (!empty($search['status'])) {
            $value = $search['status'];
            $query->where('status', $value);
        }

        if (!empty($search['min'])) {
            $value = $search['min'];
            // $query->where('status', $value);
            $query->whereHas('answers', function ($q) use ($value) {
                $q->select(\DB::raw('SUM(score) as totalScore'))
                    ->havingRaw('totalScore >= ?', [$value]);
            });
        }

        if (!empty($search['max'])) {
            $value = $search['max'];
            $query->whereHas('answers', function ($q) use ($value) {
                $q->select(\DB::raw('SUM(score) as totalScore'))
                    ->havingRaw('totalScore <= ?', [$value]);
            });
        }
    }
}
