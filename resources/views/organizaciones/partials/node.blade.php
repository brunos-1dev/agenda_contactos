@php
    // Este partial espera: $node (nodo actual) y $openIds (array de IDs abiertos cuando hay búsqueda)
    $hasChildren = isset($node->children) && count($node->children) > 0;
    $isOpen = in_array($node->id, $openIds ?? []);
    $collapseId = "org-node-{$node->id}";
@endphp

<li class="mb-2">
    <div class="card bg-dark text-light border-secondary">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-start">
                <div class="pe-2">
                    @if ($hasChildren)
                        <a class="text-decoration-none text-light"
                           data-bs-toggle="collapse"
                           href="#{{ $collapseId }}"
                           role="button"
                           aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                           aria-controls="{{ $collapseId }}">
                            {{-- Indicador simple (sin CSS extra): ▶ / ▼ --}}
                            <span class="me-2">{{ $isOpen ? '▼' : '▶' }}</span>
                            <span class="fw-semibold" title="{{ $node->nombre }}">{{ $node->nombre }}</span>
                        </a>
                    @else
                        <span class="fw-semibold" title="{{ $node->nombre }}">{{ $node->nombre }}</span>
                    @endif

                    <div class="text-muted small">
                        {{ $node->tipo ?? '—' }}
                    </div>
                </div>

                {{-- badge opcional con cantidad de hijos --}}
                @if ($hasChildren)
                    <span class="badge text-bg-secondary">{{ count($node->children) }} hijo{{ count($node->children)>1 ? 's' : '' }}</span>
                @endif
            </div>
        </div>
    </div>

    @if ($hasChildren)
        <div class="collapse {{ $isOpen ? 'show' : '' }} ms-3 border-start border-secondary ps-3 mt-1" id="{{ $collapseId }}">
            <ul class="list-unstyled mb-0">
                @foreach ($node->children as $child)
                    @include('organizaciones.partials.node', [
                        'node' => $child,
                        'openIds' => $openIds ?? []
                    ])
                @endforeach
            </ul>
        </div>
    @endif
</li>
