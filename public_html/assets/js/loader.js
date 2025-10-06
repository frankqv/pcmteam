window.addEventListener('load', function () {
  const pre = document.querySelector('.pre-loader');
  if (!pre) return;
  // Ocultar suavemente sin bloquear render
  requestAnimationFrame(() => pre.classList.add('hidden'));
  const removePre = () => {
    if (pre && pre.parentNode) pre.parentNode.removeChild(pre);
  };
  pre.addEventListener('transitionend', removePre, { once: true });
  setTimeout(removePre, 3000);
});
