<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InformationRequest;
use App\Models\Objection;
use App\Models\RequestResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    /**
     * Stream a privately-stored request/objection attachment to its owner or an authorized admin.
     */
    public function show(Request $request, Media $media): BinaryFileResponse
    {
        $owner = $media->model;

        $informationRequest = match (true) {
            $owner instanceof InformationRequest => $owner,
            $owner instanceof RequestResponse, $owner instanceof Objection => $owner->request,
            default => null,
        };

        abort_if($informationRequest === null, 404);

        $user = $request->user();
        $isOwner = $informationRequest->user_id === $user->id;
        $isAuthorizedAdmin = $user->can('manage-requests') || $user->can('approve-objections');

        abort_unless($isOwner || $isAuthorizedAdmin, 403);

        return response()->file($media->getPath());
    }
}
