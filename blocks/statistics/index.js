(() => {
  'use strict';

  const { registerBlockType } = wp.blocks;
  const { InspectorControls, RichText, useBlockProps } = wp.blockEditor;
  const { Button, PanelBody, SelectControl, TextControl, TextareaControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  const iconPaths = {
    building: ['M4 22V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v18', 'M2 22h20', 'M8 6h2', 'M14 6h2', 'M8 10h2', 'M14 10h2', 'M8 14h2', 'M14 14h2', 'M10 22v-4h4v4'],
    home: ['m3 10 9-7 9 7', 'M5 9v12h14V9', 'M9 21v-7h6v7'],
    location: ['M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z', 'M12 7.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z'],
    key: ['M21 7a5 5 0 1 1-9.6 2H3v4h3v3h3v3h4v-8.4A5 5 0 0 1 21 7Z', 'M17 7h.01'],
    area: ['M4 4h16v16H4z', 'M8 16h8V8']
  };

  const renderIcon = (icon) => iconPaths[icon] ? el('span', { className: 'archdome-statistics__icon', 'aria-hidden': true },
    el('svg', { viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', strokeWidth: 1.75, strokeLinecap: 'round', strokeLinejoin: 'round', focusable: false },
      iconPaths[icon].map((path, index) => el('path', { d: path, key: index }))
    )
  ) : null;

  registerBlockType('archdome/statistics', {
    edit: ({ attributes, setAttributes }) => {
      const items = Array.isArray(attributes.items) ? attributes.items : [];
      const headingTag = attributes.headingLevel === 3 ? 'h3' : 'h2';
      const update = (index, key, value) => setAttributes({
        items: items.map((item, itemIndex) => itemIndex === index ? { ...item, [key]: value } : item)
      });

      return el(Fragment, {},
        el(InspectorControls, {},
          el(PanelBody, { title: __('Statistics settings', 'archdome-portal') },
            el(SelectControl, {
              label: __('Heading level', 'archdome-portal'),
              value: String(attributes.headingLevel || 2),
              options: [
                { label: __('Heading 2', 'archdome-portal'), value: '2' },
                { label: __('Heading 3', 'archdome-portal'), value: '3' }
              ],
              onChange: (value) => setAttributes({ headingLevel: Number(value) })
            }),
            el(TextControl, {
              label: __('Eyebrow (optional)', 'archdome-portal'),
              value: attributes.eyebrow || '',
              onChange: (value) => setAttributes({ eyebrow: value })
            }),
            el(TextareaControl, {
              label: __('Supporting text (optional)', 'archdome-portal'),
              value: attributes.text || '',
              onChange: (value) => setAttributes({ text: value })
            })
          )
        ),
        el('section', useBlockProps({ className: 'archdome-statistics archdome-statistics-editor' }),
          el('div', { className: 'archdome-statistics__inner' },
            el('div', { className: 'archdome-statistics__intro' },
              attributes.eyebrow ? el('p', { className: 'archdome-statistics__eyebrow' }, attributes.eyebrow) : null,
              el(RichText, {
                tagName: headingTag,
                className: 'archdome-statistics__heading',
                placeholder: __('Statistics heading', 'archdome-portal'),
                value: attributes.heading,
                allowedFormats: [],
                onChange: (value) => setAttributes({ heading: value })
              }),
              attributes.text ? el('p', { className: 'archdome-statistics__text' }, attributes.text) : null
            ),
            el('div', { className: 'archdome-statistics-editor__content' },
              items.length ? el('div', { className: 'archdome-statistics__list' },
                items.map((item, index) => el('div', { className: 'archdome-statistics__item', key: index },
                  renderIcon(item.icon),
                  el(SelectControl, {
                    label: __('Icon', 'archdome-portal'),
                    value: item.icon || '',
                    options: [
                      { label: __('No icon', 'archdome-portal'), value: '' },
                      { label: __('Building', 'archdome-portal'), value: 'building' },
                      { label: __('Home', 'archdome-portal'), value: 'home' },
                      { label: __('Location', 'archdome-portal'), value: 'location' },
                      { label: __('Key', 'archdome-portal'), value: 'key' },
                      { label: __('Area', 'archdome-portal'), value: 'area' }
                    ],
                    onChange: (value) => update(index, 'icon', value)
                  }),
                  el(TextControl, {
                    label: __('Value', 'archdome-portal'),
                    value: item.value || '',
                    onChange: (value) => update(index, 'value', value)
                  }),
                  el(TextControl, {
                    label: __('Label', 'archdome-portal'),
                    value: item.label || '',
                    onChange: (value) => update(index, 'label', value)
                  }),
                  el(Button, {
                    variant: 'link',
                    isDestructive: true,
                    onClick: () => setAttributes({ items: items.filter((unused, itemIndex) => itemIndex !== index) })
                  }, __('Remove statistic', 'archdome-portal'))
                ))
              ) : el('p', { className: 'archdome-statistics-editor__empty' }, __('Add a statistic to begin. Each statistic needs a value and a label.', 'archdome-portal')),
              items.length < 8 ? el(Button, {
                variant: 'secondary',
                className: 'archdome-statistics-editor__add',
                onClick: () => setAttributes({ items: [...items, { value: '', label: '', icon: '' }] })
              }, __('Add statistic', 'archdome-portal')) : null
            )
          )
        )
      );
    },
    save: () => null
  });
})();
