# Matcha

Web app' for dating lost souls.


## Subject

Matcha is the second 42's web project.

### Only micro-framework allowed. ORM, validator and users module are forbidden

__Grade: 125 /100 (max)__

*French subject --> cf matcha.fr.pdf*


## Stack

- PHP slim micro-framework
- twig template engine
- twitter bootstrap
- jquery, ...
- mySQL

## Getting Started

Git clone parent repo 'portfolio' and open a terminal. 

Create database and dump some fake users :
```php matcha/app/Config/setup.php```

Start server :
```sh matcha/launchserver.sh```

Finally, browse to the Document root indicated in the console.


## Notes

- Authentication and connexion system:
    - local
    - OAuth protocol for facebook and 42 
- Proper data validation for security concern
- Edit profile
- Browse other user's profile / your own
- Search and filter profiles system
- Likes and Match systems
- Chat available for matched users
- notification system
- history of likes / visits
- block specific user
- Emailing for account validation and forgotten password
- File uploading for pictures
- Responsive design
- MVC design pattern
- POO
- call to APIs for geolocalisation



Il s'agit de concevoir une application permettant à deux potentielles âmes soeurs de se rencontrer, de l’inscription au contact final.

Un utilisateur devra donc pouvoir s’inscrire, se connecter, compléter son profil, parcourir et rechercher d’autres utilisateurs, les liker, et chatter avec ceux qui auront liké en retour.

Technos autorisés:
  - Choix du langage libre.
  - micro-framework (ex: slim pour php, expressjs pour nodejs ...). 
  - librairies externes.

Contraintes:
  - pas d’ORM.
  - pas de validateurs.
  - pas de gestion de comptes utilisateurs.
  - compatible sur Firefox (>= 41) et Chrome (>= 46).
  - présentable sur mobile, et une mise en page acceptable sur de petites résolutions.

Stack :
  - slim micro-framework PHP
  - mySQL PDO
  - twig moteur de templates
  - twitter bootstrap
  - jquery, ...

Ce projet suit le motif d'architecture MVC et la POO. Protocole OAuth avec les api fcbk et 42 pour l'inscription

__Note: 125 /100 (note max)__

*Sujet disponible --> cf matcha.fr.pdf*