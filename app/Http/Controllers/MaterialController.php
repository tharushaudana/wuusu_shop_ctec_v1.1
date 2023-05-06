<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManageMaterialRequest;
use App\Http\Resources\PaginatedResource;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        /*$paginate = Material::orderBy('id', 'desc')->paginate(2);

        return response()->success(new PaginatedResource($paginate));*/

        $materials = Material::orderBy('id', 'desc')->get();

        return response()->success(['materials' => $materials]);
    }

    public function store(ManageMaterialRequest $request)
    {
        $material = Material::create($request->all());

        return response()->success(['material' => $material]);
    }

    public function show(Material $material)
    {
        return response()->success(['material' => $material]);
    }

    public function update(ManageMaterialRequest $request, Material $material)
    {
        $material->update($request->all());

        return response()->success(['material' => $material]);
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return response()->success();
    }
}
