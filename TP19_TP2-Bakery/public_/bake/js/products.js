const container = document.getElementById('bakes-container');

function loadProducts(category ='') {
    fetch('../api/get_bakes.php${category ? '?category=' + category : ''}')
        .then(response => response.json())
        .then(data => {
            container.innerHTML = '';
            if(data.length === 0) {
                container.innerHTML = '<p>No products found.</p>';
                return;
            }
            data.forEach(bake=> {
                const card = document.createElement('article');
                card.className = 'card product-card';
                card.innerHTML = `
                    <img src="/public_/bake/images/${bake.imageFileName}" class="product-image" alt="${bake.bakeName}">
                    <h4>${bake.bakeName}</h4>
                    <p>${bake.description}</p>
                    <p class="price">£${parseFloat(bake.price).toFixed(2)}</p>
                `;
                container.appendChild(card);
            });
        });
}
loadProducts();
document.querySelectorAll('.category-card').forEach(button => {
    card.addEventListener('click', e => {
        e.preventDefault();
        const url = new URL(card.href);
        const category = url.searchParams.get('category');
        loadProducts(category);
    });
});