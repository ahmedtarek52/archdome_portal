(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, RangeControl, TextControl, ToggleControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;

  registerBlockType('archdome/insights', {
    edit: ({ attributes, setAttributes }) => el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('Query settings', 'archdome-portal') },
        el(TextControl, { label: __('Section heading', 'archdome-portal'), value: attributes.heading, onChange: (value) => setAttributes({ heading: value }) }),
        el(RangeControl, { label: __('Number of insights', 'archdome-portal'), value: attributes.limit, min: 1, max: 12, onChange: (value) => setAttributes({ limit: value }) }),
        el(TextControl, { label: __('Category ID (optional)', 'archdome-portal'), type: 'number', value: attributes.categoryId || '', onChange: (value) => setAttributes({ categoryId: Number(value) || 0 }) }),
        el(ToggleControl, { label: __('Show excerpts', 'archdome-portal'), checked: attributes.showExcerpt, onChange: (value) => setAttributes({ showExcerpt: value }) })
      )),
      el('div', useBlockProps(), el(wp.serverSideRender, { block: 'archdome/insights', attributes }))
    ),
    save: () => null
  });
})();
