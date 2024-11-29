<?php

namespace MattDaneshvar\Survey;

use Livewire\Livewire;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use MattDaneshvar\Survey\Http\Livewire\Table;
use MattDaneshvar\Survey\Http\Livewire\Answers;
use MattDaneshvar\Survey\Http\Livewire\Addresses;
use MattDaneshvar\Survey\Http\Livewire\ShowEntry;
use MattDaneshvar\Survey\Http\Livewire\CreateSurvey;
use MattDaneshvar\Survey\Http\Middleware\UserSurvey;
use Illuminate\Contracts\View\Factory as ViewFactory;
use MattDaneshvar\Survey\Http\Livewire\EntryList;
use MattDaneshvar\Survey\Http\View\Composers\SurveyComposer;

class SurveyServiceProvider extends ServiceProvider
{
    /**
     * Boot the package.
     *
     * @param  ViewFactory  $viewFactory
     */
    public function boot(ViewFactory $viewFactory)
    {
        $this->registerRoutes();
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('user-survey', UserSurvey::class);
        $this->publishes([
            __DIR__ . '/../config/survey.php' => config_path('survey.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resources/views/' => base_path('resources/views/vendor/survey'),
        ], 'views');

         // Publishing is only necessary when using the CLI.
         if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
        
        $this->mergeConfigFrom(__DIR__ . '/../config/survey.php', 'survey');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'survey');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'survey');

        $viewFactory->composer('survey::standard', SurveyComposer::class);

        $this->publishMigrations([
            'create_surveys_table',
            'create_questions_table',
            'create_entries_table',
            'create_answers_table',
            'create_sections_table',
            'create_surveyeds_table'
        ]);

        $this->bootLivewireComponents();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Answer::class, \MattDaneshvar\Survey\Models\Answer::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Entry::class, \MattDaneshvar\Survey\Models\Entry::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Question::class, \MattDaneshvar\Survey\Models\Question::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Section::class, \MattDaneshvar\Survey\Models\Section::class);
        $this->app->bind(\MattDaneshvar\Survey\Contracts\Survey::class, \MattDaneshvar\Survey\Models\Survey::class);
        $this->mergeConfigFrom(__DIR__ . '/../config/iworking-survey.php', 'iworking-survey');
    }

    /**
     * Publish package migrations.
     *
     * @param $migrations
     */
    protected function publishMigrations($migrations)
    {
        foreach ($migrations as $migration) {
            $migrationClass = Str::studly($migration);

            if (class_exists($migrationClass)) {
                return;
            }

            $this->publishes([
                __DIR__ . "/../database/migrations/$migration.php.stub" => database_path('migrations/' . date(
                    'Y_m_d_His',
                    time()
                ) . "_$migration.php"),
            ], 'migrations');
        }
    }

     /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Generic json publish
        $this->publishes([
            __DIR__ . '/../resources/json'                              => public_path('json'),
        ], 'iworking-aside-json');
    }

    /**
     * @return void
     */
    protected function registerRoutes(): void
    {
        // $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    protected function bootLivewireComponents()
    {
        Livewire::component('iworking-survery::survey-list',    Table::class);
        Livewire::component('iworking-survery::create-survey',  CreateSurvey::class);
        Livewire::component('iworking-survery::survey-answers', Answers::class);
        Livewire::component('iworking-survery::show-entry',     ShowEntry::class);
        Livewire::component('iworking-survery::addresses',     Addresses::class);
        Livewire::component('iworking-survery::entry-list',     EntryList::class);
    }
}
