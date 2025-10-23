@extends('layouts.app')
@section('title','Productos')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 mb-0">
          <i class="bi bi-box me-2"></i>Gestión de Productos
        </h1>
        <p class="text-muted">Administra tu inventario de productos</p>
      </div>
      <button class="btn btn-brand" data-bs-toggle="modal" data-bs-target="#productCreate">
        <i class="bi bi-bag-plus me-1"></i> Nuevo Producto
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
            <i class="bi bi-list-ul me-2"></i>Lista de Productos
          </h5>
          <form class="d-flex" method="get">
            <input name="q" value="{{ $q }}" class="form-control me-2" placeholder="Buscar por nombre o SKU..." style="width: 300px;">
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
                <th>Producto</th>
                <th>SKU</th>
                <th>Precio</th>
                <th>Stock</th>
                <th class="text-end">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $p)
                <tr>
                  <td>
                    <span class="badge bg-secondary">#{{ $p->id }}</span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-box text-white"></i>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $p->name }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-primary">{{ $p->sku }}</span>
                  </td>
                  <td class="fw-semibold">${{ number_format($p->price, 2) }}</td>
                  <td>
                    @if($p->stock > 50)
                      <span class="badge bg-success">{{ $p->stock }}</span>
                    @elseif($p->stock > 10)
                      <span class="badge bg-warning">{{ $p->stock }}</span>
                    @else
                      <span class="badge bg-danger">{{ $p->stock }}</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="btn-group" role="group">
                      <button class="btn btn-sm btn-ghost" data-bs-toggle="modal" data-bs-target="#productEdit"
                        data-product='@json($p)' title="Editar">
                        <i class="bi bi-pencil-square"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ route('products.destroy', $p->id) }}', '{{ $p->name }}', 'producto')" title="Eliminar">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="text-muted">
                      <i class="bi bi-box display-4 d-block mb-3"></i>
                      <h5>No hay productos registrados</h5>
                      <p>Comienza agregando tu primer producto</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($products->hasPages())
          <div class="card-footer">
            {{ $products->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Crear --}}
<div class="modal fade" id="productCreate" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="post" action="{{ route('products.store') }}">@csrf
      <div class="modal-header"><h5 class="modal-title">Nuevo producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre</label><input name="name" class="form-control" required></div>
        <div class="col-6"><label class="form-label">SKU</label><input name="sku" class="form-control" required></div>
        <div class="col-3"><label class="form-label">Precio</label><input name="price" type="number" min="0" step="0.01" class="form-control" required></div>
        <div class="col-3"><label class="form-label">Stock</label><input name="stock" type="number" min="0" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-ghost" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-brand">Guardar</button></div>
    </form>
  </div>
</div>

{{-- Editar --}}
<div class="modal fade" id="productEdit" tabindex="-1">
  <div class="modal-dialog">
    <form id="productEditForm" class="modal-content" method="post">@csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Editar producto</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body row g-3">
        <div class="col-12"><label class="form-label">Nombre</label><input name="name" id="p_name" class="form-control" required></div>
        <div class="col-6"><label class="form-label">SKU</label><input name="sku" id="p_sku" class="form-control" required></div>
        <div class="col-3"><label class="form-label">Precio</label><input name="price" id="p_price" type="number" min="0" step="0.01" class="form-control" required></div>
        <div class="col-3"><label class="form-label">Stock</label><input name="stock" id="p_stock" type="number" min="0" class="form-control" required></div>
      </div>
      <div class="modal-footer"><button class="btn btn-ghost" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-brand">Actualizar</button></div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
const epm = document.getElementById('productEdit');
epm?.addEventListener('show.bs.modal', e => {
  const btn = e.relatedTarget, p = JSON.parse(btn.getAttribute('data-product'));
  const f = document.getElementById('productEditForm'); f.action = `/products/${p.id}`;
  p_name.value = p.name ?? ''; p_sku.value = p.sku ?? '';
  p_price.value = p.price ?? 0; p_stock.value = p.stock ?? 0;
});
</script>
@endpush
