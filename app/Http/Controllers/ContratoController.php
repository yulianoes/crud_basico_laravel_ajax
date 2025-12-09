<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Http\Requests\StoreContratoRequest;
use App\Http\Requests\UpdateContratoRequest;
use Illuminate\Http\Request;

class ContratoController extends Controller
{

    private function get_categorias(){
        return ['Programador Júnior', 'Programador Sénior', 'Programador Pleno', 'Especialista'];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Definição da paginação
        $per_page = $request->input('per_page', 10);

        // Query Builder
        $query = Contrato::query();

        // Pesquisa pelo nome (LIKE)
        if ($request->filled('nome')) {
            $nome = $request->nome;
            $query->where('nome', 'like', '%' . $nome . '%');
        }

        // Obter resultados paginados
        $contratos = $query->orderBy('nome', 'asc')
            // Em caso de nomes iguais, ordena pelo 'email'
            ->orderBy('email', 'asc')->paginate($per_page);

        // Retornar dados em json
        return response()->json($contratos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contrato.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContratoRequest $request)
    {
        $contrato = Contrato::create($request->validated());

        return response()->json([
            'message' => 'Contrato criado com sucesso!',
            'contrato' => $contrato,
        ], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Contrato $contrato)
    {
        return response()->json($contrato, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contrato $contrato)
    {
        return response()->json($contrato, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContratoRequest $request, Contrato $contrato)
    {
        $contrato->update($request->validated());

        return response()->json([
            'message' => 'Contrato atualizado com sucesso!',
            'contrato' => $contrato->refresh(), // Retorna o objeto atualizado
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contrato $contrato)
    {
        $contrato->delete();

        return response()->json([
            'message' => 'Contrato excluído com sucesso!',
            'id' => $contrato->id // Opcional: retornar o ID
        ], 200);
    }

    public function indexWeb()
    {
        $categorias = $this->get_categorias();
        return view('contratos.index', compact('categorias'));
    }
}
