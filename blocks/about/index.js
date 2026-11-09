(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, MediaUpload, MediaUploadCheck, RichText, useBlockProps } = wp.blockEditor;
  const { Button, PanelBody, TextControl, TextareaControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;

  registerBlockType('archdome/about', {
    edit: ({ attributes, setAttributes }) => el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('About settings', 'archdome-portal') },
        el(TextControl, { label: __('Link label', 'archdome-portal'), value: attributes.linkLabel, onChange: (value) => setAttributes({ linkLabel: value }) }),
        el(TextControl, { label: __('Link URL', 'archdome-portal'), type: 'url', value: attributes.linkUrl, onChange: (value) => setAttributes({ linkUrl: value }) })
      )),
      el('section', useBlockProps({ className: 'archdome-about-editor' }),
        el(RichText, { tagName: 'p', placeholder: __('Optional eyebrow', 'archdome-portal'), value: attributes.eyebrow, allowedFormats: [], onChange: (value) => setAttributes({ eyebrow: value }) }),
        el(RichText, { tagName: 'h2', placeholder: __('About heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
        el(TextareaControl, { label: __('About text', 'archdome-portal'), value: attributes.text, onChange: (value) => setAttributes({ text: value }) }),
        el(MediaUploadCheck, {}, el(MediaUpload, { allowedTypes: ['image'], value: attributes.mediaId, onSelect: (media) => setAttributes({ mediaId: media.id }), render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, attributes.mediaId ? __('Replace image', 'archdome-portal') : __('Choose image', 'archdome-portal')) })),
        attributes.mediaId ? el(Button, { variant: 'link', isDestructive: true, onClick: () => setAttributes({ mediaId: 0 }) }, __('Remove image', 'archdome-portal')) : null
      )
    ),
    save: () => null
  });
})();
