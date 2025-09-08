@php
    // Espera: $node y $openIds
    $hasChildren = isset($node->children) && count($node->children) > 0;
    $isOpen = in_array($node->id, $openIds ?? []);
@endphp

<li class="mb-2">
    <details class="org-node" {{ $isOpen ? 'open' : '' }}>
        <summary class="org-card d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-start">
                @if($hasChildren)
                    <span class="org-caret me-2"></span>
                @else
                    <span class="org-caret me-2 opacity-0"></span>
                @endif
                <span class="fw-semibold" title="{{ $node->nombre }}">{{ $node->nombre }}</span>
            </div>
            @if($hasChildren)
                <span class="badge text-bg-secondary">{{ count($node->children) }} hijo{{ count($node->children)>1 ? 's' : '' }}</span>
            @endif
        </summary>

        @if ($hasChildren)
            <div class="org-children ms-3 ps-3 mt-1 border-start border-secondary">
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
    </details>
</li>
