Photography
===========

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/FabianSchmick/photography/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/FabianSchmick/photography/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/FabianSchmick/photography/badges/build.png?b=master)](https://scrutinizer-ci.com/g/FabianSchmick/photography/build-status/master)

This project is based on the [Symfony PHP Framework](http://symfony.com/) and should be seen as some sort of small template for photograph websites.
Note: you should have experience in web development to adjust the project to your preferences.

You can find first impressions on [fotografie.fabian-schmick.de](http://fotografie.fabian-schmick.de) or watch the gif below

![back-and-frontend](./.github/example.gif "Back- and Frontend view")


## Features

The project provides you following functionality:
- [Symfony 4.4](https://symfony.com/releases/4.4) (LTS version) 
- Responsive layout with the help of [Bootstrap](https://getbootstrap.com/) and [jQuery](https://jquery.com/)
- Small CMS / Backend functionality for creating new entries, tags and tours
- Image compression and thumbnail generation with [LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
- I18n support in English and German
- Tag possibility for describing and filtering entries
- Uploading gpx files for showing your favorite tours with [Leaflet](https://leafletjs.com/) and connect them with your entries 
- [Lazyloading](https://github.com/verlok/lazyload) and Infinityscrolling for images
- Lightbox with [Fancybox](http://fancyapps.com/fancybox/3/)
- Dynamic Sitemap generation
- SEO optimization
- PWA Support with [Workbox](https://developers.google.com/web/tools/workbox/modules/workbox-build)
- Many more 


## Installation

Clone the project
```
git clone https://github.com/FabianSchmick/photography.git
```

Install the dependencies via composer
```
composer install
```

To run the application [setup Symfony](https://symfony.com/doc/4.4/setup.html#running-the-symfony-application) or use [vagrantfile for local development](https://github.com/FabianSchmick/vagrant_skeleton/blob/master/README.md) or use [migraw for local development](https://github.com/marcharding/migraw)

Generate the database schema
```
php bin/console doctrine:migrations:migrate
```

Now you only need to execute `npm install` and `npm run build` in the project root to generate the necessary stylesheet and javascript files.

If you need some example data run:
```
php bin/console doctrine:fixtures:load
```

---

The standard login credentials for the backend `/admin`:
- Username: admin
- Password: admin

If you want to change them you need to encode the password with symfony built in command and replace it's value in `.env.local`
```
php bin/console security:encode-password
```
