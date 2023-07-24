<p align="center">
  <a href="https://www.swordfox.nz" target="_blank" rel="noopener noreferrer">
    <img width="300" src="https://www.swordfox.nz/ss-vite.png" alt="Vite Silverstripe logo">
  </a>
</p>
<br/>

# Silverstripe Vite Plugin

[Vite](https://vitejs.dev/) is a modern frontend build tool that provides an extremely fast development environment and bundles your code for production.

This plugin configures Vite for use with Silverstripe.

## Install

To clone and run this application, you'll need [Git](https://git-scm.com) and [Node.js](https://nodejs.org/en/download/) (which comes with [npm](http://npmjs.com)) installed on your computer. From your command line, in the root folder of your project:

1. Install the plugin

```bash
composer require swordfox/vite
```

2. Copy vite yml config

```bash
cp vendor/swordfox/vite/_config/vite.yml.example app/_config/vite.yml
```

Make sure to set the path to images folder of your theme `assetsImageFolder`

You can also use `extra_requirements_css`, `editor_css` as shown in the config, in order to apply custom css to admin environment through Vite

3. Copy vite config

```bash
cp vendor/swordfox/vite/vite.config.js.example vite.config.js
```

You might need to update paths to your assets in vite.config.js, eg.: `themes/custom/src/app.scss`

4. Prepare package.json

We need to set up package.json in our project root folder. Use it for all frontend packages. If you have one, make sure to include npm scripts and dependencies as follows:

~~~js
    "scripts": {
      "dev": "vite",
      "build": "vite build",
      "preview": "vite preview"
    },
    "devDependencies": {
        "laravel-vite-plugin": "^0.7.8",
        "sass": "^1.63.6",
        "vite": "^4.4.0",
        "vite-plugin-static-copy": "^0.17.0"
    },
~~~

Otherwise, run the command to copy one from the source folder to start from

```bash
cp vendor/swordfox/vite/package.json.example package.json
```

5. Finally, run

```bash
npm install
```

6. Add APP_URL to your .env with the local address of your website

```bash
APP_URL=https://localhost
ASSET_URL=https://localhost

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## How To Use

We need to include css in our head tag

~~~html
<head>
  ...
  $Vite.CSS.RAW
</head>
~~~

The same goes with js requirements, but it can be included in body tag instead, at the very bottom

~~~html
<body>
  ...
  $Vite.JS.RAW
</body>
~~~

Excellent! Next, let's get into a groove. For dev environment you need to run

```bash
npm run dev
```

Compiling for Production

```bash
npm run build
```

In order to add assets in your template .ss files you can use these helpers:

~~~html
$Vite.asset('themes/custom/src/images/image.jpg')
~~~

or, shortcut if you set `assetsImageFolder` in your `vite.yml` as mentioned above

~~~html
$Vite.image('image.jpg')
~~~


You can also reach vite helper through SiteConfig

~~~html
$SiteConfig.Vite.image('image.jpg')
~~~


You are all set now, enjoy.
