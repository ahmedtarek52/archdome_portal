(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, TextControl, TextareaControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/map-location', { edit: ({ attributes, setAttributes, context }) => el(Fragment, {},
    el(InspectorControls, {}, el(PanelBody, { title: __('Location settings', 'archdome-portal') },
      el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
      el(TextareaControl, { label: __('Address or directions', 'archdome-portal'), value: attributes.address, onChange: (value) => setAttributes({ address: value }) })
    )), el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/map-location', attributes, urlQueryArgs: { post_id: context.postId || 0 } }))
  ), save: () => null });
})();
