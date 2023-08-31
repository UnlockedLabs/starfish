<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LTIProvider extends Model
{
    protected $table = 'lti_provider';
    protected $fillable = ['providerID', 'accountName', 'ltiAccount'];

    public static function checkAndInsert($providerID, $accountName, $ltiAccount)
    {
        $provider = self::where('providerID', $providerID)
            ->where('accountName', $accountName)
            ->where('ltiAccount', $ltiAccount)
            ->first();

        if (!$provider) {
            self::create([
                'providerID' => $providerID,
                'accountName' => $accountName,
                'ltiAccount' => $ltiAccount,
            ]);
            return true; // Data inserted
        }

        return false; // Data already exists
    }
}
