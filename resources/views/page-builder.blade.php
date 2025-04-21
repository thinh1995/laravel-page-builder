<div class="page-builder" id="{{ $id }}">
  <div class="row">
    <div class="col-md-9">
      <h3>{{ __('page-builder.view.heading') }}</h3>
      @if (is_array($locales) && count($locales) > 1)
        <ul class="nav nav-tabs mb-3">
          @foreach ($locales as $locale)
            <li class="nav-item">
              <a class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                 href="#editor-{{ $locale }}">{{ __("page-builder.language.$locale") }}</a>
            </li>
          @endforeach
        </ul>
        <div class="tab-content">
          @foreach ($locales as $locale)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                 id="editor-{{ $locale }}-{{ $id }}">
              <div class="sortable-container mb-3" data-locale="{{ $locale }}"></div>
              <button type="button" class="btn btn-info mb-3 preview-btn" data-locale="{{ $locale }}"
                      data-bs-toggle="modal" data-bs-target="#preview-modal">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                  <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                  <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                </svg> {{ __('page-builder.view.preview') }}
              </button>
            </div>
          @endforeach
        </div>

        @foreach ($locales as $locale)
          <input type="hidden" name="blocks[{{ $locale }}]" id="blocks-{{ $locale }}-{{ $id }}">
        @endforeach
      @else
        <div class="sortable-container mb-3" data-locale="{{ $locales }}"></div>
        <button type="button" class="btn btn-info mb-3 preview-btn"
                data-locale="{{ $locales }}" data-bs-toggle="modal"
                data-bs-target="#preview-modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
          </svg> {{ __('page-builder.view.preview') }}
        </button>
        <input type="hidden" name="blocks[{{ $locales }}]" id="blocks-{{ $locales }}-{{ $id }}">
      @endif
    </div>
    <div class="col-md-3">
      <h3>{{ __('page-builder.view.list_blocks') }}</h3>
      <div id="block-list-{{ $id }}">
        @foreach($blocks as $block)
          <div class="block" data-page-builder-id="{{ $id }}"
               data-block-id="{{ $block->id }}" data-type="{{ $block->type }}"
               data-is-layout="{{ $block->is_layout }}">
            {!! $block->icon !!} {{ $block->name }}
          </div>
        @endforeach
      </div>
    </div>
  </div>
  @include('page-builder::partials.modal-preview')
</div>

@pushonce('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/thinhnx/page-builder/css/page-builder.min.css') }}">
@endpushonce

@pushonce('script')
  <script src="{{ asset('packages/thinhnx/page-builder/libs/SortableJS/Sortable.min.js') }}"></script>
  <script src="{{ asset('packages/thinhnx/page-builder/js/page-builder.min.js') }}"></script>
@endpushonce

@push('script')
  <script>
    document.addEventListener('DOMContentLoaded', async function () {
      await PageBuilder.init('{{ $id }}', @json($locales), @json($initialBlocks));
    });
  </script>
@endpush