<?php

namespace App\Http\Controllers\Bible;

use App\Models\Bible\BibleFile;
use App\Models\Bible\BibleVerse;
use App\Models\Bible\Book;
use App\Models\Bible\BibleFileset;
use App\Models\Bible\BookTranslation;
use App\Models\Language\Language;
use App\Transformers\V2\LibraryCatalog\BookTransformer;
use App\Http\Controllers\APIController;

class BooksControllerV2 extends APIController
{

    /**
     * Gets the book order and code listing for a volume.
     *
     * @version 2
     * @category v2_library_book
     * @category v2_library_bookOrder
     * @link http://dbt.io/library/bookorder - V2 Access
     * @link http://api.dbp.test/library/bookorder?key=1234&v=2&dam_id=AMKWBT&pretty - V2 Test
     * @link https://dbp.test/eng/docs/swagger/v2#/Library/v2_library_book - V2 Test Docs
     *
     * @OA\Get(
     *     path="/library/book/",
     *     tags={"Library Catalog"},
     *     summary="Returns books order",
     *     description="Gets the book order and code listing for a volume.",
     *     operationId="v2_library_book",
     *     @OA\Parameter(name="dam_id",in="query",required=true, @OA\Schema(ref="#/components/schemas/Bible/properties/id")),
     *     @OA\Parameter(name="asset_id",in="query", @OA\Schema(ref="#/components/schemas/Asset/properties/id")),
     *     @OA\Parameter(ref="#/components/parameters/version_number"),
     *     @OA\Parameter(ref="#/components/parameters/key"),
     *     @OA\Parameter(ref="#/components/parameters/pretty"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\MediaType(mediaType="application/json", @OA\Schema(ref="#/components/schemas/v2_library_book"))
     *     )
     * )
     *
     * @param dam_id - the volume internal bible_id.
     *
     * @return Book string - A JSON string that contains the status code and error messages if applicable.
     */
    public function book()
    {
        $id        = checkParam('dam_id');
        $asset_id  = checkParam('bucket|bucket_id|asset_id') ?? config('filesystems.disks.s3_fcbh.bucket');

        $fileset   = BibleFileset::with('bible')->where('asset_id', $asset_id)
            ->where('id', $id)
            ->orWhere('id', substr($id, 0, -4))
            ->orWhere('id', substr($id, 0, -2))
            ->orWhere('id', 'LIKE', $id.'%')
            ->first();
        if (!$fileset) {
            return $this->setStatusCode(404)->replyWithError(trans('api.bible_fileset_errors_404', ['id' => $id]));
        }
        $testament = false;

        switch ($id[\strlen($id) - 2]) {
            case 'O':
                $testament = 'OT';
                break;

            case 'N':
                $testament = 'NT';
                break;
        }

        $cache_string = strtolower('v2_library_book_' . $id . $asset_id . $fileset . $testament);
        $libraryBook = \Cache::remember($cache_string, 1600,
            function () use ($id, $fileset, $testament) {

                if ($fileset->set_type_code === 'text_plain') {
                    // If plain text check BibleVerse
                    $booksChapters = BibleVerse::where('hash_id', $fileset->hash_id)->select('book_id', 'chapter')->distinct()->get();
                    $chapter_field = 'chapter';
                } else {
                    // Otherwise refer to Bible Files
                    $booksChapters = BibleFile::where('hash_id', $fileset->hash_id)->select(['book_id','chapter_start'])->distinct()->get();
                    $chapter_field = 'chapter_start';
                }

                $books = Book::whereIn('id', $booksChapters->pluck('book_id'))->when($testament, function ($q) use ($testament) {
                    $q->where('book_testament', $testament);
                })->orderBy('protestant_order')->get();
                foreach ($books as $key => $book) {
                    $current_chapters[$key] = $booksChapters->where('book_id', $book->id)->pluck($chapter_field);
                }

                $bible_id = $fileset->bible->first()->id;
                foreach ($books as $key => $book) {
                    $books[$key]->source_id       = $id;
                    $books[$key]->bible_id        = $bible_id;
                    $books[$key]->number_chapters = $current_chapters[$key]->count();
                    $books[$key]->chapters        = $current_chapters[$key]->implode(',');
                }

                return fractal($books, new BookTransformer(), $this->serializer);
            }
        );

        return $this->reply($libraryBook);
    }

