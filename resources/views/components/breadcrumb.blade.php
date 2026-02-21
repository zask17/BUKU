{{-- 
  Breadcrumb Component
  
  Penggunaan:
  @include('components.breadcrumb', [
      'items' => [
          ['url' => route('admin.dashboard'), 'label' => 'Dashboard'],
          ['url' => route('admin.buku'), 'label' => 'Buku'],
          ['label' => 'Detail Buku', 'active' => true]
      ]
  ])
--}}

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        @foreach($items as $item)
            @if(isset($item['active']) && $item['active'])
                <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
