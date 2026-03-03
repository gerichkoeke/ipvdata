<?php

namespace App\Http\Controllers;

use App\Models\Distribuidor;
use Illuminate\Http\Request;

class DistribuidorController extends Controller
{
    public function index()
    {
        return view('admin.distribuidores.index', ['distribuidores' => Distribuidor::all()]);
    }

    public function create()
    {
        return view('admin.distribuidores.create');
    }

    public function store(Request $request)
    {
        Distribuidor::create($request->validated());
        return redirect()->route('distribuidores.index')->with('success', 'Distribuidor criado com sucesso!');
    }
}
