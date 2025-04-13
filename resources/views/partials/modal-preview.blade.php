<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="previewModalLabel">{{ __('page-builder.view.list_blocks') }}</h5>
        <div class="switch-field">
          <input type="radio" id="device-desktop" name="device-select" value="desktop" checked/>
          <label for="device-desktop">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="icon icon-tabler icons-tabler-outline icon-tabler-device-desktop">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M3 5a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-10z"/>
              <path d="M7 20h10"/>
              <path d="M9 16v4"/>
              <path d="M15 16v4"/>
            </svg>
          </label>
          <input type="radio" id="device-tablet" name="device-select" value="tablet"/>
          <label for="device-tablet">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="icon icon-tabler icons-tabler-outline icon-tabler-device-ipad">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M18 3a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2z"/>
              <path d="M9 18h6"/>
            </svg>
          </label>
          <input type="radio" id="device-mobile" name="device-select" value="mobile"/>
          <label for="device-mobile">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="icon icon-tabler icons-tabler-outline icon-tabler-device-mobile">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M6 5a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2v-14z"/>
              <path d="M11 4h2"/>
              <path d="M12 17v.01"/>
            </svg>
          </label>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex justify-content-center">
        <div id="iframe-container" class="text-center">
          <iframe id="preview-iframe" class="preview-iframe"></iframe>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">{{ __('page-builder.view.close') }}</button>
      </div>
    </div>
  </div>
</div>