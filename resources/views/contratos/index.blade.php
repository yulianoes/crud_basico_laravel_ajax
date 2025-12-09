<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Contratos</title>
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

    <style>
        /* Estilos customizados para aprimorar o Bootstrap */
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Melhoria na apresentação da Paginação Bootstrap */
        .pagination-container .page-item {
            margin: 0 2px;
        }

        .action-btns button {
            margin-right: 5px;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <h1 class="mb-4 text-primary">Gestão de Contratos</h1>

        <div class="header-controls">
            <button class="btn btn-success shadow" onclick="openModal('create')">
                <i class="bi bi-plus-circle"></i> Adicionar Novo Contrato
            </button>

            <div class="d-flex gap-2">
                <input type="text" id="search-input" class="form-control" placeholder="Pesquisar por Nome..."
                    onkeyup="loadContratos(1)">
                <select id="per-page-select" class="form-select" onchange="loadContratos(1)" style="width: auto;">
                    <option value="10">10 pág.</option>
                    <option value="25">25 pág.</option>
                    <option value="50">50 pág.</option>
                    <option value="100">Mais de 50</option>
                </select>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="contratos-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Nº</th>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th style="width: 15%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="contratos-body">
                            <tr>
                                <td colspan="5" class="text-center">A carregar contratos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="pagination-links" class="d-flex justify-content-center mt-4"></div>
    </div>

    {{-- ================================================================= --}}
    {{-- MODAL BOOTSTRAP (Usado para CREATE, EDIT e VIEW) --}}
    {{-- ================================================================= --}}
    <div class="modal fade" id="contrato-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="modalTitle" aria-hidden="true">
        {{-- Adiciona modal-lg para ser um pouco maior e modal-dialog-scrollable para scroll interno --}}
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                {{-- HEADER: Fixo e com cores contrastantes --}}
                <div class="modal-header bg-primary text-white p-3">
                    <h5 class="modal-title fw-bold" id="modal-title">Detalhes do Contrato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- BODY: Onde o conteúdo (View ou Form) é trocado --}}
                <div class="modal-body p-4">

                    {{-- ## CONTAINER DE VISUALIZAÇÃO (VIEW) ## --}}
                    <div id="view-details" style="display: none;" class="p-3 border rounded bg-light">
                        <h6 class="text-primary mb-3"><i class="bi bi-info-circle-fill"></i> Informações do Contrato
                        </h6>

                        {{-- Lista de Definição Elegante (Melhor espaçamento) --}}
                        <dl class="row mb-0">
                            <dt class="col-sm-3 fw-semibold">Nome:</dt>
                            <dd class="col-sm-9" id="view-nome"></dd>

                            <dt class="col-sm-3 fw-semibold">Email:</dt>
                            <dd class="col-sm-9" id="view-email"></dd>

                            <dt class="col-sm-3 fw-semibold">Telefone:</dt>
                            <dd class="col-sm-9" id="view-telefone"></dd>

                            <dt class="col-sm-3 fw-semibold">Categoria:</dt>
                            <dd class="col-sm-9" id="view-categoria"></dd>

                            <dt class="col-sm-3 fw-semibold text-muted border-top pt-2">ID Interno:</dt>
                            <dd class="col-sm-9 text-muted border-top pt-2" id="view-id"></dd>
                        </dl>
                    </div>

                    {{-- ## FORMULÁRIO (CREATE/EDIT) ## --}}
                    <form id="contrato-form">
                        <input type="hidden" name="contrato_id" id="contrato_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nome" class="form-label">Nome:</label>
                                <input type="text" name="nome" id="nome" minlength="4" maxlength="100"
                                    class="form-control" placeholder="Nome Completo">
                                <div id="error-nome" class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control"
                                    placeholder="exemplo@dominio.com">
                                <div id="error-email" class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefone" class="form-label">Telefone:</label>
                                <input type="text" name="telefone" id="telefone" class="form-control"
                                    placeholder="+244 9XX XXX XXX.">
                                <div id="error-telefone" class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="categoria" class="form-label">Categoria:</label>
                                <select name="categoria" id="categoria" class="form-select">
                                    <option value="" disabled selected>Selecione uma categoria...</option>
                                </select>
                                <div id="error-categoria" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- FOOTER: Botões de Ação --}}
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Fechar
                    </button>
                    <button type="submit" form="contrato-form" id="submit-button" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2@11.js') }}"></script>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.js') }}"></script>

    <script>
        // Injeção das rotas (URIs) da API do Laravel para uso no JavaScript
        const AppRoutes = {
            // [1] Rota para INDEX e STORE (GET / POST)
            contratos_index: "{{ route('contratos.index') }}",
            // [2] Rota para SHOW, UPDATE e DESTROY (GET/PUT/DELETE / {id})
            // O :id será substituído no JS
            contratos_show_update_destroy: "{{ route('contratos.show', ['contrato' => ':id']) }}",

        };

        const CategoriesList = @json($categorias);
    </script>

    <script src="{{ asset('build/js/contratos.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#telefone').mask('+244 900 000 000');
        });
    </script>

</body>

</html>
