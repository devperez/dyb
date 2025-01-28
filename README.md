
# TeleSlack

Cette application permet de poster des messages à la fois sur Slack et sur Telegram. Les messages peuvent être planifiés ou bien envoyés directement.




## Installation

Une fois le projet récupéré, il est nécessaire de passer par plusieurs étapes pour pouvoir le faire fonctionner.
Ouvrez un terminal à la racine du projet.

On compile le front :

```bash
npm run dev
```
Puis on lance le serveur de développement:

```bash
php artisan serve
```
Ensuite on fait les migrations de la base de données MariaDB:

```bash
php artisan migrate
```
Pour envoyer les messages qui seront placés en file d'attente, on lance :
```bash
php artisan schedule:work
```
Enfin, pour exécuter les jobs à envoyer :
```bash
php artisan queue:listen
```
## Configuration

Lorsque l'application s'ouvre pour la première fois, cliquez en haut à droite sur Register pour créer votre compte.
Une fois connecté, dans le menu configuration vous pourrez entrer le webhook de Slack et / ou votre configuration pour Telegram.
Pour le channel id de Telegram, il s'agit de la dernière partie de l'url du channel qu'il faut précéder d'un arobase.
Par exemple, si l'url de votre channel est : https://t.me/testapp, alors votre channel id sera @testapp.
