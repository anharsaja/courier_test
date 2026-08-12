<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $query = Courier::query();

        //search
        if ($request->filled('search')) {
            $searchTerms = preg_split(
                '/\s+/',
                trim($request->search),
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            //term = budi agung
            foreach ($searchTerms as $term) {
                $query->where('name', 'like', "%{$term}%");
            }
        }


        //fileter level
        if ($request->filled('level')) {
            $levels = array_filter(
                array_map('intval', explode(',', $request->level))
            );
            $query->whereIn('level', $levels);
        }


        //sort
        if ($request->input('sort') === 'registered_at') {
            $query->orderBy('registed_at', 'desc');
        } else {
            $query->orderBy('name', 'asc');
        }
        return response()->json(
            $query->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'level' => ['required', 'integer', 'between:1,5'],
            'registered_at' => ['required', 'date'],
        ]); //tinggal tab vscode ni asoy

        $courier = Courier::create($validated);
        return response()->json($courier, 201);
        //jujur ini tinggal tab tab
    }

    public function show(Courier $courier)
    {
        return response()->json($courier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Courier $courier)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'level' => ['sometimes', 'required', 'integer', 'between:1,5'],
            'registered_at' => ['sometimes', 'required', 'date'],
        ]);

        $courier->update($validated);
        return response()->json($courier->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Courier $courier)
    {
        $courier->delete();
        return response()->json([
            'message' => 'courier terhapus bang',
        ]);
    }
}
