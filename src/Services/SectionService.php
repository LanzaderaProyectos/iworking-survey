<?php

namespace MattDaneshvar\Survey\Services;

use Illuminate\Support\Facades\Log;
use MattDaneshvar\Survey\Models\Section;

class SectionService
{
    public function saveSection(Section $section, $surveyId, $sectionName)
    {
        try {
            $section->survey_id = $surveyId;
            $section
                ->setTranslation('name', 'es', $sectionName['es'])
                ->setTranslation('name', 'en', $sectionName['en']);
            $section->save();

            return true;
        } catch (\Throwable $th) {
            Log::error($th);
            return false;
        }
    }
}
