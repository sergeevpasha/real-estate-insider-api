<?php

declare(strict_types=1);

namespace App\Http\Traits;

use App\Enums\ApplicationLanguage;
use Illuminate\Http\Request;

trait GuessLanguageTrait
{
    /**
     * Guess user language
     *
     * @param Request $request
     * @return string
     */
    public function guessLanguage(Request $request): string
    {
        $availableLanguages = ApplicationLanguage::toArray();
        $languages = $request->getLanguages();

        foreach ($languages as $language) {
            if (in_array($language, $availableLanguages)) {
                return $language;
            }
        }

        return 'en';
    }
}
