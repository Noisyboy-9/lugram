<?php

namespace App\Http\Controllers\Follows;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AcceptFollowRequestsController extends Controller
{
    public function update($userId)
    {
        try {
            $user = User::findOrFail($userId);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'not found'], 404);
        }
        if (!auth()->user()->hasAwaitingRequestFrom($user)) {
            return response()->json(['message' => 'no request found from the user'], 406);
        }

        auth()->user()->acceptRequest($user);

        return response()->json(['accepted' => true], 200);
    }
}
