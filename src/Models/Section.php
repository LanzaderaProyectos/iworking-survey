<?php

namespace MattDaneshvar\Survey\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use MattDaneshvar\Survey\Contracts\Question;
use MattDaneshvar\Survey\Contracts\Section as SectionContract;

class Section extends Model implements SectionContract
{
    use HasTranslations;

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

        static::deleting(function (self $section) {
            $section->questions->each->delete();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

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
