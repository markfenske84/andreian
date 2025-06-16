# Webfor

A WordPress boilerplate theme for Webfor.

## Built With

* [NPM](https://www.npmjs.com/get-npm) - Package Manager
* [Gulp.js](https://gulpjs.com/) - Workflow Automation

## Getting Started

This README.md should be overwritten for the project you are using this theme for. Use this [template](https://gist.github.com/PurpleBooth/109311bb0361f32d87a2) as a guide for the README.md you create. Changes to the theme should be logged in CHANGELOG.md. Use this [format](https://keepachangelog.com/en/1.0.0/) when writing in the changelog.

This theme requires [Advanced Custom Fields](https://www.webfordev.com/plugins/advanced-custom-fields-pro.zip) to be an active plugin on the WordPress installation. This [JSON file](https://www.webfordev.com/acf-json/acf-export.json) should be downloaded and imported to your installation.

## Installation

You will need to install [Node.js](https://nodejs.org/en/) in order to use NPM to install packages for your project. Once installed, change directories to the root folder of the theme and run in your terminal the following:

```bash
npm install
npm start
```

This will automate the tasks found in gulpfile.js.

To use the BrowserSync functionality, after running the 'npm install' command listed above, open up the [gulpconfig.json](https://bitbucket.org/webfors/wp-webfor-theme/src/master/gulpconfig.json) file and update the value for the key "proxy" on Line 3 to the local domain of your site and save the file.  Then use command:

```
gulp watch-bs
```

This will run all the same processes as 'npm start', while also automatically reloading the browser for you when changes in files are detected.  Note: the [gulp-cli](https://www.npmjs.com/package/gulp-cli) will need to be installed globally on your machine first to use the gulp command in your terminal.  If this is not installed, it can be done so with this command:

```
npm install --global gulp-cli
```

## Directory Structure

- `assets/`: static content like images, video, audio, fonts etc.

- `src/`: "source" files to build and develop the project. This is where the original source files are located, before being compiled into fewer files to `dist/`. Not all source code is pushed to the production server.

- `dist/`: "distribution", the compiled code/library. The files meant for production are located here.

- `src/templates/`: WordPress template files + template partials should be added here.

## Bitbucket Pipelines

Bitbucket Pipelines are used to automate build processes and to write to servers that contain the staging/production files for a website.

Variables must be set within the Bitbucket repo in order for this to work (Settings -> Deployments). You will add the user, host, port, and folder of the server you wish to write to. Reference the bitbucket-pipelines.yml file in the root theme to match the syntax of the variables. 

You will also have to set up an SSH key for the server you wish to connect to (Settings -> SSH keys). 

## Resources

[SCSS Guidelines](https://sass-guidelin.es/)