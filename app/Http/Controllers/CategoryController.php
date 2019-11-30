<?php

namespace App\Http\Controllers;

use App\Category;
use App\Song;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Create a new category.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $category = new Category();
        $category->name = $request->name;

        $category->save();

        return response()->json([
            'success' => true,
            'category' => $category
        ]);
    }

    /**
     * Add song to a category.
     *
     * @param Category $category_id
     * @param Song $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function add($category_id, $id)
    {
        $category = Category::find($category_id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, category with given id does not exist.'
            ], 400);
        }

        $song = Song::where('id', $id)->get();

        if (!$song) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, song with given id does not exist.'
            ], 400);
        }

        $category->songs()->attach($song);

        return response()->json([
            'success' => true,
            'message' => 'Song was added to the category.'
        ]);
    }

    /**
     * Remove the specified category.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, category with id ' . $id . ' cannot be found.'
            ], 400);
        }

        if ($category->delete()) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Category could not be deleted.'
            ], 500);
        }
    }

    /**
     * Remove the specified song from specified category.
     *
     * @param Category $category
     * @param Song $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function remove(Category $category, $id)
    {
        $song = Song::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, category cannot be found.'
            ], 400);
        }

        if ($category->songs()->detach($song)) {
            return response()->json([
                'success' => true,
                'message' => 'Song from given category was successfully removed.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Song from given category could not be deleted.'
            ], 500);
        }
    }

    /**
     * Display a listing of categories.
     *
     * @return Response
     */
    public function index()
    {
        $category = Category::get(['name'])->toArray();
        return $category;
    }

    /**
     * Display songs from specified playlist
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, category with id ' . $id . ' cannot be found.'
            ], 400);
        }

        $currentCategory = $category->songs()->get(['title', 'artist', 'cues'])->toArray();

        return response()->json([
            'success' => true,
            'category' => $currentCategory
        ]);
    }
}
