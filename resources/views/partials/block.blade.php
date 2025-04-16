@props(['block' => null])

@if($block)
  @switch($block['type'])
    @case('text')
      {!! $block['content'] !!}
      @break

    @case('layout-2')
      <div class="row">
        @foreach ([0, 1] as $colIndex)
          <div class="col-6">
            @foreach (collect($block['children'])->where('column_index', $colIndex)->all() as $item)
              @includeWhen($item, 'page-builder::partials.block', ['block' => $item])
            @endforeach
          </div>
        @endforeach
      </div>
      @break

    @case('layout-3')
      <div class="row">
        @foreach ([0, 1, 2] as $colIndex)
          <div class="col-4">
            @foreach (collect($block['children'])->where('column_index', $colIndex)->all() as $item)
              @includeWhen($item, 'page-builder::partials.block', ['block' => $item])
            @endforeach
          </div>
        @endforeach
      </div>
      @break
  @endswitch
@endif