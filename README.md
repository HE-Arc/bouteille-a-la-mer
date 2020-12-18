# Bouteille à la mer
Bouteille à la mer est un site qui permet la création de conversations localisées. Lors de la création d'une conversation, on peut décider de sa durée (max 23h59). On peut participer aux conversations qui sont à moins de 30km. Le système de message est en temps réel et des images peuvent être envoyées. La création de compte n'est pas obligatoire pour la création de conversations et l'envoie de messages.

# Installation
```
git clone https://github.com/HE-Arc/bouteille-a-la-mer.git
```

Créer le fichier `.env` en reprenant le template `.env.example` et le configurer

modifier la variable `QUEUE_CONNECTION` dans le fichier `.env`

```
QUEUE_CONNECTION=database
```

```
php composer install
```

```
php artisan migrate
```

Lancement du serveur websocket
```
php artisan websocket:init
```

Lancement du manager de suppression automatique après un certain temps

```
php artisan queue:listen
```

Lancer le serveur
```
php artisan serve
```