    public function bookOrder()
    {
        $id        = checkParam('dam_id', true);
        $asset_id  = checkParam('bucket|bucket_id|asset_id') ?? 'dbp-prod';

        $fileset   = BibleFileset::with('bible')
            ->where(function($query) use ($id) {
                $query->where('id', $id)->orWhere('id', substr($id, 0, -4))->orWhere('id', substr($id, 0, -2));
            })->where('asset_id', $asset_id)->where('set_type_code', 'text_plain')->first();
        if (!$fileset) {
            return $this->setStatusCode(404)->replyWithError(trans('api.bible_fileset_errors_404', ['id' => $id]));
        }
        $testament = false;

        switch ($id[\strlen($id) - 2]) {
            case 'O':
                $testament = 'OT';
                break;

            case 'N':
                $testament = 'NT';
        }
        $cache_string = strtolower('v2_library_bookOrder_' . $id . $asset_id . $fileset . $testament);
        $libraryBook = \Cache::remember($cache_string, 1600,
            function () use ($id, $fileset, $testament) {
                $booksChapters = BibleVerse::where('hash_id', $fileset->hash_id)->select('book_id', 'chapter')->distinct()->get();
                $books = Book::whereIn('id', $booksChapters->pluck('book_id')->unique()->toArray())
                             ->when($testament, function ($q) use ($testament) {
                                 $q->where('book_testament', $testament);
                             })->orderBy('protestant_order')->get();

                $bible_id = $fileset->bible()->first()->id;

                foreach ($books as $key => $book) {
                    $chapters                     = $booksChapters->where('book', $book->id_usfx)->pluck('chapter');
                    $books[$key]->source_id       = $id;
                    $books[$key]->bible_id        = $bible_id;
                    $books[$key]->chapters        = $chapters->implode(',');
                    $books[$key]->number_chapters = $chapters->count();
                }

                return fractal($books, new BookTransformer())->serializeWith($this->serializer);
            }
        );

        return $this->reply($libraryBook);
    }

    /**
     * Gets the book order and code listing for a volume.
     *
     * @version 2
     * @category v2_library_bookName
     * @link http://dbt.io/library/bookname - V2 Access
     * @link http://api.dbp.test/library/bookname?key=1234&v=2&language_code=ben - V2 Test Access
     * @link https://dbp.test/eng/docs/swagger/v2#/Library/v2_library_bookname - V2 Test Docs
     *
     * @OA\Get(
     *     path="/library/bookname/",
     *     tags={"Library Catalog"},
     *     summary="Returns book Names",
     *     description="Gets the book order and code listing for a volume.",
     *     operationId="v2_library_bookName",
     *     @OA\Parameter(name="language_code",in="query",description="The language_code",required=true, @OA\Schema(ref="#/components/schemas/Language/properties/iso")),
     *     @OA\Parameter(ref="#/components/parameters/version_number"),
     *     @OA\Parameter(ref="#/components/parameters/key"),
     *     @OA\Parameter(ref="#/components/parameters/pretty"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\MediaType(mediaType="application/json", @OA\Schema(ref="#/components/schemas/BookName"))
     *     )
     * )
     *
     * @param language_code - The language code to filter the books by
     *
     * @return BookTranslation string - A JSON string that contains the status code and error messages if applicable.
     *
     */
    public function bookNames()
    {
        if (!$this->api) {
            return view('docs.books.bookNames');
        }
        $iso = checkParam('language_code');
        $language = Language::where('iso', $iso)->first();
        if (!$language) {
            return $this->setStatusCode(404)->replywithError('No language could be found for the iso code specified');
        }

        $cache_string = 'v2_library_bookName_' . strtolower($iso);
        $libraryBookName = \Cache::remember($cache_string, 1600, function () use ($language) {
            $bookTranslations = BookTranslation::where('language_id', $language->id)->with('book')->select(['name', 'book_id'])->get()->pluck('name', 'book.id_osis');
            $bookTranslations['AL'] = 'Alternative';
            $bookTranslations['ON'] = 'Old and New Testament';
            $bookTranslations['OT'] = 'Old Testament';
            $bookTranslations['NT'] = 'New Testament';
            $bookTranslations['AP'] = 'Apocrypha';
            $bookTranslations['VU'] = 'Vulgate';
            $bookTranslations['ET'] = 'Ethiopian Orthodox Canon/Geez Translation Additions';
            $bookTranslations['CO'] = 'Coptic Orthodox Canon Additions';
            $bookTranslations['AO'] = 'Armenian Orthodox Canon Additions';
            $bookTranslations['PE'] = 'Peshitta';
            $bookTranslations['CS'] = 'Codex Sinaiticus';
            return [$bookTranslations];
        });

        return $this->reply($libraryBookName);
    }

