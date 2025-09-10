@php
    /**
     * Variables recibidas:
     * - $node    : objeto Organizacion con ->id, ->nombre, ->tipo, ->children
     * - $openIds : array<int> con IDs a “abrir” automáticamente (búsqueda)
     * - $search  : string de búsqueda (para resaltar)
     */

    // Resaltado del término buscado en el nombre
    $label = $node->nombre ?? '';
    if (!empty($search)) {
        $pattern = '/' . preg_quote($search, '/') . '/i';
        // Escapamos primero y luego inyectamos el mark
        $label = preg_replace($pattern, '<mark>$0</mark>', e($label));
    } else {
        $label = e($label);
    }

    $isOpen = !empty($openIds) && in_array($node->id, $openIds);
@endphp

<details @if($openIds && in_array($node->id,$openIds)) open @endif>
  <summary class="org-line">
    <span class="org-name fw-semibold text-dark">{{ $node->nombre }}</span>

    @if(!empty($node->tipo))
      <small class="badge rounded-pill org-chip">{{ ucfirst($node->tipo) }}</small>
    @endif

    {{-- ejemplo de etiqueta extra si la tienes --}}
    @if(!empty($node->alias))
      <small class="badge rounded-pill org-chip">{{ $node->alias }}</small>
    @endif
  </summary>

  @if(!empty($node->children))
    <ul class="org-list">
      @foreach($node->children as $child)
        <li>
          @include('organizaciones.partials.node', [
            'node'   => $child,
            'openIds'=> $openIds,
            'search' => $search
          ])
        </li>
      @endforeach
    </ul>
  @endif
</details>
