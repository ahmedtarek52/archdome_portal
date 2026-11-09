(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { MediaUpload, MediaUploadCheck, RichText, useBlockProps } = wp.blockEditor;
  const { Button } = wp.components;
  const { createElement: el } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/master-plan', { edit: ({ attributes, setAttributes }) => el('section', useBlockProps({ className: 'archdome-plan-editor' }),
    el(RichText, { tagName: 'h2', placeholder: __('Plan heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
    el('p', {}, attributes.mediaId ? __('A custom plan is selected.', 'archdome-portal') : __('The current record plan will be used when available.', 'archdome-portal')),
    el(MediaUploadCheck, {}, el(MediaUpload, { allowedTypes: ['image'], value: attributes.mediaId, onSelect: (media) => setAttributes({ mediaId: media.id }), render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, __('Choose plan image', 'archdome-portal')) }))
  ), save: () => null });
})();
