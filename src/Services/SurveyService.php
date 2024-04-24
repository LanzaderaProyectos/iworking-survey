<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Mail\UserNotification;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Models\Section;

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

    public function getQuestions(Survey $survey, Section $section)
    {
        switch ($survey->type) {
            case "medicalPrescription":
                return $this->getMedicalPrescriptionQuestions($section);
            case "pharmaciesSale":
                return $this->getPharmaciesSaleQuestions($section);
            case "training":
                return $this->getTrainingQuestions($section);
            case "general":
                return $this->getGeneralQuestions($section);
            default:
                return [];
        }
    }

    public function getMedicalPrescriptionQuestions(Section $section)
    {
        if ($section->order == 1 || $section->name  == "General") {
            $sectionTypes = ['all', 'general'];
        } elseif ($section->order == 2 || $section->name  == "Preguntas") {
            $sectionTypes = ['all', 'questions'];
        } else {
            $sectionTypes = ['all', 'general', 'questions'];
        }
        return Question::whereIn('survey_type', ['medicalPrescription', 'general'])->whereIn('section_type', $sectionTypes)->get();
    }

    public function getPharmaciesSaleQuestions(Section $section)
    {
        if ($section->order == 1 || $section->name  == "General") {
            $sectionTypes = ['all', 'general'];
        } else {
            $sectionTypes = ['all', 'general'];
        }
        return Question::whereIn('survey_type', ['pharmaciesSale', 'general'])->whereIn('section_type', $sectionTypes)->get();
    }

    public function getTrainingQuestions(Section $section)
    {
        if ($section->order == 1 || $section->name  == "General") {
            $sectionTypes = ['all', 'general'];
        } elseif ($section->order == 2 || $section->name  == "Agendar Formación") {
            $sectionTypes = ['all', 'schedule_training'];
        } elseif ($section->order == 3 || $section->name  == "Formación realizada") {
            $sectionTypes = ['all', 'training_complete'];
        } else {
            $sectionTypes = ['all', 'general', 'schedule_training', 'training_complete'];
        }
        return Question::whereIn('survey_type', ['training', 'general'])->whereIn('section_type', $sectionTypes)->get();
    }

    public function getGeneralQuestions(Section $section)
    {
        if ($section->order == 1 || $section->name  == "General") {
            $sectionTypes = ['all', 'general'];
        } else {
            $sectionTypes = ['all', 'general'];
        }
        return Question::whereIn('survey_type', ['general'])->whereIn('section_type', $sectionTypes)->get();
    }
}
