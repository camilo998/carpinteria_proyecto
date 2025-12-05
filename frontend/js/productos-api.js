const API_URL = "http://localhost:3000/api/productos/";

fetch(API_URL)
  .then(response => response.json())
  .then(responseData => {
    const container = document.getElementById('productos-container');
    container.innerHTML = '';

    responseData.daticos.forEach(producto => {
      const productoDiv = document.createElement('div');
      productoDiv.className = 'col';

      productoDiv.innerHTML = `
        <div class="card h-100">
          <img src="${producto.imagen || 'img/default.jpg'}" class="card-img-top" alt="${producto.nombre}">
          <div class="card-body">
            <h5 class="card-title">${producto.nombre}</h5>
            <p class="card-text">${producto.descripcion}</p>
            <p class="card-text"><strong>Precio: $${producto.precio}</strong></p>
            <button class="btn btn-primary" onclick="mostrarFormularioCompra(${producto.id})">Comprar</button>
          </div>
        </div>
      `;

      container.appendChild(productoDiv);
    });
  })
  .catch(error => console.error('Error fetching products:', error));

function mostrarFormularioCompra(productoId) {
  const formulario = document.getElementById('formulario-compra');
  formulario.style.display = 'block';
}
