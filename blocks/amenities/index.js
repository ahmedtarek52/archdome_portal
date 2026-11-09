(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/amenities', { edit: ({ attributes, setAttributes, context }) => el(Fragment, {},
    el(InspectorControls, {}, el(PanelBody, { title: __('Amenities settings', 'archdome-portal') },
      el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
      el(TextControl, { label: __('Amenity term IDs', 'archdome-portal'), help: __('Comma-separated. Leave empty to use the current post.', 'archdome-portal'), value: (attributes.termIds || []).join(', '), onChange: (value) => setAttributes({ termIds: value.split(',').map((id) => Number(id.trim())).filter(Boolean).slice(0, 30) }) })
    )), el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/amenities', attributes, urlQueryArgs: { post_id: context.postId || 0 } }))
  ), save: () => null });
})();
