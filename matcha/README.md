# Matcha

Web app' for dating lost souls.


## Subject

Matcha is the second 42's web project.

### Only micro-framework allowed. ORM, validator and users module are forbidden.

__Grade: 125 /100 (max)__

*French subject --> cf matcha.fr.pdf*


## Stack

- PHP slim micro-framework
- twig template engine
- twitter bootstrap
- jquery, ...
- mySQL PDO
- API calls

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
    - OAuth protocol for facebook and 42 sign up
- Proper data validation for security concern
- Edit profile
- Geolocalisation
- Browse other user's profile / your own
- Search and filter profiles system
- Likes and Match systems
- Real-time chat available for matched users
- notification system
- flash message system
- history of likes / visits
- block specific user
- Emailing for account validation and forgotten password
- File uploading for pictures
- Responsive design
- MVC design pattern
- POO
- Firefox and Chrome support