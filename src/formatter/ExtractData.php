<?php
namespace nextgensoft\formatter;

class ExtractData
{
    const PERCENT_100 = 100;

    public function extractParagraph($paragraph)
    {
        return explode("\n", trim($paragraph));
    }

    public function commonTrimSchoolName($schoolName)
    {
        $schoolName = str_replace('โรงเรียน', '', $schoolName);
        $schoolName = str_replace(' ', '', $schoolName);
        $schoolName = str_replace('1', '', $schoolName);
        $schoolName = str_replace('.', '', $schoolName);
        $schoolName = str_replace('ชื่อ', '', $schoolName);

        return $schoolName;
    }
    public function compareSchool($array)
    {
        return $array['name'];
    }
    public function findSchoolName($schoolName)
    {
        $schoolName = $this->commonTrimSchoolName($schoolName);
        foreach (SCHOOL_SUGGEST as $schoolSuggest) {
            foreach ($schoolSuggest['synonyms'] as $synonyms) {
                similar_text($schoolName, $synonyms, $perc);
                if ($perc == self::PERCENT_100) {
                    return $schoolSuggest['name'];
                }
            }
        }
    }
}
