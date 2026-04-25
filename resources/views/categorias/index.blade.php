@extends("maestra")
@section("titulo", "Categorias")
@section("contenido")
<style>
    .categorias-shell {
        display: grid;
        grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }
    .categorias-panel {
        border: 1px solid #dbe4ea;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff, #f8fafc);
        padding: 20px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }
    .categorias-title {
        margin-bottom: 14px;
    }
    .categoria-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.22rem 0.7rem;
        border-radius: 999px;
        background: #e0f2fe;
        color: #075985;
        border: 1px solid #bae6fd;
        font-weight: 600;
        font-size: 0.82rem;
    }
    .categoria-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .categoria-inline-form {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .categoria-inline-form .form-control {
        min-width: 220px;
    }
    @media (max-width: 991px) {
        .categorias-shell {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h1 class="mb-1">Categorias</h1>
                <p class="text-muted mb-0">Crea, renombra y ordena las categorias disponibles para los productos.</p>
            </div>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-primary">Volver a productos</a>
        </div>
        @include("notificacion")

        <div class="categorias-shell">
            <section class="categorias-panel">
                <div class="categorias-title">
                    <h4 class="mb-1">Nueva categoria</h4>
                    <small class="text-muted">Disponible al instante en alta y edicion de productos.</small>
                </div>
                <form action="{{ route('categorias.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required autocomplete="off" placeholder="Ej: Limpieza, Bebidas, Congelados">
                    </div>
                    <button type="submit" class="btn btn-success">Crear categoria</button>
                </form>
            </section>

            <section class="categorias-panel">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <div>
                        <h4 class="mb-1">Categorias cargadas</h4>
                        <small class="text-muted">Al eliminar una categoria, sus productos vuelven a General.</small>
                    </div>
                    <span class="categoria-chip">{{ $categorias->count() }} activas</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Categoria</th>
                                <th>Productos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorias as $categoria)
                            <tr>
                                <td>
                                    <span class="categoria-chip">{{ $categoria->nombre }}</span>
                                </td>
                                <td>{{ $usoPorCategoria[$categoria->nombre] ?? 0 }}</td>
                                <td>
                                    <div class="categoria-actions">
                                        <form action="{{ route('categorias.update', $categoria) }}" method="post" class="categoria-inline-form">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" class="form-control form-control-sm" name="nombre" value="{{ $categoria->nombre }}" required>
                                            <button type="submit" class="btn btn-warning btn-sm">Renombrar</button>
                                        </form>
                                        <form action="{{ route('categorias.destroy', $categoria) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay categorias creadas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection