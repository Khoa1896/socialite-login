## Laravel Socialite (Social Login) - Login with google, facebook

## Clone this repo
```
https://github.com/Khoa1896/socialite-login/tree/develop1
```

## Install composer packages
```
$ cd socialite-login
$ composer install
```

## Create and setup .env file
```
make a copy of .env.example and rename to .env
$ copy .env.example .env
$ php artisan key:generate
put database credentials in .env file
```

## Create app for google, facebook
```
For google app
https://console.developers.google.com/
For facebook app
https://developers.facebook.com/apps/
Put all ids and secrets in .env file
```

## Migrate and insert records
```
$ php artisan migrate
```

## Check my website
```
https://socialitelarave2.herokuapp.com/login
```



