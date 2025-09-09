@php
    // Espera: $node (objeto con ->id, ->nombre, ->tipo, ->children[]), y $openIds (array de IDs a abrir)
    $hasChildren = isset($node->children) && count($node->children) > 0;
    $isOpen = in_array($node->id, $openIds ?? []);
    $collapseId = "org-node-{$node->id}";
@endphp

<li class="mb-2">
    <div class="org-card">
        <div class="org-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="pe-2">
                    @if ($hasChildren)
                        <button
                            class="org-toggle btn btn-link p-0 text-light text-decoration-none"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#{{ $collapseId }}"
                            aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                            aria-controls="{{ $collapseId }}"
                        >
                            <span class="org-caret"></span>
                            <span class="org-title" title="{{ $node->nombre }}">{{ $node->nombre }}</span>
                        </button>
                    @else
                        <span class="org-title" title="{{ $node->nombre }}">{{ $node->nombre }}</span>
                    @endif

                    @if(!empty($node->tipo))
                        <div class="org-sub">{{ $node->tipo }}</div>
                    @endif
                </div>

                @if ($hasChildren)
                    <span class="org-badge">{{ count($node->children) }} {{ count($node->children) === 1 ? 'hijo' : 'hijos' }}</span>
                @endif
            </div>
        </div>
    </div>

    @if ($hasChildren)
        <div class="collapse org-children mt-1 {{ $isOpen ? 'show' : '' }}" id="{{ $collapseId }}">
            <ul class="org-tree">
                @foreach ($node->children as $child)
                    @include('organizaciones.partials.node', [
                        'node'    => $child,
                        'openIds' => $openIds ?? [],
                    ])
                @endforeach
            </ul>
        </div>
    @endif
</li>
