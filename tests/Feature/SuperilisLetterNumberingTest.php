<?php

use App\Models\Superilis;

test('it resets the letter number when the year changes', function () {
    Superilis::updateOrCreate(['key' => 'last_letter_year'], ['value' => '2025']);
    Superilis::updateOrCreate(['key' => 'last_letter_number'], ['value' => '10']);

    $number = Superilis::consumeNextLetterNumber('2026');

    expect($number)->toBe('K-1/BPS1374/9286/2026')
        ->and(Superilis::where('key', 'last_letter_year')->value('value'))->toBe('2026')
        ->and(Superilis::where('key', 'last_letter_number')->value('value'))->toBe('1');
});
