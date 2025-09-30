window.addEventListener('load', function () {
  const pre = document.querySelector('.pre-loader');
  if (!pre) {
    console.warn('No se encontró .pre-loader en el DOM');
    return;
  }
  pre.classList.add('hidden');
  const removePre = () => {
    if (pre && pre.parentNode) pre.parentNode.removeChild(pre);
  };
  pre.addEventListener('animationend', removePre, { once: true });
  setTimeout(removePre, 3000);
});
