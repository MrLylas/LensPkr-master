
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
            // let timeStamp = new Date().getTime();
            var heartIconUrl = window.heartIconUrl;  // L'URL des images SVG
            var filledHeartIconUrl = window.filledHeartIconUrl;

            console.log(icon);
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
                console.log(data);
                // Si les donnees contiennent les likes
                if (data.likes !== undefined) { 

                    // On met a jour le nombre de likes
                    likes.textContent = data.likes;
                    // Si le bouton est like
                    if (data.liked) {
                    this.classList.add('liked');
                    icon.classList.add('filled');
                    icon.src = filledHeartIconUrl;
                        // On ajoute le like au localStorage
                    localStorage.setItem(`likes-${id}`, 'liked');
                    localStorage.setItem(`likes-${id}`, 'filled');
                    }else{
                        // Si le bouton n'est pas like
                        this.classList.remove('liked');
                        icon.classList.remove('filled');
                        icon.src = heartIconUrl;
                            // On retire le like au localStorage
                        localStorage.removeItem(`likes-${id}`);
                    }
                }
            })
            .catch(error => {
                console.error('Erreur AJAX:', error);
            });
        });
        // On recupere l'ID du projet via l'attribut data-id du bouton
        let id = button.dataset.id;
        // On recupere les likes stocké dans le localStorage
        let storedLikes = localStorage.getItem(`likes-${id}`);
        console.log(storedLikes);
        // Si le bouton est like
        if (storedLikes === 'liked'){
            // On ajoute la classe liked au bouton
            button.classList.add('liked');
            // On ajoute la classe filled au bouton
            button.querySelector('.like-icon').classList.add('filled');
        }
    });
});
