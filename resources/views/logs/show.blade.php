@extends("maestra")
@section("titulo", "Logs")
@section("contenido")
<style>
    .logs-shell {
        display: grid;
        grid-template-columns: minmax(280px, 360px) minmax(0, 1fr);
        gap: 24px;
        align-items: start;
    }
    .logs-panel {
        border: 1px solid #dbe4ea;
        border-radius: 16px;
        background: #fff;
        padding: 20px;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.05);
    }
    .logs-actions form + form {
        margin-top: 12px;
    }
    .deploy-result {
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 12px;
        border: 1px solid #dbe4ea;
        background: #f8fafc;
    }
    .deploy-result.is-ok {
        border-color: #86efac;
        background: #f0fdf4;
    }
    .deploy-result.is-error {
        border-color: #fca5a5;
        background: #fef2f2;
    }
    .deploy-result pre,
    .logs-output {
        margin: 0;
        white-space: pre-wrap;
        word-break: break-word;
        font-size: 0.86rem;
        max-height: 70vh;
        overflow: auto;
    }
    @media (max-width: 991px) {
        .logs-shell {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="row">
    <div class="col-12">
        <h1 class="mb-3">Logs del sistema</h1>

        @if(session('status'))
            <div class="alert alert-info">{{ session('status') }}</div>
        @endif

        <div class="logs-shell">
            <section class="logs-panel logs-actions">
                <h4>Acciones</h4>
                <p class="text-muted">Desde aca podes archivar el log y ejecutar el mantenimiento de deploy sin consola.</p>

                <form action="{{ route('logs.archive') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-block">Archivar log actual</button>
                </form>

                <form action="{{ route('logs.deployMaintenance') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-block">Ejecutar mantenimiento de deploy</button>
                </form>

                <small class="text-muted d-block mt-3">
                    El mantenimiento intenta correr `composer install`, migraciones, limpieza de caches, recreacion de caches y reinicio de colas.
                </small>

                @if(!empty($deployResults))
                    <hr>
                    <h5>Resultado ultimo deploy</h5>
                    @foreach($deployResults as $result)
                        <div class="deploy-result {{ $result['ok'] ? 'is-ok' : 'is-error' }}">
                            <strong>{{ $result['label'] }}</strong>
                            <pre>{{ $result['output'] }}</pre>
                        </div>
                    @endforeach
                @endif
            </section>

            <section class="logs-panel">
                <h4>Contenido de `laravel.log`</h4>
                <pre class="logs-output">{{ $logs }}</pre>
            </section>
        </div>
    </div>
</div>
@endsection