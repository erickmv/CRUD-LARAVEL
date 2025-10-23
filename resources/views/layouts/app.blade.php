<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Dashboard') - Sistema CRUD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
      --dark-bg: #0a0e1a;
      --card-bg: #1a1d29;
      --border-color: #2d3748;
    }
    
    body {
      background: var(--dark-bg);
      color: #e2e8f0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .navbar {
      background: linear-gradient(135deg, #1a1d29 0%, #2d3748 100%);
      border-bottom: 1px solid var(--border-color);
      backdrop-filter: blur(10px);
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.5rem;
      background: var(--primary-gradient);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .card {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
    }
    
    .stat-card {
      background: var(--card-bg);
      border-radius: 20px;
      padding: 2rem;
      position: relative;
      overflow: hidden;
    }
    
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--primary-gradient);
    }
    
    .stat-card.success::before { background: var(--success-gradient); }
    .stat-card.warning::before { background: var(--warning-gradient); }
    .stat-card.danger::before { background: var(--secondary-gradient); }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
    }
    
    .stat-label {
      color: #94a3b8;
      font-size: 0.9rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .stat-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }
    
    .btn-brand {
      background: var(--primary-gradient);
      border: none;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-brand:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }
    
    .btn-ghost {
      background: transparent;
      border: 1px solid var(--border-color);
      color: #e2e8f0;
      border-radius: 12px;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
    }
    
    .btn-ghost:hover {
      background: var(--border-color);
      color: #fff;
    }
    
    .form-control, .form-select {
      background: #1e293b;
      border: 1px solid var(--border-color);
      color: #e2e8f0;
      border-radius: 12px;
      padding: 0.75rem 1rem;
    }
    
    .form-control:focus, .form-select:focus {
      background: #1e293b;
      border-color: #667eea;
      color: #e2e8f0;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .table {
      color: #e2e8f0;
    }
    
    .table th {
      border-color: var(--border-color);
      color: #94a3b8;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }
    
    .table td {
      border-color: var(--border-color);
      vertical-align: middle;
    }
    
    .badge {
      border-radius: 8px;
      font-weight: 500;
    }
    
    .chart-container {
      position: relative;
      height: 300px;
      margin: 1rem 0;
    }
    
    .sidebar {
      background: var(--card-bg);
      border-radius: 20px;
      padding: 2rem;
      height: fit-content;
    }
    
    .nav-link {
      color: #94a3b8;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      margin-bottom: 0.5rem;
      transition: all 0.3s ease;
    }
    
    .nav-link:hover, .nav-link.active {
      background: var(--primary-gradient);
      color: #fff;
    }
    
    .modal-content {
      background: var(--card-bg);
      border: 1px solid var(--border-color);
      border-radius: 20px;
    }
    
    .modal-header {
      border-bottom: 1px solid var(--border-color);
    }
    
    .modal-footer {
      border-top: 1px solid var(--border-color);
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('dashboard') }}">
      <i class="bi bi-speedometer2 me-2"></i>Sistema CRUD
    </a>
    <div class="d-flex gap-2">
      <a class="btn btn-ghost btn-sm" href="{{ route('dashboard') }}">
        <i class="bi bi-speedometer2 me-1"></i>Dashboard
      </a>
      <a class="btn btn-ghost btn-sm" href="{{ route('clients.index') }}">
        <i class="bi bi-people me-1"></i>Clientes
      </a>
      <a class="btn btn-ghost btn-sm" href="{{ route('products.index') }}">
        <i class="bi bi-box me-1"></i>Productos
      </a>
    </div>
  </div>
</nav>

<main class="container py-4">
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Configuración de SweetAlert2 para tema oscuro
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
});

// Mostrar mensajes de éxito
@if(session('ok'))
  Toast.fire({
    icon: 'success',
    title: '{{ session('ok') }}'
  });
@endif

// Función para confirmar eliminación
function confirmDelete(url, name, type = 'elemento') {
  Swal.fire({
    title: '¿Estás seguro?',
    text: `¿Eliminar ${name}?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    background: '#1a1d29',
    color: '#e2e8f0'
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = url;
      
      const csrfToken = document.createElement('input');
      csrfToken.type = 'hidden';
      csrfToken.name = '_token';
      csrfToken.value = '{{ csrf_token() }}';
      
      const methodField = document.createElement('input');
      methodField.type = 'hidden';
      methodField.name = '_method';
      methodField.value = 'DELETE';
      
      form.appendChild(csrfToken);
      form.appendChild(methodField);
      document.body.appendChild(form);
      form.submit();
    }
  });
}
</script>
@stack('scripts')
</body>
</html>
