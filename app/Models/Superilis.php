<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Superilis extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function consumeNextLetterNumber($year): string
    {
        $yearKey = 'last_letter_year';
        $numberKey = 'last_letter_number';

        $currentYear = self::where('key', $yearKey)->value('value');

        if ($currentYear !== $year) {
            self::updateOrCreate(['key' => $yearKey], ['value' => $year]);
            self::updateOrCreate(['key' => $numberKey], ['value' => 1]);

            return 'K-1/BPS1374/9286/' . $year;
        }

        $lastNumber = (int) self::where('key', $numberKey)->value('value');
        $nextNumber = $lastNumber + 1;

        self::updateOrCreate(['key' => $numberKey], ['value' => $nextNumber]);

        return 'K-' . $nextNumber . '/BPS1374/9286/' . $year;
    }
}
