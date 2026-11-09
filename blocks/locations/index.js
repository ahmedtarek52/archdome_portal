(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, RangeControl, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/locations', { edit: ({ attributes, setAttributes }) => el(Fragment, {},
    el(InspectorControls, {}, el(PanelBody, { title: __('Section settings', 'archdome-portal') },
      el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
      el(RangeControl, { label: __('Number of locations', 'archdome-portal'), value: attributes.limit, min: 1, max: 24, onChange: (value) => setAttributes({ limit: value }) })
    )), el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/locations', attributes }))
  ), save: () => null });
})();
