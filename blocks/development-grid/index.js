(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, RangeControl, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;

  registerBlockType('archdome/development-grid', {
    edit: ({ attributes, setAttributes }) => el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('Query settings', 'archdome-portal') },
        el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
        el(RangeControl, { label: __('Number of developments', 'archdome-portal'), value: attributes.limit, min: 1, max: 12, onChange: (value) => setAttributes({ limit: value }) }),
        el(TextControl, { label: __('Location term ID', 'archdome-portal'), type: 'number', value: attributes.locationId || '', onChange: (value) => setAttributes({ locationId: Number(value) || 0 }) }),
        el(TextControl, { label: __('Development type term ID', 'archdome-portal'), type: 'number', value: attributes.typeId || '', onChange: (value) => setAttributes({ typeId: Number(value) || 0 }) }),
        el(TextControl, { label: __('Development status term ID', 'archdome-portal'), type: 'number', value: attributes.statusId || '', onChange: (value) => setAttributes({ statusId: Number(value) || 0 }) })
      )),
      el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/development-grid', attributes }))
    ),
    save: () => null
  });
})();