    /**
     * This lists the chapters for a book or all books in a standard bible volume.
     *
     * @version 2
     * @category v2_library_chapter
     * @link http://dbt.io/library/chapter - V2 Access
     * @link https://api.dbp.test/library/chapter?key=1234&v=2&dam_id=AMKWBT&book_id=MAT&pretty - V2 Test Access
     * @link https://dbp.test/eng/docs/swagger/v2#/Library/v2_library_chapter - V2 Test Docs
     *
     * @OA\Get(
     *     path="/library/chapter/",
     *     tags={"Library Catalog"},
     *     summary="Returns chapters for a book",
     *     description="Lists the chapters for a book or all books in a standard bible volume.",
     *     operationId="v2_library_chapter",
     *     @OA\Parameter(name="dam_id",in="query",description="The bible_id",required=true, @OA\Schema(ref="#/components/schemas/Bible/properties/id")),
     *     @OA\Parameter(name="asset_id",in="query",description="The asset_id", @OA\Schema(ref="#/components/schemas/Asset/properties/id")),
     *     @OA\Parameter(name="book_id",in="query",description="The book_id",required=true, @OA\Schema(ref="#/components/schemas/Book/properties/id")),
     *     @OA\Parameter(ref="#/components/parameters/version_number"),
     *     @OA\Parameter(ref="#/components/parameters/key"),
     *     @OA\Parameter(ref="#/components/parameters/pretty"),
     *     @OA\Parameter(ref="#/components/parameters/format"),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\MediaType(mediaType="application/json", @OA\Schema(type="object",example={"GEN"="Genesis","EXO"="Exodus"}))
     *     )
     * )
     *
     * @param dam_id - The Fileset ID to filter by
     * @param book_id - The USFM 2.4 or OSIS Book ID code
     * @param asset_id - The optional asset ID of the resource, if not given the API will assume FCBH origin
     *
     * @return mixed $chapters string - A JSON string that contains the status code and error messages if applicable.
     *
     */
    public function chapters()
    {
        if (!$this->api) {
            return view('docs.books.chapters');
        }

        $id        = checkParam('dam_id');
        $asset_id  = checkParam('bucket|bucket_id|asset_id') ?? config('filesystems.disks.s3_fcbh.bucket');
        $book_id   = checkParam('book_id');

        $cache_string = strtolower('v2_library_chapter_' . $id . $asset_id . $book_id);
        $chapters = \Cache::remember($cache_string, 1600, function () use ($id, $asset_id, $book_id) {
            $fileset = BibleFileset::where(function ($query) use($id) {
                $query->where('id', $id)->orWhere('id', substr($id, 0, -4));
            })->where('set_type_code', 'text_plain')->where('asset_id', $asset_id)->first();
            if (!$fileset) {
                return $this->setStatusCode(404)->replyWithError(trans('api.bible_fileset_errors_404', ['id' => $id]));
            }

            $book = Book::where('id_osis', $book_id)->orWhere('id', $book_id)->first();
            if (!$book) {
                return $this->setStatusCode(404)->replyWithError(trans('api.bible_books_errors_404', ['id' => $id]));
            }
            $chapters = BibleVerse::where('hash_id', $fileset->hash_id)
                ->when($book, function ($q) use ($book) {
                    $q->where('book_id', $book->id);
                })
                ->select(['chapter', 'book_id'])->distinct()->orderBy('chapter')->get()
                ->map(function ($chapter) use ($id, $book) {
                    $chapter->book_id  = $book->id_osis;
                    $chapter->bible_id = $id;
                    $chapter->source_id = $id;
                    return $chapter;
                });

            return fractal($chapters, new BookTransformer())->serializeWith($this->serializer);
        });

        return $this->reply($chapters);
    }
}