// Helper function
function b64DecodeUnicode(str) {
  return decodeURIComponent(atob(str)
    .split('')
    .map(function(c) {
      return '%' + ('00' + c.charCodeAt(0)
        .toString(16))
        .slice(-2);
    })
    .join(''));
}

// Initialize Sortable for the available blocklist
function initBlockList(pageBuilderId) {
  const blockList = document.getElementById(`block-list-${pageBuilderId}`);

  if (blockList) {
    Sortable.create(blockList, {
      group: {
        name: "blocks",
        pull: "clone",
        put: false
      },
      sort: false,
    });
  }
}

// Initialize Sortable for each language's editor
async function initEditors(pageBuilderId) {
  const pageBuilder = document.getElementById(pageBuilderId);

  pageBuilder.querySelectorAll(".sortable-container")
    .forEach((container) => {
      Sortable.create(container, {
        group: "blocks",
        animation: 150,
        onAdd: addBlockToColumn,
        onEnd: () => {
          updateBlocksInput(pageBuilderId, container.getAttribute("data-locale"));
        },
      });
    });
}

// Add a block to the column of layout
async function addBlockToColumn(evt) {
  let block = evt.item;
  const pageBuilderId = block.getAttribute("data-page-builder-id");
  const type = block.getAttribute("data-type");
  const is_layout = block.getAttribute("data-is-layout");
  block.innerHTML = await getBlockHtml(type);
  block.classList.remove("block");

  if (is_layout) {
    block.querySelectorAll(".sortable-column")
      .forEach((column) => {
        Sortable.create(column, {
          group: "blocks",
          onAdd: addBlockToColumn,
          onEnd: () =>
            updateBlocksInput(
              pageBuilderId,
              evt.to.closest(".sortable-container")
                .getAttribute("data-locale")
            ),
        });
      });
  }

  block.querySelector(".remove-block")
    .addEventListener("click", function() {
      block.remove();
      updateBlocksInput(
        pageBuilderId,
        evt.to.closest(".sortable-container")
          .getAttribute("data-locale")
      );
    });

  block
    .querySelector(".block-content")
    ?.addEventListener("input", () =>
      updateBlocksInput(
        pageBuilderId,
        evt.to.closest(".sortable-container")
          .getAttribute("data-locale")
      )
    );
}

// Get HTML of the block from the server
async function getBlockHtml(type, content = "") {
  let html = null;
  await fetch(route("page-builder.render-block"), {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]')
        .attr("content"),
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify({
      type: type,
      content: content
    }),
  })
    .then((response) => response.text())
    .then((text) => {
      html = b64DecodeUnicode(JSON.parse(text)
        .data);
    });

  return html;
}

// Update blocks data into the hidden input
function updateBlocksInput(pageBuilderId, locale) {
  const pageBuilder = document.getElementById(pageBuilderId);
  const container = pageBuilder.querySelector(`.sortable-container[data-locale="${locale}"]`);
  let blocks = getBlocksDataFromContainer(container);

  document.getElementById(`blocks-${locale}-${pageBuilderId}`)
    .value = JSON.stringify(blocks);
  return blocks;
}

// Get blocks data from a container
function getBlocksDataFromContainer(container, column_index = 0) {
  let blockEditors = [];
  let blocks = [];
  let order = 0;

  if (!container || !container.childElementCount) {
    return {};
  }

  container.childNodes.forEach((item) => {
    blockEditors.push(item.querySelector(".block-editor"));
  });

  for (const block of blockEditors) {
    let children = [];
    const type = block.closest("[data-type]")
      .getAttribute("data-type");
    const is_layout = block
      .closest("[data-type]")
      .getAttribute("data-is-layout");
    const id = block.closest("[data-type]")
      .getAttribute("data-id");
    let content = block.querySelector(".block-content")
      ?.value || "";

    if (is_layout) {
      let row = block.querySelector(".row");
      let columns = [];

      for (let i = 0; i < row.children.length; i++) {
        columns.push(row.children[i].querySelector(".sortable-column"));
      }

      columns.forEach((col, index) => {
        let items = getBlocksDataFromContainer(col, index);
        if (Array.isArray(items)) {
          children.push(...getBlocksDataFromContainer(col, index));
        }
      });
    }

    blocks.push({
      block_id: id,
      type: type,
      content: content,
      column_index: column_index,
      order: order++,
      children: children,
    });
  }

  return blocks;
}

