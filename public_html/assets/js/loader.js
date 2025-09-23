window.addEventListener('load', function () {
  const pre = document.querySelector('.pre-loader');
  if (!pre) {
    console.warn('No se encontró .pre-loader en el DOM');
    return;
  }  // Añade la clase (más seguro que manipular className manualmente)
  pre.classList.add('hidden');
  // Quitar el elemento una vez termine la animación para no bloquear interacción
  pre.addEventListener('animationend', () => pre.remove(), { once: true });
});
