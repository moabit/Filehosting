# Filehosting
Simple Slim Framework File Sharing App

##Features
1. Upload, download (via PHP or mod_xsend) or delete files
2. Hierarchical AJAX comments
3. Search based on Sphinx search engine

##Used technologies

1. Slim Framework
2. PostgreSQL
3. Eloquent ORM
4. Sphinx Search
5. Twig Template Engine
6. AJAX
7. Bootstrap
8. Videojs player

##Requirements

1. PHP 7+ with mbstring, PDO and intl extensions
2. Sphinx Search
3. Composer
4. PostgreSQL 

##Installation

1. Clone the project with `clone https://github.com/moabit/filehosting.git`
2. Run `composer install` to install dependencies
3. Run `install.php` in the command line to install front-end dependencies
4. Set `public` as root directory on your web server
5. Import `filehosting.sql`
6. Edit `config.json` and `sphinx.conf`
