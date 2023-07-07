<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <a class="nav-link" href="/">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                @can('clients-list')
                <div class="sb-sidenav-menu-heading">Clientes</div>
                
                    
                
                <a class="nav-link" href="{{ route('clients.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Clientes
                </a>
                @endcan
                
                @can('root-list')
                {{-- <div class="sb-sidenav-menu-heading">Estrategias</div>
                <a class="nav-link" href="{{ route('estrategia.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                    Estrategias
                </a> --}}



                @can('clients-list')
                <div class="sb-sidenav-menu-heading">Configuracion</div>
                
                    
                
                
                @endcan
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-tools"></i></div>
                    Configuracion
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        
                        <a class="nav-link" href="{{ route('users.index') }}"><div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>Usuarios</a>
                    </nav>
                </div>
                
                @endcan

            </div>
        </div>
        <div class="sb-sidenav-footer">
            @guest()
                @else
                <div class="small">Logged in as:</div>
                {{ Auth::user()->name }}
            @endguest
            
        </div>
    </nav>
</div>