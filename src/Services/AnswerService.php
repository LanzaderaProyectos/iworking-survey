<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Answer;
use MattDaneshvar\Survey\Models\Question;

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

        foreach ($answers as $item) {
            $answersArray[$item->question_id]['value']                     = $item->value;
            $answersArray[$item->question_id]['comments']                  = $item->question->comments;
            $answersArray[$item->question_id]['type']                      = $item->question->type;
            $answersArray[$item->question_id]['question_parent_id']        = $item->question->parent_id;
            $answersArray[$item->question_id]['question_question_id']      = $item->question->original_id;
            $answersArray[$item->question_id]['model']                     = $item;
            $commentsArray[$item->question_id]                             = $item->comments;
            if ($item->question->answers->count() > 0) {
                $respondedQuestions[$item->question->id] = true;
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
        $question                                                 = Question::find($updatedQuestionId);
        $answers[$updatedQuestionId]['value']                     = $value['value'] ?? $value;
        $answers[$updatedQuestionId]['comments']                  = $question->comments;
        $answers[$updatedQuestionId]['type']                      = $question->type;
        $answers[$updatedQuestionId]['question_parent_id']        = $question->parent_id;
        $answers[$updatedQuestionId]['question_original_id']      = $question->original_id;
        $answers[$updatedQuestionId]['model']                     = $answers[$updatedQuestionId]['model'] ?? null;
        $subQuestions                                             = $question->subQuestions;


        foreach ($subQuestions as $key => $subQuestion) {
            $id = $subQuestion->id;

            // Si existe la respuesta
            if (isset($answers[$id])) {

                // Si la respuesta es diferente a la condición
                if ($answers[$updatedQuestionId]['value'] != $subQuestion->condition) {
                    $answerToDelete = $subQuestion->answers()->where('entry_id', $entry->id)->first();
                    if ($answerToDelete) {
                        $answersToDelete[$id] = $answerToDelete;
                    }
                    unset($answers[$id]);
                } else {
                    // Si la respuesta es igual a la condición

                    // Si existe esa respuesta en el array de respuestas a eliminar
                    if (isset($answersToDelete[$id])) {
                        $question                                           = $answersToDelete[$id]->question;
                        $answers[$id]['value']                        = $value['value'] ?? $value;
                        $answers[$id]['comments']                     = $question->comments;
                        $answers[$id]['type']                         = $question->type;
                        $answers[$id]['question_parent_id']           = $question->parent_id;
                        $answers[$id]['question_original_id']         = $question->original_id;
                        $answers[$id]['model']                        = $answersToDelete[$updatedQuestionId];
                        unset($answersToDelete[$id]);
                    }
                }
            } else {
                if ($answers[$updatedQuestionId]['value'] == $subQuestion->condition) {
                    $answers[$id]['value']                    = '';
                    $answers[$id]['comments']                 = $question->comments;
                    $answers[$id]['type']                     = $question->type;
                    $answers[$id]['question_parent_id']       = $question->parent_id;
                    $answers[$id]['question_original_id']     = $question->original_id;
                }
            }
        }

        return [
            'answers'           => $answers,
            'answersToDelete'   => $answersToDelete
        ];
    }

    public function answersCustomValidation(array $answers, $errorsBag, $comments){
        foreach ($answers as $key => $item) {
            if (empty(trim($item['value']))) {
                $errorsBag[$key] = $key . "";
            } elseif ((trim($item['value'] == 'SI' || trim($item['value'] == 'YES')) && $item['comments'])) {
                if (empty(trim($comments[$key]))) {
                    $errorsBag[$key] = $key . "";
                }
            } else {
                unset($errorsBag[$key]);
            }
        }
        if (empty($errorsBag)) {
            return [
                'status'    => true,
                'errorsBag' => $errorsBag,
                'comments'  => $comments,
                'answers'   => $answers
            ];
        }
        return [
            'status'    => false,
        ];
    }
    
}
