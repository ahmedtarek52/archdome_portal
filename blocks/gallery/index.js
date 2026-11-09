(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, MediaUpload, MediaUploadCheck, RichText, useBlockProps } = wp.blockEditor;
  const { Button, PanelBody, ToggleControl } = wp.components;
  const { createElement: el, Fragment, useRef } = wp.element;
  const { __, sprintf } = wp.i18n;
  const keys = ['firstColumnIds', 'middleColumnIds', 'lastColumnIds'];
  let nextInstance = 0;

  const Thumbnail = ({ id }) => {
    const media = wp.data.useSelect((select) => select('core').getMedia(id), [id]);
    if (media?.mime_type?.startsWith('video/')) {
      return el('span', { className: 'archdome-gallery-editor__video' }, __('Video', 'archdome-portal'));
    }
    const url = media?.media_details?.sizes?.thumbnail?.source_url || media?.source_url;
    return url ? el('img', { src: url, alt: media.alt_text || '' }) : el('span', {}, '#' + id);
  };

  registerBlockType('archdome/gallery', {
    edit: ({ attributes, setAttributes }) => {
      const instance = useRef(null);
      if (instance.current === null) instance.current = ++nextInstance;
      const idPrefix = 'archdome-gallery-' + instance.current;
      const layout = attributes.layout === 'editorial' ? 'editorial' : 'default';
      const columns = attributes.customColumns ? keys.map((key) => attributes[key] || []) : [[], [], []];
      if (!attributes.customColumns) {
        (attributes.mediaIds || []).forEach((id, index) => {
          const position = index % 11;
          columns[position < 4 ? 0 : position < 7 ? 1 : 2].push(id);
        });
      }
      const fallbackIds = attributes.customColumns ? columns.flat() : (attributes.mediaIds || []);
      const editorialIds = attributes.editorialMediaIds?.length
        ? Array.from({ length: 6 }, (_, index) => attributes.editorialMediaIds[index] || 0)
        : Array.from({ length: 6 }, (_, index) => fallbackIds[index] || 0);
      const updateColumn = (index, ids) => {
        const next = columns.map((column, columnIndex) => columnIndex === index ? [...new Set(ids)].slice(0, 40) : column);
        setAttributes({ customColumns: true, ...Object.fromEntries(keys.map((key, columnIndex) => [key, next[columnIndex]])) });
      };
      const updateEditorial = (index, id) => {
        const next = [...editorialIds];
        next[index] = id;
        setAttributes({ editorialMediaIds: next });
      };
      const labels = [__('First column', 'archdome-portal'), __('Middle column (sticky)', 'archdome-portal'), __('Last column', 'archdome-portal')];
      return el(Fragment, {},
        el(InspectorControls, {},
          el(PanelBody, { title: __('General', 'archdome-portal'), initialOpen: true },
            el(ToggleControl, {
              label: __('Center title', 'archdome-portal'),
              checked: !!attributes.centerTitle,
              onChange: (value) => setAttributes({ centerTitle: value })
            })
          )
        ),
        el('section', useBlockProps({ className: 'archdome-gallery-editor' + (attributes.centerTitle ? ' archdome-gallery-editor--center-title' : '') }),
          el('div', { className: 'archdome-gallery-editor__tabs', role: 'tablist', 'aria-label': __('Gallery theme', 'archdome-portal') },
            [
              { value: 'default', label: __('Default', 'archdome-portal') },
              { value: 'editorial', label: __('Editorial rows', 'archdome-portal') }
            ].map((tab) => el(Button, {
              key: tab.value,
              id: idPrefix + '-tab-' + tab.value,
              role: 'tab',
              tabIndex: layout === tab.value ? 0 : -1,
              'aria-selected': layout === tab.value,
              'aria-controls': idPrefix + '-panel',
              className: 'archdome-gallery-editor__tab' + (layout === tab.value ? ' is-active' : ''),
              onClick: () => setAttributes({ layout: tab.value }),
              onKeyDown: (event) => {
                if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
                event.preventDefault();
                const next = event.key === 'ArrowRight' ? event.currentTarget.nextElementSibling || event.currentTarget.parentElement.firstElementChild : event.currentTarget.previousElementSibling || event.currentTarget.parentElement.lastElementChild;
                next.focus();
                setAttributes({ layout: next.id.endsWith('editorial') ? 'editorial' : 'default' });
              }
            }, tab.label))
          ),
          el(RichText, { tagName: 'h2', placeholder: __('Gallery heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
          el('div', { id: idPrefix + '-panel', role: 'tabpanel', 'aria-labelledby': idPrefix + '-tab-' + layout },
          layout === 'editorial'
            ? el(Fragment, {},
              el('p', {}, __('Choose up to six images or videos. The cards appear in two rows on wide screens.', 'archdome-portal')),
              el('div', { className: 'archdome-gallery-editor__rows' }, [0, 1].map((row) =>
                el('div', { key: row, className: 'archdome-gallery-editor__row archdome-gallery-editor__row--' + (row + 1) },
                  editorialIds.slice(row * 3, row * 3 + 3).map((id, position) => {
                    const index = row * 3 + position;
                    return el('div', { key: index, className: 'archdome-gallery-editor__card' },
                      el('span', { className: 'archdome-gallery-editor__card-label' }, sprintf(__('Card %d', 'archdome-portal'), index + 1)),
                      id ? el(Thumbnail, { id }) : el('span', { className: 'archdome-gallery-editor__empty' }, __('No media selected', 'archdome-portal')),
                      el(MediaUploadCheck, {}, el(MediaUpload, {
                        allowedTypes: ['image', 'video'],
                        value: id || undefined,
                        onSelect: (media) => updateEditorial(index, media.id),
                        render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, id ? __('Replace media', 'archdome-portal') : __('Choose media', 'archdome-portal'))
                      })),
                      id ? el(Button, { variant: 'link', isDestructive: true, onClick: () => updateEditorial(index, 0) }, __('Remove media', 'archdome-portal')) : null
                    );
                  })
                )
              ))
            )
            : el(Fragment, {},
              el('p', {}, __('Total images: ', 'archdome-portal') + columns.reduce((total, column) => total + column.length, 0)),
              el('p', {}, __('Choose any number of images per column, up to 40. Keep the middle column shorter than the side columns for the sticky scroll effect.', 'archdome-portal')),
              el('div', { className: 'archdome-gallery-editor__columns' }, columns.map((ids, index) =>
                el('div', { key: keys[index], className: 'archdome-gallery-editor__column' },
                  el('h3', {}, labels[index]),
                  el('p', {}, __('Images: ', 'archdome-portal') + ids.length + ' / 40'),
                  el(MediaUploadCheck, {}, el(MediaUpload, {
                    multiple: true, gallery: true, allowedTypes: ['image'], value: ids,
                    onSelect: (media) => updateColumn(index, media.map((item) => item.id)),
                    render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, __('Choose / edit images', 'archdome-portal'))
                  })),
                  el('ol', { className: 'archdome-gallery-editor__images' }, ids.map((id, imageIndex) => {
                    const move = (offset) => {
                      const reordered = [...ids];
                      [reordered[imageIndex], reordered[imageIndex + offset]] = [reordered[imageIndex + offset], reordered[imageIndex]];
                      updateColumn(index, reordered);
                    };
                    return el('li', { key: id },
                      el(Thumbnail, { id }),
                      el('div', { className: 'archdome-gallery-editor__actions' },
                        el(Button, { icon: 'arrow-up-alt2', label: __('Move image up', 'archdome-portal'), disabled: imageIndex === 0, onClick: () => move(-1) }),
                        el(Button, { icon: 'arrow-down-alt2', label: __('Move image down', 'archdome-portal'), disabled: imageIndex === ids.length - 1, onClick: () => move(1) }),
                        el(Button, { icon: 'no-alt', label: __('Remove image', 'archdome-portal'), isDestructive: true, onClick: () => updateColumn(index, ids.filter((_, i) => i !== imageIndex)) })
                      )
                    );
                  })),
                  ids.length > 0 && el(Button, { variant: 'tertiary', isDestructive: true, onClick: () => updateColumn(index, []) }, __('Clear column', 'archdome-portal'))
                )
              ))
            )
          )
        )
      );
    },
    save: () => null
  });
})();
