const container = document.getElementById('bakes-container');

function loadProducts(category = null) {
    let url = '../api/get_bakes.php';
    if(category) {
        url += `?category=${encodeURIComponent(category)}`;
    }
    fetch(url)
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
                setTimeout(() => {
                   card.classList.add('visible'); 
                }, index * 150);
            });
        });
}

loadProducts();