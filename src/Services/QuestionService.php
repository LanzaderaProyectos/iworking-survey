<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Question;

class QuestionService
{
    public function saveQuestion(Question $question, $surveyId, $questionType, $questionName, $optionES = null, $optionEN = null, $isOriginal = null)
    {

        try {
            if ($questionType == 'radio' || $questionType == "multiselect" || $questionType == "uniqueselect") {
                $question->setTranslation('options', 'es', $optionES)
                    ->setTranslation('options', 'en', $optionEN);
            }
            $question->type = $questionType;
            // TODO: use survey_questions table
            // $question->survey_id = $surveyId;
            $question
                ->setTranslation('content', 'es', $questionName['es'])
                ->setTranslation('content', 'en', $questionName['en']);
            $question->save();

            if ($isOriginal === true) {
                $question->original_id = $question->id;
                $question->save();
            }

            return true;
        } catch (\Throwable $th) {
            Log::error($th);
            return false;
        }
    }

    public function getDefaultQuestions(): Collection
    {
        return Question::whereNull('survey_id')->whereNull('section_id')->get();
    }

    public function copyQuestion($isOriginal, $selectedDefaultQuestion, $surveyId, $selectedParentQuestionId, $parentQuestionRadio, $originalId, $sectionId = null, $parentId = null, $optionES = null, $optionEN = null)
    {
        $copyQuestion                   = Question::find($selectedDefaultQuestion)->toArray();
        $copyQuestion['survey_id']      = $surveyId;

        unset($copyQuestion['id']);
        unset($copyQuestion['created_at']);
        unset($copyQuestion['updated_at']);
        unset($copyQuestion['original_id']);
        // Si la pregunta a crear es una pregunta de primer nivel
        if ($isOriginal) {
            if ($sectionId) {
                $copyQuestion['section_id'] = $sectionId;
            } else {
                return;
            }
        } else {
            // Si la pregunta a crear es una sub pregunta
            $copyQuestion['parent_id']      = $selectedParentQuestionId;
            $copyQuestion['condition']      = $parentQuestionRadio;
            $copyQuestion['original_id']    = $originalId;
            $copyQuestion['section_id']     = $sectionId;
        }

        return $this->saveQuestion(
            question: new Question($copyQuestion),
            surveyId: $surveyId,
            questionType: $copyQuestion['type'],
            questionName: [
                'es' => $copyQuestion['content']['es'],
                'en' => $copyQuestion['content']['en']
            ],
            optionES: $optionES,
            optionEN: $optionEN,
            isOriginal: $isOriginal
        );
    }
}
