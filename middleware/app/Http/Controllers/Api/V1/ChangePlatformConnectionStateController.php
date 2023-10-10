<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlatformConnectionResource;
use App\Models\ConsumerPlatform;
use App\Models\PlatformConnection;
use App\Models\ProviderPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChangePlatformConnectionStateController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, PlatformConnection $platformConnection)
    {
        $platformConnection->state = $request->state;

        $platformConnection->save();

        return PlatformConnectionResource::make($platformConnection);
    }
}
