@extends('layouts.app')
@section('title','Clientes')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-0">
          <i class="bi bi-people me-2"></i>Gestión de Clientes
        </h1>
        <p class="text-muted">Administra la información de tus clientes</p>
      </div>
      <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#clientCreate">
        <i class="bi bi-person-plus me-1"></i> Nuevo Cliente
      </button>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">
            <i class="bi bi-list-ul me-2"></i>Lista de Clientes
          </h5>
          <form class="d-flex" method="get">
            <input name="q" value="{{ $q }}" class="form-control me-2" placeholder="Buscar por nombre o email..." style="width: 300px;">
            <button class="btn btn-ghost">
              <i class="bi bi-search"></i>
            </button>
          </form>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($clients as $c)
                <tr>
                  <td>
                    <span class="badge bg-secondary">#{{ $c->id }}</span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-person text-white"></i>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $c->name }}</div>
                      </div>
                    </div>
                  </td>
                  <td>{{ $c->email }}</td>
                  <td>{{ $c->phone ?? '—' }}</td>
                  <td>
                    @if($c->active)
                      <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Activo
                      </span>
                    @else
                      <span class="badge bg-secondary">
                        <i class="bi bi-x-circle me-1"></i>Inactivo
                      </span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-ghost" data-bs-toggle="modal" data-bs-target="#clientEdit"
                        data-client='@json($c)' title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('clients.destroy', $c->id) }}', '{{ $c->name }}', 'cliente')" title="Eliminar">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="text-muted">
                      <i class="bi bi-people display-4 d-block mb-3"></i>
                      <h5>No hay clientes registrados</h5>
                      <p>Comienza agregando tu primer cliente</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($clients->hasPages())
          <div class="card-footer">
            {{ $clients->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Crear --}}
<div class="modal fade" id="clientCreate" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="{{ route('clients.store') }}">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Nuevo cliente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre</label><input name="name" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
        <div class="col-12 col-md-6"><label class="form-label">Teléfono</label><input name="phone" class="form-control"></div>
        <div class="col-12 col-md-6 d-flex align-items-center">
          <div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="active" checked value="1" id="activeCreate"><label class="form-check-label" for="activeCreate">Activo</label></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-ghost" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-brand">Guardar</button></div>
    </form>
  </div>
</div>

{{-- Editar --}}
<div class="modal fade" id="clientEdit" tabindex="-1">
  <div class="modal-dialog">
    <form id="clientEditForm" class="modal-content" method="post">@csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Editar cliente</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre</label><input name="name" id="c_name" class="form-control" required></div>
        <div class="col-12"><label class="form-label">Email</label><input name="email" id="c_email" type="email" class="form-control" required></div>
        <div class="col-12 col-md-6"><label class="form-label">Teléfono</label><input name="phone" id="c_phone" class="form-control"></div>
        <div class="col-12 col-md-6 d-flex align-items-center">
          <div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="active" id="c_active" value="1"><label class="form-check-label" for="c_active">Activo</label></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-ghost" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-brand">Actualizar</button></div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
const editM = document.getElementById('clientEdit');
editM?.addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget, c = JSON.parse(btn.getAttribute('data-client'));
  const f = document.getElementById('clientEditForm'); f.action = `/clients/${c.id}`;
  c_name.value = c.name ?? ''; c_email.value = c.email ?? ''; c_phone.value = c.phone ?? '';
  document.getElementById('c_active').checked = !!c.active;
});
</script>
@endpush
