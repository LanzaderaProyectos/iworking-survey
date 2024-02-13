<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Question;

class QuestionService
{
    public function saveQuestion(Question $question, $surveyId, $questionType, $questionName, $optionES = null, $optionEN = null)
    {

        try {
            if ($questionType == 'radio') {
                $question->setTranslation('options', 'es', $optionES)
                    ->setTranslation('options', 'en', $optionEN);;
                $question->type = 'radio';
            } else {
                $question->type = 'text';
            }
            $question->survey_id = $surveyId;
            $question
                ->setTranslation('content', 'es', $questionName['es'])
                ->setTranslation('content', 'en', $questionName['en']);
            $question->save();

            return true;
        } catch (\Throwable $th) {
            Log::error($th);
            return false;
        }
    }
}
