class PageBuilder {
  static config = {
    renderBlockUrl: '/page-builder/render-block',
    previewUrl: '/page-builder/preview',
    previewContext: {}
  };

  static b64Decode(str) {
    try {
      return decodeURIComponent(atob(str).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
    } catch (e) {
      console.error('Error:', e);
      return '';
    }
  };

  static blocksRendered() {};

  static beforePreview() {};

  static getBlockContent(block) {
    return block.querySelector('.block-content')?.value || '';
  }

  static async renderBlockItems(pbId, locale, data, container) {
    const fragment = document.createDocumentFragment();

    for (const item of data) {
      const div = this.createBlockElement(pbId, item,
          await this.getBlockHtml(item.type, item.content));

      this.attachBlockEventListeners(pbId, div, item.locale);

      if (item.is_layout) {
        await this.setupLayoutBlock(pbId, item.locale, div,
            item.children);
      }

      fragment.appendChild(div);
    }

    container.appendChild(fragment);

    this.blocksRendered()?.();
  };

  static createBlockElement(pbId, block, html) {
    const div = document.createElement('div');
    div.dataset.pageBuilderId = pbId;
    div.dataset.type = block.type;
    div.dataset.blockId = block.block_id;
    div.dataset.isLayout = block.is_layout;
    div.innerHTML = html;

    return div;
  };

  static attachBlockEventListeners(pbId, block, locale) {
    block.querySelector('.block-remove')?.addEventListener('click', () => {
      block.remove();
      this.updateBlockItems(pbId, locale);
    });

    block.querySelector('.block-content')?.addEventListener('input', () =>
        this.updateBlockItems(pbId, locale), {passive: true});
  };

  static async addBlockToColumn(evt) {
    const block = evt.item;

    if (evt.from.id.startsWith('block-list-')) {
      block.innerHTML = await PageBuilder.getBlockHtml(block.dataset.type);
      block.classList.remove('block');
      block.classList.add('sortable-dropped');

      if (block.getAttribute('data-is-layout')) {
        const pbId = block.getAttribute('data-page-builder-id');
        const locale = evt.to.closest('.sortable-container').
            getAttribute('data-locale');
        block.querySelectorAll('.sortable-column').forEach((col) => {
          Sortable.create(col, {
            group: {
              name: `editor-${pbId}`,
              put: [`block-list-${pbId}`, `editor-${pbId}`]
            },
            animation: 150,
            handle: '.block-title',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onAdd: PageBuilder.addBlockToColumn,
            onEnd: () => {
              PageBuilder.removeHighlightDroppableAreas();
              PageBuilder.updateBlockItems(pbId, locale);
            },
            onStart: () => this.highlightDroppableAreas(pbId),
            onOver: (evt) => this.handleDragOver(evt, col),
            onOut: () => this.removeHighlight(col)
          });
        });
      }
    }

    PageBuilder.attachBlockEventListeners(
        block.dataset.pageBuilderId,
        block,
        evt.to.closest('.sortable-container').dataset.locale
    );

    PageBuilder.blocksRendered()?.();
    block.classList.remove('sortable-dropped');
  }

  static async setupLayoutBlock(pbId, locale, div, children) {
    const cols = div.querySelectorAll('.sortable-column');

    await Promise.all(Array.from(cols).map(async (col, index) => {
      const colChildren = children.filter(
          child => child.column_index === index);
      if (colChildren.length) {
        await this.renderBlockItems(pbId, locale, colChildren, col);
      }

      Sortable.create(col, {
        group: {
          name: `editor-${pbId}`,
          put: [`block-list-${pbId}`, `editor-${pbId}`]
        },
        animation: 150,
        handle: '.block-title',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onAdd: this.addBlockToColumn,
        onEnd: () => {
          this.removeHighlightDroppableAreas();
          this.updateBlockItems(pbId, locale);
        },
        onStart: () => this.highlightDroppableAreas(pbId),
        onOver: (evt) => this.handleDragOver(evt, col),
        onOut: () => this.removeHighlight(col)
      });
    }));
  }

  static updateIframeSize(device) {
    const iframe = document.getElementById('preview-iframe');
    const container = document.getElementById('iframe-container');
    const sizes = {
      desktop: {width: '100%', maxWidth: 'none'},
      tablet: {width: '768px', maxWidth: '768px'},
      mobile: {width: '375px', maxWidth: '375px'}
    }[device];

    if (sizes) {
      iframe.style.width = sizes.width;
      container.style.width = sizes.width;
      container.style.maxWidth = sizes.maxWidth;
    }
  };

  static updateBlockItems(pbId, locale) {
    const container = document.getElementById(pbId).querySelector(
        `.sortable-container[data-locale="${locale}"]`);
    let blocks = this.getBlockItems(container);
    document.getElementById(`blocks-${locale}-${pbId}`).value = JSON.stringify(
        blocks);
    return blocks;
  };

  static async getBlockHtml(type, content = '') {
    try {
      const response = await fetch(this.config.renderBlockUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector(
              'meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({type, content})
      });
      const {data} = await response.json();
      return this.b64Decode(data);
    } catch (error) {
      console.error('Error:', error);
      return '';
    }
  };

  static highlightDroppableAreas(pbId) {
    document.querySelectorAll(
        `.sortable-container[data-page-builder-id="${pbId}"]`).
        forEach((container) => {
          container.classList.add('droppable-highlight');
        });

    document.querySelectorAll(
        `[data-page-builder-id="${pbId}"][data-is-layout="true"] .sortable-column`).
        forEach((column) => {
          column.classList.add('droppable-highlight');
        });
  }

  static removeHighlightDroppableAreas() {
    document.querySelectorAll('.droppable-highlight').forEach((el) => {
      el.classList.remove('droppable-highlight');
    });
  }

  static handleDragOver(evt, container) {
    container.classList.add('droppable-highlight');
  }

  static removeHighlight(container) {
    container.classList.remove('droppable-highlight');
  }

  static initBlockList(pbId) {
    const blockList = document.getElementById(`block-list-${pbId}`);
    if (blockList) {
      Sortable.create(blockList, {
        group: {
          name: `block-list-${pbId}`, put: false, pull: 'clone'
        },
        sort: false,
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onStart: () => this.highlightDroppableAreas(pbId),
        onEnd: () => this.removeHighlightDroppableAreas()
      });
    }
  };

  static async initBlockItems(pbId, locale, data) {
    const container = document.getElementById(pbId).querySelector(
        `.sortable-container[data-locale="${locale}"]`);
    await this.renderBlockItems(pbId, locale, data, container);
  };

  static initEditors(pbId) {
    const pb = document.getElementById(pbId);
    pb.querySelectorAll('.sortable-container').
        forEach((container) => {
          Sortable.create(container, {
            group: {
              name: `editor-${pbId}`,
              put: [`block-list-${pbId}`, `editor-${pbId}`]
            },
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.block-title',
            onStart: () => this.highlightDroppableAreas(pbId),
            onAdd: this.addBlockToColumn,
            onEnd: () => {
              this.removeHighlightDroppableAreas();
              this.updateBlockItems(pbId,
                  container.getAttribute('data-locale'));
            },
            onOver: (evt) => this.handleDragOver(evt, container),
            onOut: () => this.removeHighlight(container)
          });
        });
  };

  static initPreview(pbId) {
    const pb = document.getElementById(pbId);
    pb.querySelectorAll('.preview-btn').forEach(btn => {
      btn.addEventListener('click', async () => {
        const locale = btn.dataset.locale;
        const blocks = this.updateBlockItems(pbId, locale);
        const iframe = document.getElementById('preview-iframe');

        this.beforePreview?.();

        try {
          const response = await fetch(this.config.previewUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector(
                  'meta[name="csrf-token"]').content,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(
                {locale, blocks, context: this.config.previewContext})
          });
          const {data} = await response.json();
          iframe.srcdoc = this.b64Decode(data);
          document.getElementById('device-desktop').checked = true;
          this.updateIframeSize('desktop');
        } catch (error) {
          console.error('Error:', error);
        }
      });
    });

    document.querySelectorAll('input[name="device-select"]').forEach(device => {
      device.addEventListener('change', () =>
              this.updateIframeSize(document.querySelector(
                  'input[name="device-select"]:checked').value),
          {passive: true}
      );
    });
  };

