<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\APIController;

use App\Models\Language\Language;
use App\Transformers\LanguageTransformer;
use App\Traits\AccessControlAPI;

class LanguagesController extends APIController
{
    use AccessControlAPI;

    /**
     * Display a listing of the resource.
     *
     * Fetches the records from the database > passes them through fractal for transforming.
     *
     * @OA\Get(
     *     path="/languages/",
     *     tags={"Languages"},
     *     summary="Returns Languages",
     *     description="Returns the List of Languages",
     *     operationId="v4_languages.all",
     *     @OA\Parameter(
     *          name="country",
     *          in="query",
     *          @OA\Schema(ref="#/components/schemas/Country/properties/id"),
     *          description="The country"
     *     ),
     *     @OA\Parameter(
     *          name="iso",
     *          in="query",
     *          @OA\Schema(ref="#/components/schemas/Language/properties/iso"),
     *          description="The iso code to filter languages by"
     *     ),
     *     @OA\Parameter(
     *          name="language_name",
     *          in="query",
     *          @OA\Schema(type="object"),
     *          description="The language_name field will filter results by a specific language name"
     *     ),
     *     @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          @OA\Schema(type="object"),
     *          description="The sort_by field will order results by a specific field"
     *     ),
     *     @OA\Parameter(
     *          name="has_bibles",
     *          in="query",
     *          @OA\Schema(type="object"),
     *          description="When set to true will filter language results depending whether or not they have bibles."
     *     ),
     *     @OA\Parameter(
     *          name="has_filesets",
     *          in="query",
     *          @OA\Schema(type="object",default=null,example=true),
     *          description="When set to true will filter language results depending whether or not they have filesets.
     *              Will add new filesets_count field to the return."
     *     ),
     *     @OA\Parameter(
     *          name="asset_id",
     *          in="query",
     *          @OA\Schema(ref="#/components/schemas/Asset/properties/id"),
     *          description="The bucket_id"
     *     ),
     *     @OA\Parameter(
     *          name="include_alt_names",
     *          in="query",
     *          @OA\Schema(ref="#/components/schemas/Language/properties/name"),
     *          description="The include_alt_names"
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/l10n"),
     *     @OA\Parameter(ref="#/components/parameters/version_number"),
     *     @OA\Parameter(ref="#/components/parameters/key"),
     *     @OA\Parameter(ref="#/components/parameters/pretty"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\MediaType(mediaType="application/json", @OA\Schema(ref="#/components/schemas/Language"))
     *     )
     * )
     * @link https://api.dbp.test/languages?key=1234&v=4&pretty
     * @return \Illuminate\Http\Response
     *
     *
     */
    public function index()
    {
        if (!$this->api) {
            return view('wiki.languages.index');
        }

        $country               = checkParam('country');
        $code                  = checkParam('code|iso');
        $sort_by               = checkParam('sort_by') ?? 'name';
        $include_alt_names     = checkParam('include_alt_names');
        $show_restricted       = checkParam('show_restricted');
        $asset_id              = checkParam('bucket_id|asset_id');
        $name                  = checkParam('name');

        $access_control = $this->accessControl($this->key);

        $cache_string = 'v'.$this->v.'_l_'.$country.$code.$GLOBALS['i18n_id'].$sort_by.$name.
                        $show_restricted.$include_alt_names.$asset_id.$access_control->string;

        $languages = \Cache::remember($cache_string, now()->addDay(), function () use ($country, $include_alt_names, $asset_id, $code, $name, $show_restricted, $access_control) {
            $languages = Language::includeCurrentTranslation()
                ->includeAutonymTranslation()
                ->includeExtraLanguages($show_restricted, $access_control, $asset_id)
                ->includeExtraLanguageTranslations($include_alt_names)
                ->filterableByCountry($country)
                ->filterableByIsoCode($code)
                ->filterableByName($name)
                ->orderBy('languages.id')
                ->select([
                    'languages.id',
                    'languages.glotto_id',
                    'languages.iso',
                    'languages.name as backup_name',
                    'current_translation.name as name',
                    'autonym.name as autonym'
                ])->withCount('bibles')->withCount('filesets')->get();
            return fractal($languages, new LanguageTransformer(), $this->serializer);
        });

        return $this->reply($languages);
    }

    /**
     * @param $id
     *
     * @OA\Get(
     *     path="/languages/{id}",
     *     tags={"Languages"},
     *     summary="Return a single Languages",
     *     description="Returns a single Language",
     *     operationId="v4_languages.one",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The languages ID",
     *          required=true,
     *          @OA\Schema(ref="#/components/schemas/Language/properties/id")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(ref="#/components/schemas/Language")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function show($id)
    {
        $cache_string = 'language:'. strtolower($id);
        $language = \Cache::remember($cache_string, now()->addDay(), function () use ($id) {
            $language = Language::where('id', $id)->orWhere('iso', $id)->first();
            if (!$language) {
                return $this->setStatusCode(404)->replyWithError("Language not found for ID: $id");
            }
            $language->load(
                'translations',
                'codes',
                'dialects',
                'classifications',
                'countries',
                'primaryCountry',
                'bibles.translations.language',
                'bibles.filesets',
                'resources.translations',
                'resources.links'
            );
            return fractal($language, new LanguageTransformer());
        });

        return $this->reply($language);
    }

    public function valid($id)
    {
        $cache_string = 'language_single_valid:'. strtolower($id);
        $language = \Cache::remember($cache_string, now()->addDay(), function () use ($id) {
            return Language::where('iso', $id)->exists();
        });

        return $this->reply($language);
    }

}
