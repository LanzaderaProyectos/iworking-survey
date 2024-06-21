<?php

namespace MattDaneshvar\Survey\Services;

use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Answer;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Models\SurveyQuestion;

class AnswerService
{
    public $values = [
        'NO'                => 0,
        'SI'                => 100,
        'YES'               => 100,
        'NP'                => 100,
        'NA'                => 100,
        'Partially'         => 25,
        'Mainly'            => 70,
        'Totally'           => 100,
        'Parcialmente'      => 25,
        'Mayoritariamente'  => 70,
        'Totalmente'        => 100,
    ];

    public function saveAnswers($answers, $entry, $comments)
    {
        foreach ($answers as $key => $answer) {
            $score = 0;
            if ($answer['type'] == 'radio' && $answer['value'] != '') {
                $score = $this->values[$answer['value']];
            }
            if ($answer['type'] == "multiselect") {
                $answer['value'] = json_encode($answer['value'], JSON_UNESCAPED_UNICODE || JSON_UNESCAPED_SLASHES || JSON_UNESCAPED_LINE_TERMINATORS);
            }
            Answer::updateOrCreate(
                [
                    'question_id'   => $key,
                    'entry_id'      => $entry->id
                ],
                [
                    'value'     => $answer['value'],
                    'comments'  => $comments[$key] ?? null,
                    'score'     => $score
                ]
            );
        }

        return true;
    }

    public function setAnswersCommentsQuestions(Entry $entry): array
    {
        $answers            = $entry->answers;

        $answersArray       = [];
        $commentsArray      = [];
        $respondedQuestions = [];
        $listItems = [];
        foreach ($answers as $item) {
            $answersArray[$item->question_id]['value']                     = json_decode($item->value, true) ?? $item->value ?? '';
            $answersArray[$item->question_id]['comments']                  = $item->surveyQuestion->comments ?? '';
            $answersArray[$item->question_id]['type']                      = $item->surveyQuestion->question->type ?? '';
            $answersArray[$item->question_id]['question_parent_id']        = $item->surveyQuestion->parent_id ?? '';
            $answersArray[$item->question_id]['question_question_id']      = $item->surveyQuestion->original_id ?? '';
            $answersArray[$item->question_id]['model']                     = $item;
            $commentsArray[$item->question_id]                             = $item->comments ?? '';
            if ($item->surveyQuestion->answers->count() > 0) {
                $respondedQuestions[$item->surveyQuestion->id] = true;
            }
        }

        return [
            'answers'               => $answersArray,
            'comments'              => $commentsArray,
            'respondedQuestions'    => $respondedQuestions
        ];
    }

    public function updatedAnswers(array $answers, $updatedQuestionId, $value, $entry, $answersToDelete): array
    {
        $surveyQuestion                                                 = SurveyQuestion::find($updatedQuestionId);
        if (!is_array($answers[$updatedQuestionId]['value'])) {
            $answers[$updatedQuestionId]['value']                     = $value['value'] ?? $value;
        }
        $answers[$updatedQuestionId]['comments']                  = $surveyQuestion->comments ?? '';
        $answers[$updatedQuestionId]['type']                      = $surveyQuestion->question->type;
        $answers[$updatedQuestionId]['question_parent_id']        = $surveyQuestion->parent_id;
        $answers[$updatedQuestionId]['question_original_id']      = $surveyQuestion->original_id;
        $answers[$updatedQuestionId]['model']                     = $answers[$updatedQuestionId]['model'] ?? null;
        $subQuestions                                             = $surveyQuestion->children ?? [];

        foreach ($subQuestions as $key => $subQuestion) {
            $id = $subQuestion->id;

            // Si existe la respuesta
            if (isset($answers[$id])) {

                // Si la respuesta es diferente a la condición
                if ($answers[$updatedQuestionId]['value'] != $subQuestion->condition && $subQuestion->condition != '00') {
                    $answerToDelete = $subQuestion->answers()->where('entry_id', $entry->id)->first();
                    if ($answerToDelete) {
                        $answersToDelete[$id] = $answerToDelete;
                    }
                    unset($answers[$id]);
                } else {
                    // Si la respuesta es igual a la condición

                    // Si existe esa respuesta en el array de respuestas a eliminar
                    if (isset($answersToDelete[$id])) {
                        $question                               = $answersToDelete[$id]->question;
                        if (!is_array($answers[$id]['value'])) {
                            $answers[$id]['value']                  = $value['value'] ?? $value;
                        }
                        $answers[$id]['comments']               = $question->comments;
                        $answers[$id]['type']                   = $question->type;
                        $answers[$id]['model']                  = $answersToDelete[$updatedQuestionId];
                        unset($answersToDelete[$id]);
                    }
                }
            } else {
                if ($answers[$updatedQuestionId]['value'] == $subQuestion->condition) {
                    $answers[$id]['value']                    = '';
                    $answers[$id]['comments']                 = $subQuestion->comments;
                    $answers[$id]['type']                     = $subQuestion->type;
                }
            }
        }

        return [
            'answers'           => $answers,
            'answersToDelete'   => $answersToDelete
        ];
    }

    /**
     * @param $survey
     * @param array $answers
     * @param $errorsBag
     * @param $comments
     * @return array
     */
    public function answersCustomValidation($survey, array $answers, $errorsBag, $comments)
    {
        $surveyQuestions = $survey->surveyQuestionsMain;
        foreach ($surveyQuestions as $surveyQuestion) {
            $errorsBag = $this->validateMandatoryQuestion($surveyQuestion, $answers, $errorsBag, $comments);
        }
        if (empty($errorsBag)) {
            return [
                'status'    => true,
                'errorsBag' => $errorsBag
            ];
        }
        return [
            'status'    => false,
            'errorsBag' => $errorsBag
        ];
    }

    /**
     * @param SurveyQuestion $surveyQuestion
     * @param array $answers
     * @param array $errorsBag
     * @param array $comments
     * @return array
     */
    public function validateMandatoryQuestion($surveyQuestion, array $answers, $errorsBag,$comments) {
        if ($surveyQuestion->mandatory) {
            if($surveyQuestion->question->type == "number")
            {
                if (empty($answers[$surveyQuestion->id]['value']) && $answers[$surveyQuestion->id]['value'] !== 0  && $answers[$surveyQuestion->id]['value'] !== "0") {
                    if (empty($comments[$surveyQuestion->id])) {
                        $errorsBag[$surveyQuestion->id] = $surveyQuestion->id . "";
                    }
                } else {
                    unset($errorsBag[$surveyQuestion->id]);
                }
            }
            elseif (empty($answers[$surveyQuestion->id]['value'])) {
                if (empty($comments[$surveyQuestion->id])) {
                    $errorsBag[$surveyQuestion->id] = $surveyQuestion->id . "";
                }
            } else {
                unset($errorsBag[$surveyQuestion->id]);
            }
        }
        foreach($surveyQuestion->children as $child) {
            if($surveyQuestion->question->type == "radio"){
                if($answers[$surveyQuestion->id]['value'] == $child->condition){
                    $errorsBag = $this->validateMandatoryQuestion($child, $answers, $errorsBag, $comments);
                }
            }
            else{
                $errorsBag = $this->validateMandatoryQuestion($child, $answers, $errorsBag, $comments);
            }
        }
        return $errorsBag;
    }
}