  static initFormSubmit(pbId, locales = []) {
    const form = document.getElementById(pbId).closest('form');
    form.addEventListener('submit', () => {
      locales.forEach((locale) => this.updateBlockItems(pbId, locale));
      document.querySelectorAll('input[name="device-select"]').
          forEach((device) => device.disabled = true);
    });
  };

  static async init(pbId, locales, blockItems) {
    locales = Array.isArray(locales) ? locales : [locales];

    this.initBlockList(pbId);
    this.initEditors(pbId);
    this.initPreview(pbId);
    this.initFormSubmit(pbId, locales);

    await Promise.all(locales.map(locale =>
        blockItems[locale]?.length ? this.initBlockItems(pbId, locale,
            blockItems[locale]) : null
    ));
  };

  static getBlockItems(container, colIndex = 0) {
    if (!container?.childElementCount) return [];

    return Array.from(container.children).map((item, order) => {
      const block = item.querySelector('.block-editor');
      if (!block) return null;

      const parent = block.closest('[data-type]');
      const children = parent.dataset.isLayout
          ? Array.from(block.querySelector('.row').children).
              flatMap((col, index) => this.getBlockItems(
                  col.querySelector('.sortable-column'), index))
          : [];

      return {
        block_id: parent.dataset.blockId,
        type: parent.dataset.type,
        content: this.getBlockContent(block),
        column_index: colIndex,
        order,
        children
      };
    }).filter(Boolean);
  };
}