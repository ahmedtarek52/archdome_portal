(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, RangeControl, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/featured-properties', {
    edit: ({ attributes, setAttributes }) => el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('Section settings', 'archdome-portal') },
        el(TextControl, { label: __('Heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
        el(RangeControl, { label: __('Number of properties', 'archdome-portal'), value: attributes.limit, min: 1, max: 6, onChange: (value) => setAttributes({ limit: value }) }),
        el(TextControl, { label: __('Archive link label', 'archdome-portal'), value: attributes.linkLabel, onChange: (value) => setAttributes({ linkLabel: value }) }),
        el(TextControl, { label: __('Archive link URL', 'archdome-portal'), type: 'url', value: attributes.linkUrl, onChange: (value) => setAttributes({ linkUrl: value }) })
      )),
      el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/featured-properties', attributes }))
    ), save: () => null
  });
})();
