<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Section;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Models\SurveyType;
use MattDaneshvar\Survey\Mail\UserNotification;

class SurveyService
{
    public function saveSurvey(Survey $survey, $authorId, $surveyName, $surveyStatus, $auditText = null, $newSurvey = false)
    {
        try {
            $survey->author             = $authorId;
            if ($newSurvey) {
                $lastSurveyNumber       = Survey::where('survey_number', '!=', null)->orderBy('survey_number', 'desc')->first();
                $survey->survey_number  = ($lastSurveyNumber && $lastSurveyNumber->survey_number > 0) ? $lastSurveyNumber->survey_number + 1 : 10000;
            }
            $survey
                ->setTranslation('name', 'es', $surveyName['es'])
                ->setTranslation('name', 'en', $surveyName['en']);

            $survey->save();

            if ($newSurvey) {
                $survey->status = $surveyStatus;
                $survey->audit()->create([
                    'user_id'   => auth()->id(),
                    'status'    => $surveyStatus,
                    'text'      => $auditText
                ]);
            }

            return true;
        } catch (\Throwable $th) {
            dd($th);
            Log::error($th);
            return false;
        }
    }

    public function sendSurvey($users, Survey $survey, $audit = false, $auditText = null)
    {
        try {

            foreach ($users as $user) {
                try {
                    Mail::mailer('custom')->to($user->email)->send(new UserNotification($survey, $user));
                    Entry::create([
                        'survey_id' => $survey->id,
                        'participant' => $user->email,
                        'lang' => $user->lang,
                        'status' => Constants::ENTRY_STATUS_PENDING
                    ]);
                } catch (\Exception $e) {
                    $survey->audit()->create([
                        'user_id'   => auth()->id(),
                        'status'    => Constants::SURVEY_STATUS_SEND_ERROR,
                        'text'      => $user->email
                    ]);
                    Log::error($e);
                }
            }

            $survey->status = 1;
            $survey->save();

            if ($audit) {
                $survey->audit()->create([
                    'user_id'   => auth()->id(),
                    'status'    => Constants::SURVEY_STATUS_PROCESS,
                    'text'      => $auditText
                ]);
            }

            return true;
        } catch (\Throwable $th) {
            Log::error($th);

            return false;
        }
    }

    public function closeSurvey(Survey $survey, $closeStatus)
    {
        try {
            $survey->audit()->create([
                'user_id'   => auth()->id(),
                'status'    => $closeStatus,
                'text'      => 'Formulario cerrado'
            ]);
            $survey->status = $closeStatus;
            $survey->save();

            return true;
        } catch (\Throwable $th) {
            Log::error($th);
            return false;
        }
    }

    public function getQuestions(Survey $survey, Section $section, $alreadyIn = [])
    {
        if ($survey->surveyType) {
            $defaultSections = json_decode($survey->surveyType->default_sections, true);
            $generalTypeId = SurveyType::where('name', 'General')->first()->id;
            foreach ($defaultSections as $key => $value) {
                if ($section->order == $value["order"] || $section->name  == $value["name"]) {
                    $sectionTypes = ['all', $section->name];
                    return Question::whereIn('survey_type', [$survey->surveyType->id, $generalTypeId])->whereIn('section_type', $sectionTypes)->whereNotIn('id', $alreadyIn)->get();
                }
            }
        } else {
            return [];
        }
    }
}
