document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('recommendationForm');
    const recommendationsContainer = document.getElementById('recommendations');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        recommendationsContainer.innerHTML = '<p><em>Se încarcă recomandările...</em></p>';

        fetch('get_recommendations.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayRecommendations(data.recommendations);
            } else {
                recommendationsContainer.innerHTML = `<p style="color: darkred;"><strong>Eroare:</strong> ${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Eroare la fetch:', error);
            recommendationsContainer.innerHTML = '<p style="color: darkred;"><strong>Eroare:</strong> A apărut o problemă la trimiterea cererii.</p>';
        });
    });

    function displayRecommendations(recommendations) {
        recommendationsContainer.innerHTML = '';

        if (!recommendations || recommendations.length === 0) {
            recommendationsContainer.innerHTML = '<p>Nu s-au găsit rezultate pentru selecția aleasă.</p>';
            return;
        }

        recommendations.forEach(rec => {
            const recElement = document.createElement('div');
            recElement.className = 'recommendation-item';

            recElement.innerHTML = `
                <h3>${rec.title}</h3>
                <img src="${rec.image_url}" alt="${rec.title}" />
                <p>${rec.description}</p>
                <p class="details">
                    <strong>Ocazie:</strong> ${rec.type}<br>
                    <strong>Preț:</strong> ${rec.priceRange}<br>
                    ${rec.brand ? `<strong>Brand:</strong> ${rec.brand}` : ''}
                </p>
            `;

            recommendationsContainer.appendChild(recElement);
        });
    }
});
