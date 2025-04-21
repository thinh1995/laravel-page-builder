# Changelog

All notable changes to `Laravel Page Builder` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- Add a duplicate feature in a block.
- Optimize the queries using in HasBlocks trait.
- Check for memory leaks.

## [1.1.0] 2025-04-21

### Added

- Create a JavaScript class PageBuilder with support for two hooks: `blocksRendered()` and `beforePreview()`.
- Add minified JavaScript and CSS files in the assets.

### Changed

- Replace init page builder function to `PageBuilder.init(pbId, locales, blockItems)` with parameters:
    - **pbId** is a random id of the page builder element.
    - **locales** are languages maybe string or array, ex: `['en', 'vi']` or `en`.
    - **blockItems** is the initial block collection of the model.
- Update style and add animation when drag and drop.

### Removed

- Remove transformers of models.

## [1.0.2] - 2025-04-18

### Added

- Use cache for get the list of blocks when rendering Page Builder.
- Add cache config.

### Changed

- Rename function `getBlockItemsByLocale()` in `HasBlocks` trait to `getBlockItems()`.

## [1.0.1] - 2025-04-17

### Added

- Add DocBlock for functions.

### Changed

- Update JS supports multiple Page Builder views on a page.
- Auto finds the closet form when init Page Builder views.
- Remove unused files.

## [1.0.0] - 2025-04-16

### Added

- Initial release 🎉.