// Initialize blocks from database data
async function initBlocksFromData(pageBuilderId, locale, blocksData) {
  const pageBuilder = document.getElementById(pageBuilderId);
  const container = pageBuilder.querySelector(
    `.sortable-container[data-locale="${locale}"]`
  );
  await createBlockFromData(pageBuilderId, locale, blocksData, container);
}

async function createBlockFromData(
  pageBuilderId,
  locale,
  blocksData,
  container
) {
  for (let block of blocksData) {
    const div = document.createElement("div");
    div.setAttribute("data-page-builder-id", pageBuilderId);
    div.setAttribute("data-type", block.block.type);
    div.setAttribute("data-id", block.block_id);
    div.setAttribute("data-is-layout", block.block.is_layout);
    div.innerHTML = await getBlockHtml(
      block.block.type,
      block.content
    );
    container.appendChild(div);

    div.querySelector(".remove-block")
      .addEventListener("click", function() {
        div.remove();
        updateBlocksInput(pageBuilderId, locale);
      });
    div.querySelector(".block-content")
      ?.addEventListener("input", () =>
        updateBlocksInput(pageBuilderId, locale)
      );

    if (block.block.is_layout) {
      let colIndex = 0;
      for (let column of div.querySelectorAll(".sortable-column")) {
        if (block.children.length) {
          await createBlockFromData(
            pageBuilderId,
            locale,
            block.children.filter(
              (item) => item.column_index === colIndex
            ),
            column
          );
        }
        colIndex++;
      }

      div.querySelectorAll(".sortable-column")
        .forEach(
          (column) => {
            Sortable.create(column, {
              group: "blocks",
              onAdd: addBlockToColumn,
              onEnd: () =>
                updateBlocksInput(
                  pageBuilderId,
                  locale
                ),
            });
          }
        );
    }
  }
}

// Process preview
function initPreview(pageBuilderId, callback = () => {}) {
  const pageBuilder = document.getElementById(pageBuilderId);
  let currentLocale = null;

  pageBuilder.querySelectorAll(".preview-btn")
    .forEach((btn) => {
      btn.addEventListener("click", function() {
        currentLocale = this.getAttribute("data-locale");
        const blocks = updateBlocksInput(pageBuilderId, currentLocale);
        const iframe = document.getElementById("preview-iframe");

        fetch(route("page-builder.preview"), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector(
              'meta[name="csrf-token"]'
            )
              .content,
            "X-Requested-With": "XMLHttpRequest",
          },
          body: JSON.stringify({
            locale: currentLocale,
            blocks: blocks,
            context: callback(),
          }),
        })
          .then((response) => response.text())
          .then((text) => {
            iframe.srcdoc = b64DecodeUnicode(JSON.parse(text)
              .data);
            updateIframeSize("desktop");
          })
          .catch((error) => console.error("Error:", error));
      });
    });

  document
    .querySelectorAll('input[name="device-select"]')
    .forEach(function(device) {
      device.addEventListener("change", function() {
        updateIframeSize(
          document.querySelector(
            'input[name="device-select"]:checked'
          )
            .value
        );
      });
    });
}

// Process form submission
function initFormSubmit(pageBuilderId, locales) {
  const pageBuilder = document.getElementById(pageBuilderId);

  pageBuilder.closest('form')
    .addEventListener("submit", function() {
      locales.forEach((locale) => updateBlocksInput(pageBuilderId, locale));
      document
        .querySelectorAll('input[name="device-select"]')
        .forEach(function(device) {
          device.disabled = true;
        });
    });
}

// Adjust the size of the iframe
function updateIframeSize(device) {
  const iframe = document.getElementById("preview-iframe");
  const container = document.getElementById("iframe-container");

  switch (device) {
    case "desktop":
      iframe.style.width = "100%";
      container.style.width = "100%";
      container.style.maxWidth = "none";
      break;
    case "tablet":
      iframe.style.width = "768px";
      container.style.maxWidth = "768px";
      break;
    case "mobile":
      iframe.style.width = "375px";
      container.style.maxWidth = "375px";
      break;
  }
}

// Initialize the Page Builder
async function initPageBuilder(
  pageBuilderId,
  initialBlocks = {},
  getContextCallback = () => {}
) {
  initBlockList(pageBuilderId);
  await initEditors(pageBuilderId);
  initPreview(pageBuilderId, getContextCallback);
  initFormSubmit(pageBuilderId, Object.keys(initialBlocks));

  // Initialize blocks from database data
  for (const locale in initialBlocks) {
    if (initialBlocks[locale].length > 0) {
      await initBlocksFromData(pageBuilderId, locale, initialBlocks[locale]);
    }
  }
}
