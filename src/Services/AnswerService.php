<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Answer;
use MattDaneshvar\Survey\Models\Question;

class AnswerService
{
    public $values = [];

    public function __construct()
    {
        $this->values = [
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
    }

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
}
