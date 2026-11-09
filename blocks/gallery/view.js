(() => {
  'use strict';

  const init = () => {
    document.querySelectorAll('.archdome-gallery__play').forEach((button) => {
      const video = button.parentElement.querySelector('video');
      if (!video) {
        return;
      }
      button.addEventListener('click', () => {
        const play = video.play();
        if (play && typeof play.catch === 'function') {
          play.catch(() => { button.hidden = false; });
        }
      });
      video.addEventListener('play', () => { button.hidden = true; });
      video.addEventListener('pause', () => { button.hidden = false; });
      video.addEventListener('ended', () => { button.hidden = false; });
    });
  };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
