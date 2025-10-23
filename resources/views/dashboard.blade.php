@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <h1 class="h3 mb-0">
      <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </h1>
    <p class="text-muted">Resumen general del sistema</p>
  </div>
</div>

<!-- Estadísticas principales -->
<div class="row mb-4">
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="stat-card">
      <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <i class="bi bi-people"></i>
      </div>
      <div class="stat-number">{{ $totalClients }}</div>
      <div class="stat-label">Total Clientes</div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="stat-card success">
      <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <i class="bi bi-person-check"></i>
      </div>
      <div class="stat-number">{{ $activeClients }}</div>
      <div class="stat-label">Clientes Activos</div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="stat-card warning">
      <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <i class="bi bi-box"></i>
      </div>
      <div class="stat-number">{{ $totalProducts }}</div>
      <div class="stat-label">Total Productos</div>
    </div>
  </div>
  
  <div class="col-lg-3 col-md-6 mb-3">
    <div class="stat-card danger">
      <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <i class="bi bi-boxes"></i>
      </div>
      <div class="stat-number">{{ number_format($totalStock) }}</div>
      <div class="stat-label">Stock Total</div>
    </div>
  </div>
</div>

<!-- Gráficas -->
<div class="row mb-4">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="bi bi-graph-up me-2"></i>Crecimiento Mensual
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="growthChart"></canvas>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="bi bi-pie-chart me-2"></i>Estado de Clientes
        </h5>
      </div>
      <div class="card-body">
        <div class="chart-container">
          <canvas id="clientsStatusChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabla de productos top y acciones rápidas -->
<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
          <i class="bi bi-trophy me-2"></i>Top 5 Productos por Stock
        </h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Producto</th>
                <th>SKU</th>
                <th>Stock</th>
                <th>Precio</th>
                <th>Valor Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topProducts as $product)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="bi bi-box text-white"></i>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $product->name }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge bg-secondary">{{ $product->sku }}</span></td>
                  <td>
                    <span class="badge bg-success">{{ $product->stock }}</span>
                  </td>
                  <td>${{ number_format($product->price, 2) }}</td>
                  <td class="fw-semibold">${{ number_format($product->price * $product->stock, 2) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">No hay productos registrados</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="bi bi-lightning me-2"></i>Acciones Rápidas
        </h5>
      </div>
      <div class="card-body">
        <div class="d-grid gap-3">
          <a href="{{ route('clients.index') }}" class="btn btn-brand">
            <i class="bi bi-person-plus me-2"></i>Gestionar Clientes
          </a>
          <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-box-seam me-2"></i>Gestionar Productos
          </a>
          <div class="text-center mt-3">
            <small class="text-muted">
              <i class="bi bi-info-circle me-1"></i>
              Valor total del inventario: <strong>${{ number_format($totalValue, 2) }}</strong>
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Datos para las gráficas
const clientsData = @json($clientsByMonth);
const productsData = @json($productsByMonth);
const clientsStatus = @json($clientsStatus);

// Gráfica de crecimiento mensual
const growthCtx = document.getElementById('growthChart').getContext('2d');
new Chart(growthCtx, {
  type: 'line',
  data: {
    labels: clientsData.map(item => {
      const date = new Date(item.month + '-01');
      return date.toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
    }),
    datasets: [{
      label: 'Clientes',
      data: clientsData.map(item => item.count),
      borderColor: 'rgb(102, 126, 234)',
      backgroundColor: 'rgba(102, 126, 234, 0.1)',
      tension: 0.4,
      fill: true
    }, {
      label: 'Productos',
      data: productsData.map(item => item.count),
      borderColor: 'rgb(79, 172, 254)',
      backgroundColor: 'rgba(79, 172, 254, 0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        labels: {
          color: '#e2e8f0'
        }
      }
    },
    scales: {
      x: {
        ticks: {
          color: '#94a3b8'
        },
        grid: {
          color: '#2d3748'
        }
      },
      y: {
        ticks: {
          color: '#94a3b8'
        },
        grid: {
          color: '#2d3748'
        }
      }
    }
  }
});

// Gráfica de estado de clientes
const statusCtx = document.getElementById('clientsStatusChart').getContext('2d');
new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Activos', 'Inactivos'],
    datasets: [{
      data: [clientsStatus.active, clientsStatus.inactive],
      backgroundColor: [
        'rgba(79, 172, 254, 0.8)',
        'rgba(240, 147, 251, 0.8)'
      ],
      borderColor: [
        'rgba(79, 172, 254, 1)',
        'rgba(240, 147, 251, 1)'
      ],
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          color: '#e2e8f0',
          padding: 20
        }
      }
    }
  }
});
</script>
@endpush
