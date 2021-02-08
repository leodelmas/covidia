# Covidia

## 🛠 Pré-requis
* PHP
* MySql
* Composer
* npm

## 📦 Installation

### ⬇️ Récupération du projet
```
git clone https://github.com/leodelmas/covidia.git
cd covidia
```

### ⚙️ Back-end
```
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php -S 127.0.0.1:8000 -t public
```

### 🎨 Front-end
```
npm i
npm run dev-server
```

## 👮‍♂️ Règles commit
Afin que votre commit soit lisible et compréhensible, merci de préfixer votre titre du type de modification :
* [+] : J'ai ajouté des nouveaux éléments
* [*] : J'ai modifié des éléments
* [-] : J'ai supprimé des éléments
