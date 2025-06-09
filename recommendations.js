document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('recommendationForm');
    const recommendationsContainer = document.getElementById('recommendations');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        recommendationsContainer.innerHTML = '<p><em>Se încarcă recomandările...</em></p>';

        const formData = new FormData(form);

        fetch('get_recommendations.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    displayRecommendations(data.recommendations);
                    if (data.note) {
                        alert(data.note);
                    }
                } else {
                    recommendationsContainer.innerHTML = `<p style="color: darkred;"><strong>Eroare:</strong> ${data.message}</p>`;
                }
            })
            .catch(err => {
                console.error('Eroare:', err);
                recommendationsContainer.innerHTML = '<p style="color: red;">A apărut o problemă la generarea recomandărilor.</p>';
            });

    });

    function displayRecommendations(recommendations) {
        recommendationsContainer.innerHTML = '';
        if (!recommendations.length) {
            recommendationsContainer.innerHTML = '<p>Nu există recomandări pentru selecția ta.</p>';
            return;
        }

        recommendations.forEach(rec => {
            const div = document.createElement('div');
            div.className = 'recommendation-item';
            div.innerHTML = `
                <h3>${rec.title}</h3>
                <img src="${rec.image_url}" alt="${rec.title}">
                <p>${rec.description}</p>
                <p class="details">
                    <strong>Ocazie:</strong> ${rec.type}<br>
                    <strong>Preț:</strong> ${rec.priceRange}<br>
                    ${rec.brand ? `<strong>Brand:</strong> ${rec.brand}` : ''}
                </p>
            `;
            recommendationsContainer.appendChild(div);
        });
    }
});