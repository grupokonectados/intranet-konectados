<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="">
        <h1 class="mt-4">@yield('title-section')</h1>
        <ol class="breadcrumb ">
            <li class="breadcrumb-item active">@yield('breads')</li>
        </ol>
    </div>
    <div class="">

        <div class="btn-group" role="group" aria-label="Basic example">
            @yield('btn-back')
            @yield('btn-create')
          </div>
        
       
    </div>
</div>
