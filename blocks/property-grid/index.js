(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, RangeControl, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/property-grid', {
    edit: ({ attributes, setAttributes }) => el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('Query settings', 'archdome-portal') },
        el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
        el(RangeControl, { label: __('Number of properties', 'archdome-portal'), value: attributes.limit, min: 1, max: 12, onChange: (value) => setAttributes({ limit: value }) }),
        ...[['developmentId', __('Development ID', 'archdome-portal')], ['locationId', __('Location term ID', 'archdome-portal')], ['typeId', __('Property type term ID', 'archdome-portal')], ['statusId', __('Property status term ID', 'archdome-portal')]].map(([key, label]) => el(TextControl, { key, label, type: 'number', value: attributes[key] || '', onChange: (value) => setAttributes({ [key]: Number(value) || 0 }) }))
      )),
      el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/property-grid', attributes }))
    ), save: () => null
  });
})();
