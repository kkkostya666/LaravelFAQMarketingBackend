<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventUserRequest;
use App\Http\Requests\notification\CabinetsNotificationRequest;
use App\Models\Event;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions;

class EventController extends Controller
{
    use Actions\FetchMany;
    use Actions\FetchOne;
    use Actions\Store;
    use Actions\Update;
    use Actions\Destroy;
    use Actions\FetchRelated;
    use Actions\FetchRelationship;
    use Actions\UpdateRelationship;
    use Actions\AttachRelationship;
    use Actions\DetachRelationship;

    public function addEventUser(EventUserRequest $request, Event $event): JsonResponse
    {
        $eventId = $request->input('user_id', []);

        if (empty($eventId) || !is_array($eventId)) {
            return response()->json(['message' => 'Пустой cписок участников'], 422);
        }

        try {
            $existingIds = $event->users()->pluck('user_id')->toArray();
            $newIds = array_diff($eventId, $existingIds);

            if (empty($newIds)) {
                return response()->json(['message' => 'All cabinets already added']);
            }

            $event->users()->attach($newIds);

            return response()->json([
                'message' => 'Событие успешно добавлен',
                'added' => count($newIds),
                'cabinet_ids' => $newIds
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error while adding cabinet notifications', 'error' => $e->getMessage()], 500);
        }
    }
}
