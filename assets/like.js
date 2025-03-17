
// Lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', function () {
    // Ajouter un gestionnaire d'événements pour les boutons de like
    document.querySelectorAll('.like-btn').forEach(button => {
        // Ajouter un gestionnaire d'événements pour le clic
        button.addEventListener('click', function() {
            // Récupérer l'ID du projet et le token
            let id = this.dataset.id;
            let token = this.dataset.token;
            let likes = this.querySelector('.like-count');
            // Récupérer l'icone
            let icon = this.querySelector('.like-icon');
            // Envoyer la requete à /projet/{id}/like en methode POST
            fetch(`/project/${id}/like`, {
                method: 'POST',
                headers: {
                    // On indique que c'est une requete ajax
                    'X-Requested-With': 'XMLHttpRequest',
                    // On indique le type de contenu
                    'Content-Type': 'application/json'
                },
                // On envoie le token
                body: JSON.stringify({ _token: token })
            })
            .then(response => {
                // Si la requete a echoué
                if (!response.ok) {
                    // On affiche un message d'erreur
                    throw new Error('Une erreur est survenue');
                }
                // Sinon
                return response.json();
            })
            // On recupere les donnees de la response
            .then(data => {
                // Si les donnees contiennent les likes
                if (data.likes !== undefined) { 
                    // On met a jour le nombre de likes
                    // this.querySelector('.like-count').textContent = data.likes;
                    likes.textContent = data.likes;
                    // On met a jour l'icone
                    this.classList.toggle('liked');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
            });
        });
    });
});
