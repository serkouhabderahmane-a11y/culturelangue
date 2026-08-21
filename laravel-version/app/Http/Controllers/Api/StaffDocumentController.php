<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffDocumentController extends Controller
{
    protected const PLACEMENT_TEST_CORRIGE = 'private/documents/Cultulangues – Test de Niveau -Barême + Corrigé (1).docx';

    public function placementTestCorrige(): StreamedResponse
    {
        if (!Storage::disk('local')->exists(self::PLACEMENT_TEST_CORRIGE)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            self::PLACEMENT_TEST_CORRIGE,
            'Cultulangues – Test de Niveau -Barême + Corrigé (1).docx'
        );
    }
}
