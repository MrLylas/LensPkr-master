# 🎬 Documentation technique du projet – LensPkr

## 📌 Présentation générale

**LensPkr** est une plateforme communautaire dédiée aux métiers de l’audiovisuel, développée dans le cadre du **titre professionnel DWWM**. Elle permet aux utilisateurs de créer un profil, publier des projets, rejoindre des équipes et répondre à des offres d'emploi dans le secteur audiovisuel.

> **Technologies principales :** Symfony, PHP, HTML5, CSS3, JavaScript, Doctrine ORM, Bootstrap, Twig, SQL

---

## 🎯 Objectifs fonctionnels

### Pour les utilisateurs :
- 👤 Création d’un profil personnalisé (bio, image, spécialités)
- 🧑‍🤝‍🧑 Création et gestion d’équipes collaboratives
- 🎥 Publication de projets (avec images, vidéos, interactions)
- 🧾 Consultation et publication d’offres d’emploi
- 💬 Système de messagerie privée et de groupe

### Pour les administrateurs :
- 🔐 Accès restreint à un panneau d'administration (`ROLE_ADMIN`)
- 🚫 Modération du contenu (profils, projets, messages)

---

## ⚙️ Stack technique

| Élément        | Technologie ou outil            |
|----------------|---------------------------------|
| Framework      | Symfony 6+                      |
| Langages       | PHP 8+, HTML5, CSS3, JS         |
| ORM            | Doctrine ORM                    |
| Base de données| MySQL via HeidiSQL              |
| Interface      | Twig + Bootstrap 5              |
| IDE            | VSCode                          |
| Serveur local  | Laragon                         |
| Versioning     | Git + GitHub                    |

---

## 🔐 Sécurité

- 🔑 Hachage des mots de passe (Bcrypt)
- 📧 Vérification d’e-mail à l'inscription
- 🧠 Gestion des rôles (`ROLE_USER`, `ROLE_ADMIN`)
- 🕵️‍♂️ Protection contre :
  - Attaques XSS via Twig (échappement automatique)
  - Injections SQL via Doctrine
  - Uploads malveillants (types MIME, taille, renommage)
- 🔐 Accès restreint aux zones sensibles via `security.yaml`

---

## 📬 RGPD & respect des données

- ✅ Consentement explicite à l’inscription
- 🔒 Données minimales collectées (nom, e-mail, localisation)
- 🧹 Données supprimables à tout moment
- 📧 Contact RGPD : LensPkr@gmail.com

---

## ♿ Accessibilité

- 🧭 Navigation intuitive via menu burger
- 🔤 Texte alternatif sur toutes les images (`alt`)
- 🖼️ Contrastes suffisants pour lecture
- ⌨️ Navigation clavier
- 📱 Responsive design avec breakpoints et media queries

---

## 🚀 SEO et bonnes pratiques HTML

- 📄 Utilisation correcte des balises `title`, `meta`, `h1` à `h6`
- 🕸️ URL sémantiques
- 🧠 Description enrichie (balise `meta description`)
- 🧪 Audit avec Lighthouse

---

## 🧱 Architecture et design pattern

### 🧩 Pattern MVC (Modèle-Vue-Contrôleur)
- **Modèle :** Entités Doctrine + Repositories
- **Vue :** Templates Twig
- **Contrôleur :** Symfony Controllers

### 🗂️ Organisation en couches :
```
/src
  /Controller
  /Entity
  /Repository
  /Form
/templates
/public
/config
```

---

## 🗃️ Modélisation des données

10 entités principales :
- `User`, `Team`, `Project`, `Job`, `Skill`, `Speciality`, `Level`, `Message`, `Image`, `ContractType`
- Tables associatives : `UserSkill`, `Ask`, `ProjectImage`

Exemple de relation :
```php
// Un utilisateur peut créer plusieurs projets
@OneToMany(targetEntity="Project", mappedBy="user")
private $projects;
```

---

## 📄 Fonctionnalités principales

- ✅ Création de profils avec pseudo, bio, compétences
- 🧑‍🤝‍🧑 Création d’équipes et gestion de membres
- 📣 Publication de projets avec like/commentaires
- 🧾 Publication d’offres d'emploi avec réponse directe
- 💬 Messagerie privée et d’équipe
- 🖼️ Upload d’images (JPEG, PNG, WebP)

---

## 🧪 Tests

- ✔️ Tests manuels des parcours utilisateurs
- 🔍 Vérification fonctionnelle des formulaires et filtres
- 🔐 Tests de sécurité : accès, injection, XSS

---

## 🔧 Développement et environnement

- 💻 VSCode avec extensions PHP et Symfony
- 🐘 Symfony CLI
- 🔄 Git pour le versioning
- 🔬 Composer pour les dépendances
- 🛠️ Laragon pour serveur local
- 🗃️ HeidiSQL pour la base de données

---

## 🔜 Axes d’amélioration

- 💬 Mise en place de messagerie instantanée avec Mercure
- 📎 Upload de fichiers PDF pour les candidatures
- 🌓 Mode clair/sombre
- 🎯 Optimisation UX/UI pour la règle des 3 clics

---

## 🙏 Remerciements

Merci à mes formateurs et à mes camarades pour leur aide et leur soutien. Ce projet a été un véritable terrain d'apprentissage et m'a permis de consolider mes compétences en développement web.

---


---

## 🧪 Installation du projet en local

### Prérequis

- PHP ≥ 8.1
- Composer
- Symfony CLI
- MySQL / MariaDB
- Laragon ou autre environnement de développement local
- Navigateur web moderne

### Étapes

1. **Cloner le dépôt**
```bash
git clone https://github.com/ton-utilisateur/lenspkr.git
cd lenspkr
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
Créer un fichier `.env.local` à partir du fichier `.env` et modifier la ligne :
```
DATABASE_URL="mysql://username:password@127.0.0.1:3306/lenspkr"
```

4. **Créer la base et exécuter les migrations**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. **Lancer le serveur Symfony**
```bash
symfony server:start
```

---

## 🚀 Stratégie de déploiement (futur)

Le projet n’est pas encore déployé en production mais a été conçu pour l’être facilement.

### Possibilités de déploiement envisagées :

- **Serveur mutualisé (type OVH, o2switch)** :
  - Déploiement via FTP
  - Configuration de la base de données distante
  - Adaptation du `.env.local`

- **Hébergement cloud (type Render, Railway, Heroku)** :
  - Déploiement Git automatisé
  - Configuration de variables d’environnement
  - Gestion des migrations et du cache en post-deploy

- **CI/CD via GitHub Actions** (planifié) :
  - Tests automatiques à chaque push
  - Build Symfony + migration + upload vers serveur

### Points clés pour le futur déploiement :
- Configuration HTTPS
- Optimisation des performances (cache HTTP, CSS/JS minifiés)
- Monitoring et logs d'erreurs
- Sauvegardes régulières de la base de données
