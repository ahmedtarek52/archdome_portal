(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/property-search', {
    edit: ({ attributes, setAttributes }) => el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('Search settings', 'archdome-portal') },
        el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) })
      )),
      el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/property-search', attributes }))
    ), save: () => null
  });
})();
