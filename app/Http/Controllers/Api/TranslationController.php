<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TranslationRequest;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Translation Management API",
 *     description="API for managing multilingual translations"
 * )
 */

class TranslationController extends Controller
{
    public function __construct(
        private readonly TranslationService $translationService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="List translations",
     *     tags={"Translations"},
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         description="Language code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', 'en');
        $tags = $request->get('tags', []);
        $page = $request->get('page', 1);

        $translations = $this->translationService->getTranslations($locale, $tags, $page);

        return response()->json([
            'data' => $translations->items(),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ]
        ]);
    }

    public function store(TranslationRequest $request): JsonResponse
    {
        $translation = $this->translationService->createTranslation($request->validated());

        return response()->json([
            'data' => $translation->load('tags'),
            'message' => 'Translation created successfully'
        ], 201);
    }

    public function show(Translation $translation): JsonResponse
    {
        return response()->json([
            'data' => $translation->load('tags', 'language')
        ]);
    }

    public function update(TranslationRequest $request, Translation $translation): JsonResponse
    {
        $translation = $this->translationService->updateTranslation($translation, $request->validated());

        return response()->json([
            'data' => $translation->load('tags'),
            'message' => 'Translation updated successfully'
        ]);
    }

    public function destroy(Translation $translation): JsonResponse
    {
        $this->translationService->deleteTranslation($translation);

        return response()->json([
            'message' => 'Translation deleted successfully'
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'locale' => 'nullable|string|size:2',
            'tags' => 'nullable|array',
            'tags.*' => 'string'
        ]);

        $translations = $this->translationService->searchTranslations(
            $request->query('query'),
            $request->query('locale'),
            $request->query('tags', [])
        );

        return response()->json([
            'data' => $translations->items(),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ]
        ]);
    }

    public function export(Request $request): JsonResponse
    {
        $locale = $request->get('locale', 'en');
        $tags = $request->get('tags', []);
        
        $translations = $this->translationService->getTranslationsForExport($locale, $tags);

        return response()->json($translations);
    }
} 