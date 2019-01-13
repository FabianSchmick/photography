Photography
===========

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/FabianSchmick/photography/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/FabianSchmick/photography/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/FabianSchmick/photography/badges/build.png?b=master)](https://scrutinizer-ci.com/g/FabianSchmick/photography/build-status/master)

This project is based on the [Symfony PHP Framework](http://symfony.com/) and should be seen as some sort of small template for photographs.
You should have experience in web development to adjust the project to your preferences.

Here is an example of how an end result could look: [fotografie.fabian-schmick.de](http://fotografie.fabian-schmick.de)

![back-and-frontend](./github/example.gif "Back- and Frontend view")


## Features

The project provides you following functionality:
- [Symfony 3.4](https://symfony.com/) (LTS version) 
- Basic responsive layout with [Bootstrap](https://getbootstrap.com/) and [jQuery](https://jquery.com/)
- Small CMS / Backend for creating new entries, tags and tours
- Image compression and thumbnail generation with [LiipImagineBundle](https://github.com/liip/LiipImagineBundle)
- I18n support in English and German
- Tag possibility for describing and filtering entries
- Upload gpx files for showing your favorite tours with [Leaflet](https://leafletjs.com/) and connect them with your entries 
- Lazyloading / Infinityscrolling for images
- Justified Gallery with [JustifiedGallery](http://miromannino.github.io/Justified-Gallery/)
- Lightbox with [Fancybox](http://fancyapps.com/fancybox/3/)
- Dynamic Sitemap generation
- SEO optimized and partly a PWA with [Workbox](https://developers.google.com/web/tools/workbox/modules/workbox-build)
- And many more 


## Installation

Clone the project
```
git clone https://github.com/FabianSchmick/photography.git
```

Install the dependencies via composer
```
composer install
```

To start the application [Setup Symfony](https://symfony.com/doc/3.4/setup.html#running-the-symfony-application) or use [vagrantfile for local development](https://github.com/FabianSchmick/vagrant_skeleton/blob/master/README.md) or use [migraw for local development](https://github.com/marcharding/migraw)

Generate the database schema
```
php bin/console doctrine:migrations:migrate
```

**Backend password:**

Choose a secure password for the backend `/admin` and write it in `app/config/security.yml`
after you encoded the password with symfony built in command
```
php bin/console security:encode-password
```

Now you only need to run `npm install` and `gulp deploy` in the project root to generate the stylesheet and javascript files.

If you need some example data run:
```
php bin/console doctrine:fixtures:load
```
