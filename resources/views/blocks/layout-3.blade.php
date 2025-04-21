<div class="block-editor">
  <div class="block-title">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
         class="icon icon-tabler icons-tabler-outline icon-tabler-arrows-move">
      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
      <path d="M18 9l3 3l-3 3"/>
      <path d="M15 12h6"/>
      <path d="M6 9l-3 3l3 3"/>
      <path d="M3 12h6"/>
      <path d="M9 18l3 3l3 -3"/>
      <path d="M12 15v6"/>
      <path d="M15 6l-3 -3l-3 3"/>
      <path d="M12 3v6"/>
    </svg>
    {{ $block->name }}
  </div>
  <div class="row">
    <div class="col-md-4 col-sm-12">
      <div class="sortable-column" data-column="0"></div>
    </div>
    <div class="col-md-4 col-sm-12">
      <div class="sortable-column" data-column="1"></div>
    </div>
    <div class="col-md-4 col-sm-12">
      <div class="sortable-column" data-column="2"></div>
    </div>
  </div>
  <div class="block-actions text-center">
    <button type="button" class="block-remove">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
           stroke="currentColor"
           stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
           class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M4 7l16 0"/>
        <path d="M10 11l0 6"/>
        <path d="M14 11l0 6"/>
        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
      </svg> {{ __('page-builder.view.remove') }}
    </button>
  </div>
</div>